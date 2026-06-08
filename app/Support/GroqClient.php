<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqClient
{
    /**
     * Call the Groq Chat Completion API.
     *
     * @param string $systemPrompt
     * @param string $userPrompt
     * @return string|null
     */
    public static function generate(string $systemPrompt, string $userPrompt): ?string
    {
        $apiKey = config('services.groq.key');
        $model = config('services.groq.model', 'llama-3.1-8b-instant');

        if (empty($apiKey)) {
            Log::warning('Groq API Key is not set. Falling back to local offline engine.');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'temperature' => 0.2,
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            Log::error('Groq API error response: ' . $response->body());
        } catch (\Throwable $e) {
            Log::error('Exception during Groq API call: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Local Offline Fallback for Client Freelancer Recommendation.
     * Computes similarity scores using category matching, string token overlaps, and reputation.
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
            $scorePerformance = 0;

            // 1. Category Match (30%)
            if ($categoryId) {
                // Check if freelancer has services in this category
                $hasCategoryService = collect($f->services)->contains('category_id', $categoryId);
                if ($hasCategoryService) {
                    $scoreCategory = 100;
                }
            } else {
                // If custom query, match query words against category names
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
                $scoreCategory = $matchedCategories > 0 ? 100 : 30;
            }

            // 2. Skill Text Match (40%)
            $freelancerText = ($f->bio ?? '') . ' ';
            foreach ($f->services as $service) {
                $freelancerText .= ($service->title ?? '') . ' ' . ($service->description ?? '') . ' ';
            }
            foreach ($f->portofolios as $portfolio) {
                $freelancerText .= ($portfolio->title ?? '') . ' ' . ($portfolio->description ?? '') . ' ';
            }
            if ($f->skomda_student) {
                $freelancerText .= ($f->skomda_student->major ?? '') . ' ' . ($f->skomda_student->class ?? '');
            }

            $freelancerWords = self::tokenize($freelancerText);
            $intersection = array_intersect($queryWords, $freelancerWords);
            
            if (count($queryWords) > 0) {
                $skillMatchRatio = count($intersection) / count($queryWords);
                $scoreSkills = min(100, 30 + ($skillMatchRatio * 70));
            } else {
                $scoreSkills = 50; // default medium match
            }

            // 3. Performance Metric (30%)
            // Average Review Rating (max 5.0 -> 15%)
            $avgRating = \App\Models\Review::whereHas('order.service', function ($q) use ($f) {
                $q->where('freelancer_id', $f->id);
            })->avg('rating') ?? 4.0; // default 4 if no review
            $scoreRating = min(100, $avgRating * 20);

            // Completed orders count (max 5 orders -> 10%)
            $completedOrdersCount = \App\Models\Order::where('freelancer_id', $f->id)
                ->where('status', 'Completed')
                ->count();
            $scoreOrders = min(100, $completedOrdersCount * 20);

            // Verified Status Bonus (5%)
            $scoreVerified = $f->status === 'Approved' ? 100 : 20;

            $scorePerformance = ($scoreRating * 0.5) + ($scoreOrders * 0.3) + ($scoreVerified * 0.2);

            // Calculate final weighted score
            $finalScore = (int) round(($scoreCategory * 0.3) + ($scoreSkills * 0.4) + ($scorePerformance * 0.3));
            $finalScore = max(0, min(100, $finalScore));

            // Generate analysis text based on variables
            $matchedSkillsStr = implode(', ', array_slice($intersection, 0, 3));
            $analysis = "Freelancer cocok berdasarkan kategori " . ($f->skomda_student->major ?? 'IT') . ". ";
            if (!empty($matchedSkillsStr)) {
                $analysis .= "Ditemukan kata kunci relevan: [{$matchedSkillsStr}]. ";
            }
            if ($completedOrdersCount > 0) {
                $analysis .= "Telah sukses menyelesaikan {$completedOrdersCount} proyek dengan reputasi bintang " . number_format($avgRating, 1) . ".";
            } else {
                $analysis .= "Siswa bertalenta yang siap mengerjakan proyek pertama dengan bimbingan pendamping.";
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
        
        // XP calculation: 100 XP per completed project + baseline 100
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
            // Default or TJAT
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
     * Helper to tokenize a string.
     */
    private static function tokenize(string $text): array
    {
        $text = strtolower(strip_tags($text));
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $words = explode(' ', $text);
        
        // Remove empty or very short tokens
        $filtered = array_filter($words, function ($w) {
            return strlen($w) > 2;
        });

        return array_values(array_unique($filtered));
    }
}
