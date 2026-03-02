<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\SkomdaStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register_client(Request $request)
    {
        // create client account
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'phone' => 'required|unique:clients,phone',
        ]);

        Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil'
        ]);
    }

    public function register_freelancer(Request $request)
    {
        // create freelancer account
        $request->validate([
            'nis' => 'required|exists:skomda_students,nis',
            'password' => 'required|min:6',
        ]);

        $student = SkomdaStudent::where('nis', $request->nis)->first();

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Siswa dengan NIS tersebut tidak ditemukan'
            ], 404);
        }

        $freelancer = $student->freelancer;

        if ($freelancer) {
            return response()->json([
                'status' => false,
                'message' => 'Akun freelancer untuk siswa dengan NIS tersebut sudah terdaftar'
            ], 400);
        }

        $freelancer = $student->freelancer()->create([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registrasi berhasil'
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // attempt login as admin
        if (Auth::guard('administrator')->attempt($credentials)) {
            $admin = Auth::guard('administrator')->user();
            $token = $admin->createToken('admin_token', ['administrator'])->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'role' => 'Admin',
                'user' => $admin,
                'token' => $token
            ]);
        }

        // attempt login as client
        if (Auth::guard('client')->attempt($credentials)) {
            $client = Auth::guard('client')->user();
            $token = $client->createToken('client_token', ['client'])->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'role' => 'Client',
                'user' => $client,
                'token' => $token
            ]);
        }

        // attempt login as freelancer
        $student = SkomdaStudent::where('email', $credentials['email'])
            ->with('freelancer')
            ->first();

        if ($student && $student->freelancer && Hash::check($credentials['password'], $student->freelancer->password)) {
            $freelancer = $student->freelancer;

            Auth::guard('freelancer')->login($freelancer);
            $token = $freelancer->createToken('freelancer_token', ['freelancer'])->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'role' => 'Freelancer',
                'user' => $freelancer,
                'token' => $token
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Kredensial tidak valid'
        ], 401);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // detect user role
        if ($user instanceof Administrator) {
            $role = 'administrator';
        } elseif ($user instanceof Client) {
            $role = 'client';
        } elseif ($user instanceof Freelancer) {
            $role = 'freelancer';
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User role tidak dikenali'
            ], 400);
        }

        return response()->json([
            'status' => true,
            'role' => $role,
            'data' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}
