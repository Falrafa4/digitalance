<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use App\Models\Loker;
use App\Models\ServiceCategory;
use App\Support\GroqClient;
use Illuminate\Http\Request;

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
            // Try Groq API first
            $apiKey = config('services.groq.key');
            $aiData = null;

            if (!empty($apiKey) && $freelancers->isNotEmpty()) {
                // Prepare data for the prompt to keep context size reasonable
                $freelancerContext = $freelancers->map(function ($f) {
                    return [
                        'id' => $f->id,
                        'name' => $f->skomda_student->name ?? 'Freelancer',
                        'major' => $f->skomda_student->major ?? 'IT',
                        'bio' => $f->bio ?? '',
                        'services' => $f->services->map(fn($s) => $s->title . ': ' . $s->description),
                        'portfolios' => $f->portofolios->map(fn($p) => $p->title . ': ' . $p->description),
                        'completed_orders' => \App\Models\Order::where('freelancer_id', $f->id)->where('status', 'Completed')->count(),
                        'avg_rating' => \App\Models\Review::whereHas('order.service', fn($q) => $q->where('freelancer_id', $f->id))->avg('rating') ?? 4.0
                    ];
                })->toArray();

                $requirementContext = $activeLoker 
                    ? "Job Title: {$activeLoker->title}\nDescription: {$activeLoker->description}" 
                    : "Custom Search Query: {$customQuery}";

                $systemPrompt = "You are an AI recruiter assistant for a school-based digital marketplace called Digitalance.
Your task is to analyze the client's job requirements and match them with the list of student freelancers provided.
You MUST respond with a valid JSON object only. Do not output markdown codeblocks, notes or extra text outside the JSON.
The JSON format must strictly be:
{
  \"recommendations\": [
    {
      \"freelancer_id\": 1,
      \"score\": 95,
      \"analysis\": \"Detail explanation in Bahasa Indonesia of why they match the requirements based on their major, services, portfolios, and rating.\",
      \"breakdown\": {
        \"skills\": 90,
        \"category\": 100,
        \"performance\": 95
      }
    }
  ]
}";

                $userPrompt = "Job Requirements:\n{$requirementContext}\n\nList of Freelancers:\n" . json_encode($freelancerContext) . "\n\nPlease calculate the match score (0-100), detailed breakdown scores for 'skills', 'category', 'performance', and write a concise Indonesian analysis for each freelancer. Return the JSON object now.";

                $responseContent = GroqClient::generate($systemPrompt, $userPrompt);

                if ($responseContent) {
                    // Try to clean markdown formatting if the model output it inside a code block
                    $cleaned = trim($responseContent);
                    if (str_starts_with($cleaned, '```json')) {
                        $cleaned = substr($cleaned, 7);
                    }
                    if (str_ends_with($cleaned, '```')) {
                        $cleaned = substr($cleaned, 0, -3);
                    }
                    $cleaned = trim($cleaned);

                    $decoded = json_decode($cleaned, true);
                    if (isset($decoded['recommendations'])) {
                        $aiData = $decoded['recommendations'];
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
                            'score' => $aiRec['score'] ?? 50,
                            'analysis' => $aiRec['analysis'] ?? 'Relevan dengan keahlian.',
                            'breakdown' => $aiRec['breakdown'] ?? [
                                'skills' => 50,
                                'category' => 50,
                                'performance' => 50
                            ]
                        ];
                    }
                }

                // Sort by score desc
                usort($recommendations, function ($a, $b) {
                    return $b['score'] <=> $a['score'];
                });
            } else {
                // Use Local Fallback
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
