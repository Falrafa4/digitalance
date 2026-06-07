<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdministratorResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\FreelancerResource;
use App\Models\Administrator;
use App\Models\Client;
use App\Models\Freelancer;
use App\Support\ImageStorage;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileControllerApi extends Controller
{
    use ApiResponse;

    private const DEFAULT_PROFILE_PHOTO = 'profiles/placeholder.webp';

    public function show(Request $request): JsonResponse
    {
        return $this->successResponse($this->profileResource($request->user()), 'Profil berhasil diambil');
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate($this->profileRules($user));

        if ($user instanceof Client || $user instanceof Freelancer) {
            if ($profilePhoto = $this->storeProfilePhoto($request)) {
                $this->deleteProfilePhoto($user->profile_photo);
                $validated['profile_photo'] = $profilePhoto;
            }
        }

        if ($user instanceof Freelancer) {
            $this->updateFreelancerProfile($user, $validated);

            return $this->successResponse(
                new FreelancerResource($user->fresh('skomda_student')),
                'Profil berhasil diperbarui'
            );
        }

        $user->update($validated);

        return $this->successResponse($this->profileResource($user->fresh()), 'Profil berhasil diperbarui');
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return $this->errorResponse('Password saat ini salah', 422, [
                'current_password' => ['Password saat ini salah'],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return $this->successResponse(null, 'Password berhasil diperbarui');
    }

    private function profileResource($user)
    {
        if ($user instanceof Administrator) {
            return new AdministratorResource($user);
        }

        if ($user instanceof Client) {
            return new ClientResource($user);
        }

        if ($user instanceof Freelancer) {
            return new FreelancerResource($user->loadMissing('skomda_student'));
        }

        return $user;
    }

    /**
     * @return array<string, mixed>
     */
    private function profileRules($user): array
    {
        if ($user instanceof Administrator) {
            return [
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('administrators', 'email')->ignore($user->id)],
                'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
                'bio' => ['sometimes', 'nullable', 'string', 'max:1000'],
                'avatar' => ['sometimes', 'nullable', 'string', 'max:255'],
            ];
        }

        if ($user instanceof Client) {
            return [
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($user->id)],
                'phone' => ['sometimes', 'required', 'string', 'max:30'],
                'profile_photo' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            ];
        }

        if ($user instanceof Freelancer) {
            return [
                'bio' => ['sometimes', 'nullable', 'string', 'max:500'],
                'name' => ['sometimes', 'nullable', 'string', 'max:255'],
                'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('skomda_students', 'email')->ignore($user->student_id)],
                'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
                'profile_photo' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            ];
        }

        return [];
    }

    private function updateFreelancerProfile(Freelancer $freelancer, array $validated): void
    {
        $freelancerData = [];
        foreach (['bio', 'profile_photo'] as $field) {
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
