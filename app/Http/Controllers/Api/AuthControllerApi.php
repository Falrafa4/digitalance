<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterClientRequest;
use App\Http\Requests\RegisterFreelancerRequest;
use App\Http\Resources\AdministratorResource;
use App\Http\Resources\FreelancerResource;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\SkomdaStudent;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        return response()->json([
            'status' => 'success',
            'message' => 'Akun client berhasil dibuat',
        ], 201);
    }

    /**
     * Register a new freelancer.
     */
    public function registerFreelancer(RegisterFreelancerRequest $request)
    {
        $validated = $request->validated();
        $student = SkomdaStudent::where('id', $validated['student_id'])->first();

        if (! $student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa dengan ID Student tersebut tidak ditemukan',
            ], 404);
        }

        if ($student->freelancer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun freelancer untuk ID Student ini sudah terdaftar. Silakan login.',
            ], 400);
        }

        $student->freelancer()->create([
            'student_id' => $validated['student_id'],
            'password' => Hash::make($validated['password']),
            'status' => 'Pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi freelancer berhasil. Silakan login.',
        ], 201);
    }

    /**
     * Login All Role.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::guard('administrator')->attempt($credentials)) {
            $admin = $request->user('administrator');
            $token = $admin->createToken('api-token')->plainTextToken;
            
            return $this->successResponse([
                'user' => new AdministratorResource($admin),
                'role' => 'administrator',
                'token' => $token,
            ], 'Login sebagai administrator berhasil');
        }

        if (Auth::guard('client')->attempt($credentials)) {
            $token = $request->user('client')->createToken('api-token')->plainTextToken;

            return $this->successResponse([
                'user' => $request->user('client'),
                'role' => 'client',
                'token' => $token,
            ], 'Login sebagai client berhasil');
        }

        $freelancer = Freelancer::with('skomda_student')->whereHas('skomda_student', function ($query) use ($credentials) {
            $query->where('email', $credentials['email']);
        })->first();

        if ($freelancer && $freelancer->status !== 'Approved') {
            return $this->errorResponse('Akun freelancer Anda sedang dalam status ' . $freelancer->status . '. Silakan tunggu konfirmasi dari administrator.', 403);
        }

        if ($freelancer && Hash::check($credentials['password'], $freelancer->password)) {
            Auth::guard('freelancer')->login($freelancer);
            $token = $request->user('freelancer')->createToken('api-token')->plainTextToken;

            return $this->successResponse([
                'user' => new FreelancerResource($freelancer),
                'role' => 'freelancer',
                'token' => $token,
            ], 'Login sebagai freelancer berhasil');
        }

        return $this->errorResponse('Email atau password salah. Silakan coba lagi.', 401);
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
