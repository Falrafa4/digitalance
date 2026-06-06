<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ClientIndexRequest;
use App\Http\Requests\Api\UpdateUserPasswordRequest;
use App\Http\Requests\Api\UserStoreRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Http\Requests\UpdateClientPasswordRequest;
use App\Http\Requests\UpdateClientProfileRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\UserManagementResource;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\SkomdaStudent;
use App\Support\ImageStorage;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ClientControllerApi extends Controller
{
    use ApiResponse;

    private const DEFAULT_PROFILE_PHOTO = 'profiles/placeholder.webp';

    /**
     * Get All Data for User Management (Admin Dashboard)
     */
    public function index(ClientIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $role = $this->normalizeAdminUserRole($validated['role'] ?? 'all');
        $perPage = (int) ($validated['per_page'] ?? 12);

        $clientsQuery = Client::query()->select('id', 'name', 'email', 'phone', 'profile_photo', DB::raw('NULL as avatar'), DB::raw("'Client' as role"), DB::raw("'Active' as status"), 'created_at');
        $skomdaQuery = SkomdaStudent::query()
            ->whereDoesntHave('freelancer')
            ->select('id', 'name', 'email', 'phone', DB::raw('NULL as profile_photo'), 'avatar', DB::raw("'Skomda Student' as role"), DB::raw("'Active' as status"), 'created_at');
        
        // Use the query builder so Freelancer::getNameAttribute() does not override the joined student name.
        $freelancersQuery = DB::table('freelancers')
            ->join('skomda_students', 'freelancers.student_id', '=', 'skomda_students.id')
            ->select('freelancers.id', 'skomda_students.name', 'skomda_students.email', 'skomda_students.phone', 'freelancers.profile_photo', 'skomda_students.avatar', DB::raw("'Freelancer' as role"), 'freelancers.status', 'freelancers.created_at');

        if ($q !== '') {
            $clientsQuery->where(fn($query) => $query
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%"));

            $skomdaQuery->where(fn($query) => $query
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('nis', 'like', "%{$q}%"));

            $freelancersQuery->where(fn($query) => $query
                ->where('skomda_students.name', 'like', "%{$q}%")
                ->orWhere('skomda_students.email', 'like', "%{$q}%")
                ->orWhere('skomda_students.nis', 'like', "%{$q}%"));
        }

        if ($role === 'Client') {
            $users = $clientsQuery->latest()->paginate($perPage)->withQueryString();
        } elseif ($role === 'Freelancer') {
            $users = $freelancersQuery->orderByDesc('freelancers.created_at')->paginate($perPage)->withQueryString();
        } elseif ($role === 'Skomda Student') {
            $users = $skomdaQuery->latest()->paginate($perPage)->withQueryString();
        } else {
            // Combined using Union
            $combined = $clientsQuery->union($skomdaQuery)->union($freelancersQuery);
            $users = DB::table(DB::raw("({$combined->toSql()}) as combined"))
                ->mergeBindings($combined->getQuery())
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();
        }

        $users->through(fn($user) => (new UserManagementResource($user))->toArray($request));

        // $skomdaAll = SkomdaStudent::query()
        //     ->whereDoesntHave('freelancer')
        //     ->select('id', 'name', 'nis')
        //     ->orderBy('name')
        //     ->get()
        //     ->map(fn($student) => (new SkomdaStudentOptionResource($student))->toArray($request))
        //     ->values();

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'role' => $role,
                'per_page' => $perPage,
            ],
            'users' => $users,
            // 'skomda_students' => $skomdaAll,
        ], 'Data users berhasil diambil');
    }

    /**
     * Store Client Data
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $client = Client::create($validated);

        return $this->successResponse(new ClientResource($client), 'Klien berhasil ditambahkan', 201);
    }

    /**
     * Get Single Client Data
     */
    public function show(Client $client): JsonResponse
    {
        return $this->successResponse(new ClientResource($client), 'Data klien berhasil diambil');
    }

    /**
     * Update Client Data
     */
    public function update(UserUpdateRequest $request, Client $client): JsonResponse
    {
        $client->update($request->validated());

        return $this->successResponse(new ClientResource($client->fresh()), 'Akun klien berhasil diperbarui');
    }

    /**
     * Delete Client Data
     */
    public function destroy(Client $client): JsonResponse
    {
        $this->deleteProfilePhoto($client->profile_photo);
        $client->delete();

        return $this->successResponse(null, 'Akun klien berhasil dihapus');
    }

    /**
     * Update the client's profile.
     */
    public function updateProfile(UpdateClientProfileRequest $request): JsonResponse
    {
        $client = $request->user();
        $validated = $request->validated();

        if ($profilePhoto = $this->storeProfilePhoto($request)) {
            $this->deleteProfilePhoto($client->profile_photo);
            $validated['profile_photo'] = $profilePhoto;
        }

        $client->update($validated);

        return $this->successResponse(new ClientResource($client->fresh()), 'Profil berhasil diperbarui');
    }

    /**
     * Update the client's password.
     */
    public function updatePassword(UpdateClientPasswordRequest $request): JsonResponse
    {
        $client = $request->user();

        if (! Hash::check($request->current_password, $client->password)) {
            return $this->errorResponse('Password saat ini salah', 422, [
                'current_password' => ['Password saat ini salah'],
            ]);
        }

        $this->updateHashedPassword($client, $request->password);

        return $this->successResponse(null, 'Password berhasil diperbarui');
    }

    // ==========================================
    // ADMIN-ONLY (MANAGE ANY CLIENT)
    // ==========================================
    /**
     * Update a client's password.
     */
    public function updateClientPassword(UpdateUserPasswordRequest $request, string $id): JsonResponse
    {
        $client = Client::findOrFail($id);
        $this->updateHashedPassword($client, $request->password);

        return $this->successResponse(null, 'Password ' . $client->name . ' berhasil diperbarui');
    }

    /**
     * Update a freelancer's password.
     */
    public function updateFreelancerPassword(UpdateUserPasswordRequest $request, string $id): JsonResponse
    {
        $freelancer = Freelancer::findOrFail($id);
        $this->updateHashedPassword($freelancer, $request->password);

        return $this->successResponse(null, 'Password ' . ($freelancer->skomda_student->name ?? 'Freelancer') . ' berhasil diperbarui');
    }

    /**
     * Update a Skomda student's password.
     */
    public function updateSkomdaPassword(UpdateUserPasswordRequest $request, string $id): JsonResponse
    {
        $skomdaStudent = SkomdaStudent::findOrFail($id);

        if (! $this->updateHashedPassword($skomdaStudent, $request->password)) {
            return $this->errorResponse('Perubahan password untuk akun Skomda Student belum didukung.', 422);
        }

        return $this->successResponse(null, 'Password ' . $skomdaStudent->name . ' berhasil diperbarui');
    }

    // ==========================================
    // FREELANCER ONLY
    // ==========================================

    /**
     * Get all clients (for freelancers to view potential clients).
     */
    public function freelancerIndex(): JsonResponse
    {
        $clients = Client::query()
            ->latest()
            ->get()
            ->map(fn($client) => (new ClientResource($client))->toArray(request()))
            ->values();

        return $this->successResponse($clients, 'Data klien berhasil diambil');
    }

    private function normalizeAdminUserRole(?string $role): string
    {
        $normalized = trim((string) $role);

        return match ($normalized) {
            'Client', 'Freelancer', 'Skomda Student', 'all' => $normalized,
            'Siswa Skomda', 'Skomda Students', 'Skomda', 'skomda student' => 'Skomda Student',
            default => 'all',
        };
    }

    private function updateHashedPassword(Model $model, string $password): bool
    {
        if (! Schema::hasColumn($model->getTable(), 'password')) {
            return false;
        }

        $model->forceFill([
            'password' => Hash::make($password),
        ])->save();

        return true;
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
