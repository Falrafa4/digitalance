<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqClient
{
    /**
     * Call the Groq Chat Completion API with automatic fallback models.
     *
     * @param string $systemPrompt
     * @param string $userPrompt
     * @param array $options
     * @return string|null
     */
    public static function generate(string $systemPrompt, string $userPrompt, array $options = []): ?string
    {
        $apiKey = config('services.groq.key');

        if (empty($apiKey)) {
            Log::warning('Groq API Key is not set. Falling back to local offline engine.');
            return null;
        }

        $primaryModel = config('services.groq.model', 'openai/gpt-oss-20b');
        $candidateModels = array_values(array_unique([
            $primaryModel,
            'openai/gpt-oss-20b',
            'qwen/qwen3.8-27b',
            'openai/gpt-oss-120b',
        ]));

        $temperature = $options['temperature'] ?? 0.1;
        $maxTokens = $options['max_tokens'] ?? 1500;

        foreach ($candidateModels as $model) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(20)->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt]
                    ],
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $content = $data['choices'][0]['message']['content'] ?? null;
                    if (!empty($content)) {
                        return $content;
                    }
                }

                Log::warning("Groq API call with model [{$model}] failed (HTTP {$response->status()}): " . $response->body());
            } catch (\Throwable $e) {
                Log::warning("Exception during Groq API call with model [{$model}]: " . $e->getMessage());
            }
        }

        Log::error('All Groq model attempts exhausted. Falling back to local offline recommendation engine.');
        return null;
    }

    /**
     * Local Offline Fallback for Client Freelancer Recommendation.
     * Computes similarity scores using category matching, technical skill token overlaps, and reputation.
     */
    public static function getLocalRecommendationFallback(array $freelancers, ?array $loker = null, ?string $customQuery = null): array
    {
        $queryText = '';
        $categoryId = null;

        if ($loker) {
            $queryText = ($loker['title'] ?? '') . ' ' . ($loker['description'] ?? '');
            $categoryId = $loker['category_id'] ?? null;
        } elseif ($customQuery) {
            $queryText = $customQuery;
        }

        $queryWords = self::tokenize($queryText);
        $results = [];

        foreach ($freelancers as $f) {
            $scoreSkills = 0;
            $scoreCategory = 0;

            // 1. Category Match (25%)
            if ($categoryId) {
                $hasCategoryService = collect($f->services)->contains('category_id', $categoryId);
                $scoreCategory = $hasCategoryService ? 100 : 30;
            } else {
                $matchedCategories = 0;
                foreach ($f->services as $service) {
                    $catName = strtolower($service->category->name ?? '');
                    foreach ($queryWords as $word) {
                        if (str_contains($catName, $word)) {
                            $matchedCategories++;
                            break;
                        }
                    }
                }
                $scoreCategory = $matchedCategories > 0 ? 100 : 35;
            }

            // 2. Skill Text Match (50%)
            // Separate portfolio, service, and bio tokens for weighted precision
            $portfolioText = '';
            foreach ($f->portofolios as $portfolio) {
                $portfolioText .= ($portfolio->title ?? '') . ' ' . ($portfolio->description ?? '') . ' ';
            }
            $portfolioWords = self::tokenize($portfolioText);

            $serviceText = '';
            foreach ($f->services as $service) {
                $serviceText .= ($service->title ?? '') . ' ' . ($service->description ?? '') . ' ';
            }
            $serviceWords = self::tokenize($serviceText);

            $bioText = ($f->bio ?? '') . ' ';
            if ($f->skomda_student) {
                $bioText .= ($f->skomda_student->major ?? '') . ' ' . ($f->skomda_student->class ?? '');
            }
            $bioWords = self::tokenize($bioText);

            $portfolioMatches = array_intersect($queryWords, $portfolioWords);
            $serviceMatches = array_intersect($queryWords, $serviceWords);
            $bioMatches = array_intersect($queryWords, $bioWords);

            $allMatchedWords = array_values(array_unique(array_merge($portfolioMatches, $serviceMatches, $bioMatches)));

            if (count($queryWords) > 0) {
                if (count($allMatchedWords) > 0) {
                    // Match found: weight portfolio highest, then services, then bio
                    $scoreSkills = min(100, 30 + (count($portfolioMatches) * 35) + (count($serviceMatches) * 25) + (count($bioMatches) * 15));
                } else {
                    // No skill/keyword match found at all
                    $scoreSkills = 20;
                }
            } else {
                $scoreSkills = 50; // default medium match when query is empty
            }

            // 3. Performance Metric (25%)
            $avgRating = \App\Models\Review::whereHas('order.service', function ($q) use ($f) {
                $q->where('freelancer_id', $f->id);
            })->avg('rating') ?? 4.0;
            $scoreRating = min(100, $avgRating * 20);

            $completedOrdersCount = \App\Models\Order::where('freelancer_id', $f->id)
                ->where('status', 'Completed')
                ->count();
            $scoreOrders = min(100, $completedOrdersCount * 25);

            $scoreVerified = $f->status === 'Approved' ? 100 : 30;
            $scorePerformance = ($scoreRating * 0.5) + ($scoreOrders * 0.3) + ($scoreVerified * 0.2);

            // Calculate final weighted score (Skills 50%, Category 25%, Performance 25%)
            $finalScore = (int) round(($scoreSkills * 0.50) + ($scoreCategory * 0.25) + ($scorePerformance * 0.25));
            $finalScore = max(0, min(100, $finalScore));

            // Generate professional analysis text without stopwords
            $majorName = $f->skomda_student->major ?? 'IT';
            if (!empty($allMatchedWords)) {
                $matchedSkillsStr = implode(', ', array_slice($allMatchedWords, 0, 4));
                $analysis = "Memiliki keahlian dan portofolio relevan pada: [{$matchedSkillsStr}]. ";
            } else {
                $analysis = "Profil belum secara spesifik mencantumkan kata kunci keahlian yang dicari. ";
            }

            $analysis .= "Siswa jurusan {$majorName}";
            if ($completedOrdersCount > 0) {
                $analysis .= " dengan rekam jejak {$completedOrdersCount} proyek sukses (rating " . number_format($avgRating, 1) . "★).";
            } else {
                $analysis .= " yang siap mengerjakan proyek perdana dengan pendampingan.";
            }

            $results[] = [
                'freelancer_id' => $f->id,
                'score' => $finalScore,
                'analysis' => $analysis,
                'breakdown' => [
                    'skills' => (int) round($scoreSkills),
                    'category' => (int) round($scoreCategory),
                    'performance' => (int) round($scorePerformance)
                ]
            ];
        }

        // Sort descending by match score
        usort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $results;
    }

    /**
     * Local Offline Fallback for Freelancer Career Mapping.
     */
    public static function getLocalCareerMappingFallback(array $profileData): array
    {
        $major = strtoupper($profileData['major'] ?? 'SIJA');
        $completedCount = $profileData['completed_count'] ?? 0;
        
        $xp = 100 + ($completedCount * 100);
        $level = 'Junior Specialist';
        $xpToNext = 300 - $xp;

        if ($xp > 300) {
            $level = 'Associate Specialist';
            $xpToNext = 1000 - $xp;
        }
        if ($xp > 1000) {
            $level = 'Senior Master';
            $xpToNext = 0;
        }

        $track = '';
        $milestones = [];
        $skillGaps = [];
        $learningRoadmap = '';
        $lokerCriteria = '';

        if ($major === 'SIJA') {
            $track = 'Web & System Integration Engineer';
            $learningRoadmap = 'Fokus pada pemrograman modern (Laravel, Node.js), pemahaman API, basis data relasional, dan deployment dasar menggunakan Docker/VPS.';
            $lokerCriteria = 'Web Development, IT Support, IoT';

            $milestones = [
                ['title' => 'Menguasai HTML, CSS & Basic JS', 'status' => 'Completed', 'description' => 'Memahami dasar kerangka visual web.'],
                ['title' => 'Implementasi Framework Backend (Laravel)', 'status' => $completedCount >= 1 ? 'Completed' : 'Pending', 'description' => 'Membangun fungsionalitas server-side dan database.'],
                ['title' => 'Pembuatan RESTful API & Keamanan', 'status' => $completedCount >= 3 ? 'Completed' : 'Pending', 'description' => 'Integrasi antar sistem yang aman.'],
                ['title' => 'DevOps Dasar & Cloud Deployment', 'status' => $completedCount >= 8 ? 'Completed' : 'Pending', 'description' => 'Deployment aplikasi mandiri menggunakan server VPS & Nginx.']
            ];

            $skillGaps = [
                ['name' => 'Restful API & Sanctum', 'recommended_action' => 'Pelajari Laravel Resources, autentikasi Token API, dan integrasi frontend.'],
                ['name' => 'Server & Cloud VPS', 'recommended_action' => 'Pelajari dasar-dasar perintah Linux Ubuntu Server dan setup Nginx/MySQL.'],
                ['name' => 'Version Control (Git)', 'recommended_action' => 'Biasakan melakukan commit dan push proyek sekolah Anda ke GitHub.']
            ];
        } else {
            $track = 'Network & Telecommunication Specialist';
            $learningRoadmap = 'Fokus pada pemahaman topologi jaringan, konfigurasi perangkat jaringan pintar (MikroTik, Cisco), perancangan infrastruktur akses fiber optic, dan cybersecurity dasar.';
            $lokerCriteria = 'Jaringan Komputer, IT Support';

            $milestones = [
                ['title' => 'Dasar Jaringan & Subnetting', 'status' => 'Completed', 'description' => 'Memahami IP Address dan pembagian jaringan.'],
                ['title' => 'Konfigurasi Routing & Switching', 'status' => $completedCount >= 1 ? 'Completed' : 'Pending', 'description' => 'Menghubungkan antar segmen jaringan lokal.'],
                ['title' => 'Implementasi Hotspot & Wireless Mikrotik', 'status' => $completedCount >= 3 ? 'Completed' : 'Pending', 'description' => 'Manajemen bandwidth dan otentikasi user.'],
                ['title' => 'Keamanan Jaringan & VPN Server', 'status' => $completedCount >= 8 ? 'Completed' : 'Pending', 'description' => 'Menjaga lalu lintas data perusahaan dari luar.']
            ];

            $skillGaps = [
                ['name' => 'MikroTik RouterOS', 'recommended_action' => 'Pelajari materi sertifikasi MTCNA (MikroTik Certified Network Associate).'],
                ['name' => 'Network Security', 'recommended_action' => 'Pelajari dasar-dasar Firewall Filter Rules dan Address List pada Router.'],
                ['name' => 'Serat Optik (Fiber Optic)', 'recommended_action' => 'Pelajari penyambungan fiber optic menggunakan fusion splicer.']
            ];
        }

        return [
            'career_track' => $track,
            'current_level' => $level,
            'xp_to_next' => max(0, $xpToNext),
            'xp' => $xp,
            'milestones' => $milestones,
            'skill_gaps' => $skillGaps,
            'learning_roadmap' => $learningRoadmap,
            'recommended_lokers_criteria' => $lokerCriteria
        ];
    }

    /**
     * Clean and tokenize text into meaningful keywords, stripping stopwords and preserving tech stacks.
     *
     * @param string $text
     * @return array
     */
    public static function tokenize(string $text): array
    {
        // Whitelist for valid short tech tokens (length <= 2)
        $techWhitelist = ['js', 'ts', 'go', 'ai', 'ui', 'ux', 'db', 'qa', 'ci', 'cd', 'php', 'py', 'c', 'r', 'it', 'ip'];

        // Comprehensive stopwords in Indonesian and English
        $stopwords = [
            'yang', 'dan', 'di', 'ke', 'dari', 'ini', 'itu', 'untuk', 'pada', 'adalah', 'sebagai',
            'dengan', 'bisa', 'butuh', 'mencari', 'ingin', 'mau', 'tolong', 'ada', 'atau', 'juga',
            'akan', 'sudah', 'saya', 'kami', 'anda', 'kamu', 'mereka', 'dia', 'saja', 'hanya',
            'lagi', 'bila', 'jika', 'agar', 'supaya', 'oleh', 'karena', 'tentang', 'seperti',
            'dalam', 'bagi', 'antara', 'tanpa', 'serta', 'namun', 'tetapi', 'jasa', 'bantu',
            'proyek', 'project', 'kerja', 'kerjaan', 'orang', 'buat', 'bikin', 'punya', 'ahli',
            'tau', 'tahu', 'mohon', 'tolonglah', 'dong', 'nih', 'tuh', 'lah', 'kah', 'sih',
            'the', 'a', 'an', 'and', 'or', 'in', 'on', 'at', 'to', 'for', 'with', 'by', 'of',
            'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had', 'do', 'does', 'did',
            'can', 'could', 'will', 'would', 'should', 'need', 'want', 'looking', 'help',
            'please', 'make', 'create', 'build', 'find', 'get'
        ];

        $normalized = strtolower(strip_tags($text));

        // Tech normalization: merge punctuated tech phrases into single alphanumeric tokens
        $normalized = preg_replace('/\bnode[\.\s_-]?js\b/', ' nodejs ', $normalized);
        $normalized = preg_replace('/\breact[\.\s_-]?js\b/', ' reactjs ', $normalized);
        $normalized = preg_replace('/\bvue[\.\s_-]?js\b/', ' vuejs ', $normalized);
        $normalized = preg_replace('/\bnext[\.\s_-]?js\b/', ' nextjs ', $normalized);
        $normalized = preg_replace('/\bangular[\.\s_-]?js\b/', ' angularjs ', $normalized);
        $normalized = preg_replace('/\bc\+\+\b/', ' cpp ', $normalized);
        $normalized = preg_replace('/\bc#\b/', ' csharp ', $normalized);
        $normalized = preg_replace('/\b\.net\b/', ' dotnet ', $normalized);

        // Replace any remaining non-alphanumeric characters with spaces
        $normalized = preg_replace('/[^a-z0-9\s]/', ' ', $normalized);

        $words = array_filter(explode(' ', $normalized));
        $tokens = [];

        foreach ($words as $word) {
            $w = trim($word);
            if (empty($w)) {
                continue;
            }
            if (in_array($w, $stopwords, true)) {
                continue;
            }
            if (strlen($w) <= 2 && !in_array($w, $techWhitelist, true)) {
                continue;
            }
            $tokens[] = $w;
        }

        return array_values(array_unique($tokens));
    }
}
