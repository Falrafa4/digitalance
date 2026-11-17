<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use App\Models\Loker;
use App\Models\ServiceCategory;
use App\Support\GroqClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientAiRecommendationController extends Controller
{
    public function index(Request $request)
    {
        $client = auth('client')->user();

        // Get all active lokers of this client to let them match against a loker
        $lokers = Loker::where('client_id', $client->id)
            ->where('status', 'Open')
            ->get();

        $selectedLokerId = $request->query('loker_id');
        $customQuery = $request->query('q');

        $activeLoker = null;
        if ($selectedLokerId) {
            $activeLoker = Loker::where('client_id', $client->id)
                ->where('id', $selectedLokerId)
                ->first();
        }

        // Get approved freelancers with services, portfolios, student info
        $freelancers = Freelancer::with(['skomda_student', 'services.category', 'portofolios'])
            ->where('status', 'Approved')
            ->get();

        $recommendations = [];

        if ($activeLoker || $customQuery) {
            $apiKey = config('services.groq.key');
            $aiData = null;

            if (!empty($apiKey) && $freelancers->isNotEmpty()) {
                // Prepare concise profile data for LLM context
                $freelancerContext = $freelancers->map(function ($f) {
                    $servicesSummary = $f->services->take(3)->map(fn($s) => $s->title . ': ' . $s->description)->implode(' | ');
                    $portfoliosSummary = $f->portofolios->take(3)->map(fn($p) => $p->title . ': ' . $p->description)->implode(' | ');
                    $completedCount = \App\Models\Order::where('freelancer_id', $f->id)->where('status', 'Completed')->count();
                    $avgRating = (float) (\App\Models\Review::whereHas('order.service', fn($q) => $q->where('freelancer_id', $f->id))->avg('rating') ?? 4.0);

                    return [
                        'id' => $f->id,
                        'name' => $f->skomda_student->name ?? 'Freelancer',
                        'major' => $f->skomda_student->major ?? 'IT',
                        'bio' => $f->bio ?? '',
                        'services' => $servicesSummary,
                        'portfolios' => $portfoliosSummary,
                        'completed_orders' => $completedCount,
                        'avg_rating' => round($avgRating, 1)
                    ];
                })->toArray();

                $requirementContext = $activeLoker 
                    ? "Judul Lowongan: {$activeLoker->title}\nDeskripsi: {$activeLoker->description}" 
                    : "Kueri Kebutuhan Klien: {$customQuery}";

                $systemPrompt = "Anda adalah asisten AI Recruiter profesional untuk platform talenta siswa SMK Digitalance.
Tugas Anda: Analisis kebutuhan klien dan cocokkan dengan data talenta siswa freelancer yang disediakan.
Aturan Penilaian:
1. Cocokkan teknologi, framework, dan domain keahlian secara ketat (misal JavaScript, Node.js, Web, Backend, Frontend, UI/UX, Jaringan).
2. Siswa yang memiliki portofolio/layanan/bio yang relevan dengan tech stack yang dicari berikan skor tinggi (75 - 95).
3. Siswa yang bidangnya tidak relevan (misal teknisi jaringan/CCTV saat klien butuh developer kode) berikan skor rendah (20 - 40) dengan analisis yang jujur dan objektif.
4. Jangan pernah menyertakan kata penghubung umum seperti 'yang', 'dan', 'di' sebagai kata kunci keahlian.
5. Format output WAJIB HANYA JSON VALID tanpa formatting markdown atau teks pengantar, dengan skema:
{
  \"recommendations\": [
    {
      \"freelancer_id\": 1,
      \"score\": 85,
      \"analysis\": \"Penjelasan dalam Bahasa Indonesia yang ringkas dan jelas mengapa freelancer ini cocok/kurang cocok berdasarkan portofolio, keahlian, dan pengalamannya.\",
      \"breakdown\": {
        \"skills\": 85,
        \"category\": 90,
        \"performance\": 80
      }
    }
  ]
}";

                $userPrompt = "Kebutuhan Klien:\n{$requirementContext}\n\nDaftar Talenta Siswa:\n" . json_encode($freelancerContext, JSON_UNESCAPED_UNICODE) . "\n\nBerikan evaluasi rekomendasi JSON sekarang.";

                $responseContent = GroqClient::generate($systemPrompt, $userPrompt, ['max_tokens' => 2000]);

                if ($responseContent) {
                    // Extract JSON substring safely using regex
                    if (preg_match('/\{[\s\S]*\}/', $responseContent, $matches)) {
                        $decoded = json_decode($matches[0], true);
                        if (isset($decoded['recommendations']) && is_array($decoded['recommendations'])) {
                            $aiData = $decoded['recommendations'];
                        }
                    } else {
                        Log::warning('Failed to extract JSON from Groq response: ' . $responseContent);
                    }
                }
            }

            if ($aiData) {
                // Map AI recommendations back to Eloquent models
                $aiDataMap = collect($aiData)->keyBy('freelancer_id');
                
                foreach ($freelancers as $f) {
                    if ($aiDataMap->has($f->id)) {
                        $aiRec = $aiDataMap->get($f->id);
                        $recommendations[] = [
                            'freelancer' => $f,
                            'score' => (int) ($aiRec['score'] ?? 50),
                            'analysis' => $aiRec['analysis'] ?? 'Profil relevan dengan keahlian yang dibutuhkan.',
                            'breakdown' => [
                                'skills' => (int) ($aiRec['breakdown']['skills'] ?? 50),
                                'category' => (int) ($aiRec['breakdown']['category'] ?? 50),
                                'performance' => (int) ($aiRec['breakdown']['performance'] ?? 50),
                            ]
                        ];
                    } else {
                        // If model omitted this freelancer, compute safe baseline
                        $recommendations[] = [
                            'freelancer' => $f,
                            'score' => 30,
                            'analysis' => 'Profil belum memiliki portofolio yang cocok dengan spesifikasi kueri.',
                            'breakdown' => [
                                'skills' => 25,
                                'category' => 30,
                                'performance' => 40,
                            ]
                        ];
                    }
                }

                // Sort by score desc
                usort($recommendations, function ($a, $b) {
                    return $b['score'] <=> $a['score'];
                });
            } else {
                // Use Local Offline Fallback Engine
                $fallbackData = GroqClient::getLocalRecommendationFallback($freelancers->all(), $activeLoker?->toArray(), $customQuery);
                $fallbackMap = collect($fallbackData)->keyBy('freelancer_id');

                foreach ($freelancers as $f) {
                    if ($fallbackMap->has($f->id)) {
                        $fb = $fallbackMap->get($f->id);
                        $recommendations[] = [
                            'freelancer' => $f,
                            'score' => $fb['score'],
                            'analysis' => $fb['analysis'],
                            'breakdown' => $fb['breakdown']
                        ];
                    }
                }

                // Sort by score desc
                usort($recommendations, function ($a, $b) {
                    return $b['score'] <=> $a['score'];
                });
            }
        }

        return view('dashboard.client.ai-recommendations.index', compact(
            'lokers',
            'activeLoker',
            'customQuery',
            'recommendations'
        ));
    }
}
