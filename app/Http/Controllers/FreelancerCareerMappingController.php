<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use App\Models\Loker;
use App\Models\Notification;
use App\Models\Administrator;
use App\Support\GroqClient;
use Illuminate\Http\Request;

class FreelancerCareerMappingController extends Controller
{
    public function index(Request $request)
    {
        /** @var Freelancer $freelancer */
        $freelancer = auth('freelancer')->user();
        $freelancer->load(['skomda_student', 'services.category', 'portofolios']);

        if ($request->query('reset') === '1') {
            session()->forget(['career_map_result', 'career_map_lokers']);
        }

        $careerMap = session('career_map_result');
        $matchingLokers = session('career_map_lokers', collect());

        return view('dashboard.freelancer.career-mapping.index', compact(
            'freelancer',
            'careerMap',
            'matchingLokers'
        ));
    }

    public function analyze(Request $request)
    {
        /** @var Freelancer $freelancer */
        $freelancer = auth('freelancer')->user();
        $freelancer->load(['skomda_student', 'services.category', 'portofolios']);

        $request->validate([
            'focus_field' => 'required|string|max:255',
            'top_skills' => 'required|string|max:500',
            'interest_area' => 'required|string|max:500',
        ]);

        $completedCount = \App\Models\Order::where('freelancer_id', $freelancer->id)
            ->where('status', 'Completed')
            ->count();

        $profileData = [
            'name' => $freelancer->skomda_student->name ?? 'Freelancer',
            'major' => $freelancer->skomda_student->major ?? 'SIJA',
            'bio' => $freelancer->bio ?? '',
            'services' => $freelancer->services->map(fn($s) => ['title' => $s->title, 'category' => $s->category->name ?? '']),
            'portfolios' => $freelancer->portofolios->map(fn($p) => $p->title),
            'completed_count' => $completedCount,
            'focus_field' => $request->input('focus_field'),
            'top_skills' => $request->input('top_skills'),
            'interest_area' => $request->input('interest_area'),
        ];

        $careerMap = null;

        $apiKey = config('services.groq.key');
        if (!empty($apiKey)) {
            $systemPrompt = "You are an expert career advisor for vocational high school students in Indonesia.
Your task is to analyze the student freelancer's profile AND their self-reported preferences, then map their career progression.
You MUST respond with a valid JSON object only. Do not output markdown codeblocks, notes or extra text outside the JSON.
The JSON format must strictly be:
{
  \"career_track\": \"Name of the specialization track (e.g. Web Suite Engineer, Network Administrator, IoT Systems Integrator)\",
  \"current_level\": \"Junior Specialist or Associate Specialist or Senior Master based on completed orders and skills\",
  \"xp\": 150,
  \"xp_to_next\": 150,
  \"milestones\": [
    {
      \"title\": \"Milestone Title\",
      \"status\": \"Completed or Pending\",
      \"description\": \"Brief description of this milestone\"
    }
  ],
  \"skill_gaps\": [
    {
      \"name\": \"Skill Name\",
      \"recommended_action\": \"How the student can learn this skill\"
    }
  ],
  \"learning_roadmap\": \"A paragraph explaining their custom learning roadmap in Indonesian.\",
  \"recommended_lokers_criteria\": \"Simple search keywords for matching job postings (e.g. 'web development' or 'jaringan')\"
}";

            $userPrompt = "Freelancer Profile Data (including self-assessment):\n" . json_encode($profileData) . "\n\nPlease calculate their career path mapping considering both their existing profile data and their self-stated preferences, and return the JSON object now.";

            $responseContent = GroqClient::generate($systemPrompt, $userPrompt);

            if ($responseContent) {
                $cleaned = trim($responseContent);
                if (str_starts_with($cleaned, '```json')) {
                    $cleaned = substr($cleaned, 7);
                }
                if (str_ends_with($cleaned, '```')) {
                    $cleaned = substr($cleaned, 0, -3);
                }
                $cleaned = trim($cleaned);

                $decoded = json_decode($cleaned, true);
                if (isset($decoded['career_track'])) {
                    $careerMap = $decoded;
                }
            }
        }

        if (!$careerMap) {
            $careerMap = GroqClient::getLocalCareerMappingFallback($profileData);
        }

        $keywords = collect(explode(',', $careerMap['recommended_lokers_criteria'] ?? 'Web Development'))
            ->map(fn($k) => trim($k))
            ->filter()
            ->values();

        $matchingLokers = Loker::with('client')
            ->where('status', 'Open')
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $i => $keyword) {
                    $like = "%{$keyword}%";
                    if ($i === 0) {
                        $q->where('title', 'like', $like)
                          ->orWhere('description', 'like', $like)
                          ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', $like));
                    } else {
                        $q->orWhere('title', 'like', $like)
                          ->orWhere('description', 'like', $like)
                          ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', $like));
                    }
                }
            })
            ->latest()
            ->take(4)
            ->get();

        if ($matchingLokers->isEmpty()) {
            $matchingLokers = Loker::with('client')
                ->where('status', 'Open')
                ->latest()
                ->take(4)
                ->get();
        }

        session([
            'career_map_result' => $careerMap,
            'career_map_lokers' => $matchingLokers,
            'last_focus_field' => $request->input('focus_field'),
            'last_top_skills' => $request->input('top_skills'),
            'last_interest_area' => $request->input('interest_area'),
        ]);

        return redirect()->route('freelancer.career-mapping', '#results')->with('success', 'Analisis pemetaan karir berhasil dilakukan!');
    }

    public function submitCareerApproval(Request $request)
    {
        /** @var Freelancer $freelancer */
        $freelancer = auth('freelancer')->user();

        if ($freelancer->status === 'Approved') {
            return redirect()->back()->with('warning', 'Akun Anda sudah terverifikasi.');
        }

        if ($freelancer->status === 'Pending') {
            return redirect()->back()->with('warning', 'Permintaan verifikasi Anda sedang dalam peninjauan admin.');
        }

        $request->validate([
            'career_track' => 'required|string|max:255',
        ]);

        $freelancer->update([
            'career_track' => $request->input('career_track'),
            'career_track_status' => 'Pending',
            'status' => 'Pending',
        ]);

        $admins = Administrator::all();
        $studentName = $freelancer->skomda_student->name ?? 'Freelancer Baru';

        foreach ($admins as $admin) {
            Notification::create([
                'title' => 'Permintaan Verifikasi Akun & Jalur Karir',
                'message' => "Freelancer '{$studentName}' mengajukan verifikasi akun dengan spesialisasi: '{$freelancer->career_track}'.",
                'type' => 'info',
                'role' => 'admin',
                'user_id' => $admin->id,
                'link' => route('admin.freelancers.index') . '?q=' . urlencode($studentName),
            ]);
        }

        session()->forget(['career_map_result', 'career_map_lokers']);

        return redirect()->route('freelancer.career-mapping')->with('success', 'Permintaan verifikasi akun dan jalur karir berhasil diajukan ke admin!');
    }
}
