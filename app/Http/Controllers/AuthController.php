<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\SkomdaStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function showLogin()
    {
        return view('auth.login');
    }

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

        return redirect('/login')->with('success', 'Registrasi berhasil');
    }

    /**
     * Register Freelancer.
     */
    public function register_freelancer(Request $request)
    {
        $request->validate([
            'nis' => 'required|exists:skomda_students,nis',
            'password' => 'required|min:6',
        ]);

        $student = SkomdaStudent::where('nis', $request->nis)->first();

        if (!$student) {
            return back()->withErrors('Siswa dengan NIS tersebut tidak ditemukan');
        }

        if ($student->freelancer) {
            return back()->withErrors('Akun freelancer sudah terdaftar');
        }

        $student->freelancer()->create([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil');
    }

    /**
     * Login All Role.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // admin
        if (Auth::guard('administrator')->attempt($credentials)) {
            return redirect('/admin/dashboard');
        }

        // client
        if (Auth::guard('client')->attempt($credentials)) {
            return redirect('/client/dashboard');
        }

        // freelancer
        $student = SkomdaStudent::where('email', $credentials['email'])
            ->with('freelancer')
            ->first();

        if (
            $student && $student->freelancer &&
            Hash::check($credentials['password'], $student->freelancer->password)
        ) {

            Auth::guard('freelancer')->login($student->freelancer);

            return redirect('/freelancer/dashboard');
        }

        return back()->withErrors('Email atau password salah');
    }

    public function logout()
    {
        Auth::guard('administrator')->logout();
        Auth::guard('client')->logout();
        Auth::guard('freelancer')->logout();

        return redirect('/login');
    }
}
