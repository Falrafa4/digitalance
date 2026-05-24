<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterClientRequest;
use App\Http\Requests\RegisterFreelancerRequest;
use App\Models\Client;
use App\Models\SkomdaStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthControllerApi extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'message' => 'Data profil berhasil diambil',
            'data' => [
                'user' => $user,
                'role' => $user->getRoleNames()->first() ?? 'Unknown',
            ],
        ], 200);
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

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login gagal, pastikan email dan password benar',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'role' => $user->getRoleNames()->first() ?? 'Unknown',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil',
        ], 200);
    }
}
