<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterClientRequest;
use App\Http\Requests\RegisterFreelancerRequest;
use App\Http\Resources\AdministratorResource;
use App\Http\Resources\FreelancerResource;
use App\Models\Administrator;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\SkomdaStudent;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get the authenticated user's profile.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return $this->successResponse([
            'user' => $user,
            'role' => Auth::guard('administrator')->check() ? 'administrator' : (Auth::guard('client')->check() ? 'client' : (Auth::guard('freelancer')->check() ? 'freelancer' : null)),
        ], 'Profil berhasil diambil');
    }

    /**
     * Register a new client.
     */
    public function registerClient(RegisterClientRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        Client::create($validated);

        return $this->successResponse(null, 'Registrasi client berhasil. Silakan login.', 201);
    }

    /**
     * Register a new freelancer.
     */
    public function registerFreelancer(RegisterFreelancerRequest $request)
    {
        $validated = $request->validated();
        $student = SkomdaStudent::where('id', $validated['student_id'])->first();

        if (! $student) {
            return $this->errorResponse('Siswa dengan ID Student tersebut tidak ditemukan', 404);
        }

        if ($student->is_registered || Freelancer::where('student_id', $validated['student_id'])->exists()) {
            return $this->errorResponse('Akun freelancer untuk ID Student ini sudah terdaftar. Silakan login.', 400);
        }

        try {
            $student->freelancer()->create([
                'student_id' => $validated['student_id'],
                'password' => Hash::make($validated['password']),
                'status' => 'Pending',
            ]);

            try {
                $student->is_registered = true;
                $student->save();
            } catch (\Exception $e) {
                // Log the error but don't fail the registration if this part fails
                Log::error('Failed to update SkomdaStudent after freelancer registration: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Terjadi kesalahan saat mendaftarkan freelancer. Silakan coba lagi.', 500);
        }

        return $this->successResponse(null, 'Registrasi freelancer berhasil. Silakan login.', 201);
    }

    /**
     * Login All Role.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $email = $credentials['email'];
        $password = $credentials['password'];
        $role = $credentials['role'] ?? null;

        if ($role) {
            return match ($role) {
                'administrator' => $this->loginAdministrator($email, $password),
                'client' => $this->loginClient($email, $password),
                'freelancer' => $this->loginFreelancer($email, $password),
            } ?? $this->errorResponse('Email atau password salah. Silakan coba lagi.', 401);
        }

        foreach (['administrator', 'client', 'freelancer'] as $attemptRole) {
            $response = match ($attemptRole) {
                'administrator' => $this->loginAdministrator($email, $password),
                'client' => $this->loginClient($email, $password),
                'freelancer' => $this->loginFreelancer($email, $password),
            };

            if ($response) {
                return $response;
            }
        }

        return $this->errorResponse('Email atau password salah. Silakan coba lagi.', 401);
    }

    private function loginAdministrator(string $email, string $password): ?JsonResponse
    {
        $admin = Administrator::where('email', $email)->first();

        if (! $admin || ! Hash::check($password, $admin->password)) {
            return null;
        }

        $token = $admin->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'user' => new AdministratorResource($admin),
            'role' => 'administrator',
            'token' => $token,
        ], 'Login sebagai administrator berhasil');
    }

    private function loginClient(string $email, string $password): ?JsonResponse
    {
        $client = Client::where('email', $email)->first();

        if (! $client || ! Hash::check($password, $client->password)) {
            return null;
        }

        $token = $client->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'user' => $client,
            'role' => 'client',
            'token' => $token,
        ], 'Login sebagai client berhasil');
    }

    private function loginFreelancer(string $email, string $password): ?JsonResponse
    {
        $freelancer = Freelancer::whereHas('skomda_student', function ($query) use ($email) {
            $query->where('email', $email);
        })->first();

        if (! $freelancer || ! Hash::check($password, $freelancer->password)) {
            return null;
        }

        if ($freelancer->status !== 'Approved') {
            return $this->errorResponse('Akun freelancer Anda sedang dalam status ' . $freelancer->status . '. Silakan tunggu konfirmasi dari administrator.', 403);
        }

        $token = $freelancer->createToken('api-token')->plainTextToken;
        $freelancer->load('skomda_student');

        return $this->successResponse([
            'user' => new FreelancerResource($freelancer),
            'role' => 'freelancer',
            'token' => $token,
        ], 'Login sebagai freelancer berhasil');
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout berhasil');
    }
}
