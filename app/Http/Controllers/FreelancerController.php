<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFreelancerRequest;
use App\Http\Requests\UpdateFreelancerPasswordRequest;
use App\Http\Requests\UpdateFreelancerRequest;
use App\Models\Freelancer;
use App\Models\Service;
use App\Support\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FreelancerController extends Controller
{
    private const DEFAULT_PROFILE_PHOTO = 'profiles/placeholder.webp';

    private function buildProfileData(Freelancer $freelancer): array
    {
        $freelancer->load([
            'skomda_student',
            'services' => function ($query) {
                $query->with('category:id,name')
                    ->latest();
            },
            'portofolios' => function ($query) {
                $query->with('service.category:id,name')
                    ->latest();
            },
        ]);

        $services = $freelancer->services->values();
        $approvedServices = $services->where('status', 'Approved')->values();
        $portofolios = $freelancer->portofolios->values();
        $skillSource = $approvedServices->isNotEmpty() ? $approvedServices : $services;
        $skillTags = $skillSource
            ->pluck('category.name')
            ->filter()
            ->unique()
            ->values();

        return [
            'freelancer' => $freelancer,
            'services' => $services,
            'approvedServices' => $approvedServices,
            'portofolios' => $portofolios,
            'skillTags' => $skillTags,
            'stats' => [
                'services' => $services->count(),
                'approvedServices' => $approvedServices->count(),
                'portofolios' => $portofolios->count(),
                'skills' => $skillTags->count(),
            ],
        ];
    }

    // =========================
    // FREELANCER ONLY
    // =========================
    public function profile()
    {
        /** @var Freelancer $freelancer */
        $freelancer = auth('freelancer')->user();

        $profileData = $this->buildProfileData($freelancer);

        return view('dashboard.freelancer.profile', [
            'user' => $profileData['freelancer'],
            'freelancer' => $profileData['freelancer'],
            'services' => $profileData['services'],
            'approvedServices' => $profileData['approvedServices'],
            'portofolios' => $profileData['portofolios'],
            'skillTags' => $profileData['skillTags'],
            'stats' => $profileData['stats'],
            'role' => 'Freelancer',
        ]);
    }

    public function updateProfile(UpdateFreelancerRequest $request)
    {
        /** @var Freelancer $freelancer */
        $freelancer = auth('freelancer')->user();

        $validated = $request->validated();

        $freelancerData = [
            'bio' => $validated['bio'] ?? $freelancer->bio,
        ];

        if (!empty($validated['password'])) {
            $freelancerData['password'] = Hash::make($validated['password']);
        }

        $freelancer->update($freelancerData);

        if ($freelancer->skomda_student) {
            $studentData = [];
            if (isset($validated['name'])) {
                $studentData['name'] = $validated['name'];
            }
            if (isset($validated['email'])) {
                $studentData['email'] = $validated['email'];
            }
            if (isset($validated['phone'])) {
                $studentData['phone'] = $validated['phone'];
            }

            if (!empty($studentData)) {
                $freelancer->skomda_student->update($studentData);
            }
        }

        if ($profilePhoto = ImageStorage::storeAsWebp($request->file('profile_photo'), 'profiles')) {
            $this->deleteProfilePhoto($freelancer->profile_photo);
            $validated['profile_photo'] = $profilePhoto;
        }

        $freelancerData = [];
        foreach (['bio', 'profile_photo'] as $field) {
            if (array_key_exists($field, $validated)) {
                $freelancerData[$field] = $validated[$field];
            }
        }

        if (!empty($freelancerData)) {
            $freelancer->update($freelancerData);
        }

        return redirect()->route('freelancer.profile')->with('success', 'Profil berhasil diperbarui');
    }

    public function updatePassword(UpdateFreelancerPasswordRequest $request)
    {
        /** @var Freelancer $freelancer */
        $freelancer = auth('freelancer')->user();
        /** @var array{current_password:string, password:string} $validated */
        $validated = $request->validated();

        if (!Hash::check($validated['current_password'], $freelancer->password)) {
            return redirect()->route('freelancer.profile')->with('error', 'Password lama salah');
        }

        $freelancer->password = Hash::make($validated['password']);
        $freelancer->save();

        return redirect()->route('freelancer.profile')->with('success', 'Password berhasil diperbarui');
    }

    public function deleteAccount(Request $request)
    {
        /** @var Freelancer $freelancer */
        $freelancer = auth('freelancer')->user();

        if (!Hash::check($request->password, $freelancer->password)) {
            return redirect()->route('freelancer.profile')->with('error', 'Password salah');
        }

        $student = $freelancer->skomda_student;

        $this->deleteProfilePhoto($freelancer->profile_photo);
        $freelancer->delete();
        $student?->forceFill(['is_registered' => false])->save();

        return redirect()->route('home')->with('success', 'Akun freelancer berhasil dihapus');
    }

    // =========================
    // ADMIN ONLY
    // =========================
    public function index(Request $request)
    {
        return redirect()->route('admin.clients.index', ['role' => 'Freelancer']);
    }

    public function store(StoreFreelancerRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['profile_photo'] = $this->storeProfilePhoto($request) ?? self::DEFAULT_PROFILE_PHOTO;

        $freelancer = Freelancer::create($data);
        $freelancer->skomda_student?->forceFill(['is_registered' => true])->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun freelancer berhasil dibuat'], 201);
        }

        return redirect()->route('admin.freelancers.index')->with('success', 'Akun freelancer berhasil dibuat');
    }

    public function show(Freelancer $freelancer)
    {
        $freelancer->load('skomda_student');

        return view('dashboard.admin.freelancers', compact('freelancer'));
    }

    public function showServices(Freelancer $freelancer)
    {
        $freelancer->load(['services', 'skomda_student', 'services.category']);

        return view('dashboard.admin.freelancers.services', compact('freelancer'));
    }

    public function update(UpdateFreelancerRequest $request, Freelancer $freelancer)
    {
        $validated = $request->validated();

        if ($profilePhoto = $this->storeProfilePhoto($request)) {
            $this->deleteProfilePhoto($freelancer->profile_photo);
            $validated['profile_photo'] = $profilePhoto;
        }

        // Update Freelancer fields
        $freelancerData = [];
        foreach (['bio', 'status', 'profile_photo'] as $field) {
            if (array_key_exists($field, $validated)) {
                $freelancerData[$field] = $validated[$field];
            }
        }

        if (!empty($freelancerData)) {
            $freelancer->update($freelancerData);
        }

        // Update linked SkomdaStudent fields
        if ($freelancer->skomda_student) {
            $studentData = [];
            if (isset($validated['name'])) {
                $studentData['name'] = $validated['name'];
            }
            if (isset($validated['email'])) {
                $studentData['email'] = $validated['email'];
            }
            if (isset($validated['phone'])) {
                $studentData['phone'] = $validated['phone'];
            }

            if (!empty($studentData)) {
                $freelancer->skomda_student->update($studentData);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun freelancer berhasil diperbarui'], 200);
        }

        return redirect()->route('admin.freelancers.index')->with('success', 'Akun freelancer berhasil diperbarui');
    }

    public function destroy(Request $request, string $id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $student = $freelancer->skomda_student;
        $this->deleteProfilePhoto($freelancer->profile_photo);
        $freelancer->delete();
        $student?->forceFill(['is_registered' => false])->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun freelancer berhasil dihapus'], 200);
        }

        return redirect()->route('admin.freelancers.index')->with('success', 'Akun freelancer berhasil dihapus');
    }

    public function verify(int $id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update([
            'status' => 'Approved',
        ]);

        return redirect()->route('admin.freelancers.index')
            ->with('success', 'Freelancer berhasil diverifikasi');
    }

    public function suspend(int $id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update([
            'status' => 'Suspended',
        ]);

        return redirect()->route('admin.freelancers.index')
            ->with('success', 'Freelancer berhasil disuspend');
    }

    public function unsuspend(int $id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update([
            'status' => 'Approved',
        ]);

        return redirect()->route('admin.freelancers.index')
            ->with('success', 'Freelancer berhasil diaktifkan kembali');
    }

    /**
     * Freelancer applies for verification — triggered from onboarding modal.
     */
    public function applyForVerification(Request $request)
    {
        /** @var Freelancer $freelancer */
        $freelancer = auth('freelancer')->user();
        if (!$freelancer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // If already approved or already pending, return appropriate response
        if ($freelancer->status === 'Approved') {
            return response()->json(['message' => 'Akun sudah terverifikasi'], 200);
        }

        if ($freelancer->status === 'Pending') {
            return response()->json(['message' => 'Permintaan verifikasi sudah dikirim. Tunggu keputusan admin.'], 200);
        }

        // Update status to Pending and notify all administrators
        $freelancer->update(['status' => 'Pending']);

        $admins = \App\Models\Administrator::all();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'title' => 'Permintaan Verifikasi Freelancer',
                'message' => "Freelancer '{$freelancer->getNameAttribute()}' (ID: {$freelancer->id}) mengajukan verifikasi.",
                'type' => 'info',
                'role' => 'admin',
                'user_id' => $admin->id,
                'link' => route('admin.freelancers.show', $freelancer->id),
            ]);
        }

        return response()->json(['message' => 'Permintaan verifikasi berhasil dikirim. Tunggu konfirmasi dari admin.']);
    }

    // =========================
    // CLIENT ONLY (Find Talent)
    // =========================

    public function clientFindTalent()
    {
        $freelancers = Freelancer::with('skomda_student')
            ->where('status', 'Approved')
            ->withCount([
                'services as services_count' => function ($query) {
                    $query->where('status', 'Approved');
                }
            ])
            ->orderByDesc('services_count')
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.client.talents.find-talent', compact('freelancers'));
    }

    /**
     * CLIENT: Talent detail (profil + list services)
     */
    public function clientTalentShow(Freelancer $freelancer)
    {
        if ($freelancer->status !== 'Approved') {
            return redirect()->route('client.talents.index')->with('warning', 'Freelancer tidak tersedia.');
        }

        $profileData = $this->buildProfileData($freelancer);

        return view('dashboard.client.talents.talent-show', [
            'freelancer' => $profileData['freelancer'],
            'services' => $profileData['approvedServices'],
            'portofolios' => $profileData['portofolios'],
            'skillTags' => $profileData['skillTags'],
            'stats' => $profileData['stats'],
            'returnTo' => request('return_to'),
        ]);
    }

    private function storeProfilePhoto(Request $request): ?string
    {
        if (!$request->hasFile('profile_photo')) {
            return null;
        }

        return ImageStorage::storeAsWebp($request->file('profile_photo'), 'profiles');
    }

    private function deleteProfilePhoto(?string $path): void
    {
        if (!$path || $path === self::DEFAULT_PROFILE_PHOTO) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
