<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterClientRequest;
use App\Http\Requests\RegisterFreelancerRequest;
use App\Models\Client;
use App\Models\SkomdaStudent;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function showRegister()
    {
        $categories = ServiceCategory::pluck('name')->toArray();
        return view('auth.login', compact('categories'));
    }

    public function registerClient(RegisterClientRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        Client::create($validated);

        return redirect('/login')->with('success', 'Registrasi berhasil');
    }

    public function registerFreelancer(RegisterFreelancerRequest $request)
    {
        $request->validated();

        $student = SkomdaStudent::where('student_id', $request->student_id)->first();

        if (!$student) {
            return back()->withErrors(['student_id' => 'Siswa dengan ID Student tersebut tidak ditemukan'])->withInput();
        }

        if ($student->freelancer) {
            return back()->withErrors(['student_id' => 'Akun freelancer untuk ID Student ini sudah terdaftar. Silakan login.'])->withInput();
        }

        $defaultPassword = $student->nis;

        $student->freelancer()->create([
            'student_id' => $request->student_id,
            'password' => Hash::make($defaultPassword),
            'status' => 'Pending',
        ]);

        return redirect('/login')->with('success', 'Registrasi freelancer berhasil. Password awal adalah NIS Anda.');
    }

    /**
     * Login All Role.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::guard('client')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/client');
        }

        if (Auth::guard('administrator')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin');
        }

        if (Auth::guard('freelancer')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/freelancer');
        }

        return back()->withErrors(['email' => 'Email atau Password salah, atau akun belum terdaftar!']);
    }

    public function logout(Request $request)
    {
        Auth::guard('administrator')->logout();
        Auth::guard('client')->logout();
        Auth::guard('freelancer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
