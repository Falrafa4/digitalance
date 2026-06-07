<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AdministratorIndexRequest;
use App\Http\Requests\Api\AdministratorProfileUpdateRequest;
use App\Http\Requests\Api\AdministratorStoreRequest;
use App\Http\Requests\Api\AdministratorUpdateRequest;
use App\Http\Requests\Api\UpdateAdministratorPasswordRequest;
use App\Http\Requests\Api\UpdateUserPasswordRequest;
use App\Http\Resources\AdministratorResource;
use App\Models\Administrator;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdministratorControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get administrator list.
     */
    public function index(AdministratorIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $status = trim((string) ($validated['status'] ?? ''));
        $page = (int) ($validated['page'] ?? 1);

        $administrators = Administrator::query()
            ->when($q !== '', fn($query) => $query->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            }))
            ->when($status !== '', fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10, ['*'], 'page', $page)
            ->withQueryString();

        $administrators->through(fn($administrator) => (new AdministratorResource($administrator))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'status' => $status,
                'per_page' => 10,
                'page' => $page,
            ],
            'administrators' => $administrators,
        ], 'Data administrator berhasil diambil');
    }

    /**
     * Store a new administrator.
     */
    public function store(AdministratorStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $validated['status'] ?? 'Active';

        $administrator = Administrator::create($validated);

        return $this->successResponse(
            new AdministratorResource($administrator->fresh()),
            'Akun administrator berhasil dibuat',
            201
        );
    }

    /**
     * Get a single administrator.
     */
    public function show(Administrator $administrator): JsonResponse
    {
        return $this->successResponse(
            new AdministratorResource($administrator),
            'Data administrator berhasil diambil'
        );
    }

    /**
     * Update an administrator.
     */
    public function update(AdministratorUpdateRequest $request, Administrator $administrator): JsonResponse
    {
        $administrator->update($request->validated());

        return $this->successResponse(
            new AdministratorResource($administrator->fresh()),
            'Akun administrator berhasil diperbarui'
        );
    }

    /**
     * Delete an administrator.
     */
    public function destroy(Administrator $administrator): JsonResponse
    {
        $administrator->delete();

        return $this->successResponse(null, 'Akun administrator berhasil dihapus');
    }

    /**
     * Update the authenticated administrator profile.
     */
    public function updateProfile(AdministratorProfileUpdateRequest $request): JsonResponse
    {
        $administrator = $request->user();
        $administrator->update($request->validated());

        return $this->successResponse(
            new AdministratorResource($administrator->fresh()),
            'Profil administrator berhasil diperbarui'
        );
    }

    /**
     * Update the authenticated administrator password.
     */
    public function updatePassword(UpdateAdministratorPasswordRequest $request): JsonResponse
    {
        $administrator = $request->user();

        if (! Hash::check($request->current_password, $administrator->password)) {
            return $this->errorResponse('Password saat ini salah', 422, [
                'current_password' => ['Password saat ini salah'],
            ]);
        }

        $this->updateHashedPassword($administrator, $request->password);

        return $this->successResponse(null, 'Password berhasil diperbarui');
    }

    /**
     * Reset another administrator password.
     */
    public function updateAdminPassword(UpdateUserPasswordRequest $request, Administrator $administrator): JsonResponse
    {
        $this->updateHashedPassword($administrator, $request->password);

        return $this->successResponse(null, 'Password ' . $administrator->name . ' berhasil diperbarui');
    }

    private function updateHashedPassword(Administrator $administrator, string $password): void
    {
        $administrator->forceFill([
            'password' => Hash::make($password),
        ])->save();
    }
}
