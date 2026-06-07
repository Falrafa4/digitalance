<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FreelancerDeleteAccountRequest;
use App\Http\Requests\Api\FreelancerIndexRequest;
use App\Http\Requests\Api\FreelancerProfileUpdateRequest;
use App\Http\Requests\Api\FreelancerStoreRequest;
use App\Http\Requests\Api\FreelancerUpdateRequest;
use App\Http\Requests\Api\UpdateFreelancerPasswordRequest;
use App\Http\Resources\FreelancerResource;
use App\Models\Administrator;
use App\Models\Freelancer;
use App\Models\Notification;
use App\Support\ImageStorage;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FreelancerControllerApi extends Controller
{
    use ApiResponse;

    private const DEFAULT_PROFILE_PHOTO = 'profiles/placeholder.webp';

    /**
     * Get freelancer list for administrator.
     */
    public function index(FreelancerIndexRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Freelancer::class);

        if ($request->user()?->getRole() !== 'administrator') {
            return $this->clientFindTalent($request);
        }

        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $status = trim((string) ($validated['status'] ?? ''));
        $page = (int) ($validated['page'] ?? 1);

        $freelancers = Freelancer::query()
            ->with('skomda_student')
            ->when($q !== '', fn($query) => $query->whereHas('skomda_student', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%");
            }))
            ->when($status !== '', fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10, ['*'], 'page', $page)
            ->withQueryString();

        $freelancers->through(fn($freelancer) => (new FreelancerResource($freelancer))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'status' => $status,
                'per_page' => 10,
                'page' => $page,
            ],
            'freelancers' => $freelancers,
        ], 'Data freelancer berhasil diambil');
    }

    /**
     * Store a new freelancer for administrator.
     */
    public function store(FreelancerStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', Freelancer::class);

        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['profile_photo'] = $this->storeProfilePhoto($request) ?? self::DEFAULT_PROFILE_PHOTO;

        $freelancer = Freelancer::create($validated);
        $freelancer->skomda_student?->forceFill(['is_registered' => true])->save();

        return $this->successResponse(
            new FreelancerResource($freelancer->fresh('skomda_student')),
            'Akun freelancer berhasil dibuat',
            201
        );
    }

    /**
     * Get a single freelancer for administrator.
     */
    public function show(Freelancer $freelancer): JsonResponse
    {
        Gate::authorize('view', $freelancer);

        return $this->successResponse(
            new FreelancerResource($freelancer->load('skomda_student')),
            'Data freelancer berhasil diambil'
        );
    }

    /**
     * Get freelancer services for administrator.
     */
    public function showServices(Freelancer $freelancer): JsonResponse
    {
        Gate::authorize('view', $freelancer);

        $freelancer->load(['skomda_student', 'services.category']);

        if (request()->user()?->getRole() !== 'administrator') {
            $freelancer->setRelation(
                'services',
                $freelancer->services->where('status', 'Approved')->values()
            );
        }

        return $this->successResponse([
            'freelancer' => new FreelancerResource($freelancer),
            'services' => $freelancer->services,
        ], 'Data layanan freelancer berhasil diambil');
    }

    /**
     * Update a freelancer for administrator.
     */
    public function update(FreelancerUpdateRequest $request, Freelancer $freelancer): JsonResponse
    {
        Gate::authorize('update', $freelancer);

        $this->updateFreelancerFromValidatedData($freelancer, $request->validated(), $request);

        return $this->successResponse(
            new FreelancerResource($freelancer->fresh('skomda_student')),
            'Akun freelancer berhasil diperbarui'
        );
    }

    /**
     * Delete a freelancer for administrator.
     */
    public function destroy(Freelancer $freelancer): JsonResponse
    {
        Gate::authorize('delete', $freelancer);

        $student = $freelancer->skomda_student;

        $this->deleteProfilePhoto($freelancer->profile_photo);
        $freelancer->delete();
        $student?->forceFill(['is_registered' => false])->save();

        return $this->successResponse(null, 'Akun freelancer berhasil dihapus');
    }

    /**
     * Approve a freelancer.
     */
    public function verify(Freelancer $freelancer): JsonResponse
    {
        Gate::authorize('moderate', $freelancer);

        $freelancer->update([
            'status' => 'Approved',
            'reject_reason' => null,
        ]);

        return $this->successResponse(
            new FreelancerResource($freelancer->fresh('skomda_student')),
            'Freelancer berhasil diverifikasi'
        );
    }

    /**
     * Suspend a freelancer.
     */
    public function suspend(Freelancer $freelancer): JsonResponse
    {
        Gate::authorize('moderate', $freelancer);

        $freelancer->update([
            'status' => 'Suspended',
        ]);

        return $this->successResponse(
            new FreelancerResource($freelancer->fresh('skomda_student')),
            'Freelancer berhasil disuspend'
        );
    }

    /**
     * Activate a suspended freelancer.
     */
    public function unsuspend(Freelancer $freelancer): JsonResponse
    {
        Gate::authorize('moderate', $freelancer);

        $freelancer->update([
            'status' => 'Approved',
            'reject_reason' => null,
        ]);

        return $this->successResponse(
            new FreelancerResource($freelancer->fresh('skomda_student')),
            'Freelancer berhasil diaktifkan kembali'
        );
    }

    /**
     * Get authenticated freelancer profile.
     */
    public function profile(Request $request): JsonResponse
    {
        return $this->successResponse(
            $this->buildProfileData($request->user()),
            'Profil freelancer berhasil diambil'
        );
    }

    /**
     * Update authenticated freelancer profile.
     */
    public function updateProfile(FreelancerProfileUpdateRequest $request): JsonResponse
    {
        $freelancer = $request->user();
        $this->updateFreelancerFromValidatedData($freelancer, $request->validated(), $request);

        return $this->successResponse(
            $this->buildProfileData($freelancer->fresh('skomda_student')),
            'Profil berhasil diperbarui'
        );
    }

    /**
     * Update authenticated freelancer password.
     */
    public function updatePassword(UpdateFreelancerPasswordRequest $request): JsonResponse
    {
        $freelancer = $request->user();

        if (! Hash::check($request->current_password, $freelancer->password)) {
            return $this->errorResponse('Password saat ini salah', 422, [
                'current_password' => ['Password saat ini salah'],
            ]);
        }

        $freelancer->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return $this->successResponse(null, 'Password berhasil diperbarui');
    }

    /**
     * Delete authenticated freelancer account.
     */
    public function deleteAccount(FreelancerDeleteAccountRequest $request): JsonResponse
    {
        $freelancer = $request->user();

        if (! Hash::check($request->password, $freelancer->password)) {
            return $this->errorResponse('Password salah', 422, [
                'password' => ['Password salah'],
            ]);
        }

        $student = $freelancer->skomda_student;

        $this->deleteProfilePhoto($freelancer->profile_photo);
        $freelancer->delete();
        $student?->forceFill(['is_registered' => false])->save();

        return $this->successResponse(null, 'Akun freelancer berhasil dihapus');
    }

    /**
     * Apply for verification as authenticated freelancer.
     */
    public function applyForVerification(Request $request): JsonResponse
    {
        $freelancer = $request->user();

        if ($freelancer->status === 'Approved') {
            return $this->successResponse(
                new FreelancerResource($freelancer->load('skomda_student')),
                'Akun sudah terverifikasi'
            );
        }

        if ($freelancer->status === 'Pending') {
            return $this->successResponse(
                new FreelancerResource($freelancer->load('skomda_student')),
                'Permintaan verifikasi sudah dikirim. Tunggu keputusan admin.'
            );
        }

        $freelancer->update([
            'status' => 'Pending',
            'reject_reason' => null,
        ]);

        Administrator::query()->each(function (Administrator $administrator) use ($freelancer) {
            Notification::create([
                'title' => 'Permintaan Verifikasi Freelancer',
                'message' => "Freelancer '{$freelancer->name}' (ID: {$freelancer->id}) mengajukan verifikasi.",
                'type' => 'info',
                'role' => 'admin',
                'user_id' => $administrator->id,
                'link' => url('/admin/freelancers/' . $freelancer->id),
            ]);
        });

        return $this->successResponse(
            new FreelancerResource($freelancer->fresh('skomda_student')),
            'Permintaan verifikasi berhasil dikirim. Tunggu konfirmasi dari admin.'
        );
    }

    /**
     * Get approved freelancers for clients.
     */
    public function clientFindTalent(FreelancerIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $page = (int) ($validated['page'] ?? 1);

        $freelancers = Freelancer::query()
            ->with('skomda_student')
            ->where('status', 'Approved')
            ->withCount([
                'services as services_count' => fn($query) => $query->where('status', 'Approved'),
            ])
            ->when($q !== '', fn($query) => $query->whereHas('skomda_student', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%");
            }))
            ->orderByDesc('services_count')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'page', $page)
            ->withQueryString();

        $freelancers->through(fn($freelancer) => (new FreelancerResource($freelancer))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'per_page' => 10,
                'page' => $page,
            ],
            'talents' => $freelancers,
        ], 'Data talent berhasil diambil');
    }

    /**
     * Get an approved freelancer profile for clients.
     */
    public function clientTalentShow(Freelancer $freelancer): JsonResponse
    {
        Gate::authorize('view', $freelancer);

        if ($freelancer->status !== 'Approved') {
            return $this->errorResponse('Freelancer tidak tersedia.', 404);
        }

        return $this->successResponse(
            $this->buildProfileData($freelancer),
            'Data talent berhasil diambil'
        );
    }

    private function buildProfileData(Freelancer $freelancer): array
    {
        $freelancer->load([
            'skomda_student',
            'services' => fn($query) => $query->with('category:id,name')->latest(),
            'portofolios' => fn($query) => $query->with('service.category:id,name')->latest(),
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
            'freelancer' => new FreelancerResource($freelancer),
            'services' => $services,
            'approved_services' => $approvedServices,
            'portofolios' => $portofolios,
            'skill_tags' => $skillTags,
            'stats' => [
                'services' => $services->count(),
                'approved_services' => $approvedServices->count(),
                'portofolios' => $portofolios->count(),
                'skills' => $skillTags->count(),
            ],
        ];
    }

    private function updateFreelancerFromValidatedData(Freelancer $freelancer, array $validated, Request $request): void
    {
        if ($profilePhoto = $this->storeProfilePhoto($request)) {
            $this->deleteProfilePhoto($freelancer->profile_photo);
            $validated['profile_photo'] = $profilePhoto;
        }

        $freelancerData = [];
        foreach (['bio', 'status', 'reject_reason', 'profile_photo'] as $field) {
            if (array_key_exists($field, $validated)) {
                $freelancerData[$field] = $validated[$field];
            }
        }

        if ($freelancerData !== []) {
            $freelancer->update($freelancerData);
        }

        $studentData = [];
        foreach (['name', 'email', 'phone'] as $field) {
            if (array_key_exists($field, $validated)) {
                $studentData[$field] = $validated[$field];
            }
        }

        if ($studentData !== [] && $freelancer->skomda_student) {
            $freelancer->skomda_student->update($studentData);
        }
    }

    private function storeProfilePhoto(Request $request): ?string
    {
        if (! $request->hasFile('profile_photo')) {
            return null;
        }

        return ImageStorage::storeAsWebp($request->file('profile_photo'), 'profiles');
    }

    private function deleteProfilePhoto(?string $path): void
    {
        if (! $path || $path === self::DEFAULT_PROFILE_PHOTO) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
