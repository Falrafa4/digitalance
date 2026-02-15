<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
            $token = Auth::guard('administrator')->user()->createToken('admin_token', ['administrator'])->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'role' => 'Admin',
                'token' => $token
            ]);
        }

        // attempt login as client
        if (Auth::guard('client')->attempt($credentials)) {
            $token = Auth::guard('client')->user()->createToken('client_token', ['client'])->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'role' => 'Client',
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
                'token' => $token
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Kredensial tidak valid'
        ], 401);
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
