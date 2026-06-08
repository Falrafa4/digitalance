<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterClientRequest;
use App\Http\Requests\RegisterFreelancerRequest;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        /** @var array{student_id:int|string, password:string} $validated */
        $validated = $request->validated();

        // 1. Validasi ganda sebelum create untuk mencegah DB constraint bypass
        $student = SkomdaStudent::where('id', $validated['student_id'])->first();

        if (!$student) {
            return back()->withErrors(['student_id' => 'Siswa dengan ID Student tersebut tidak ditemukan'])->withInput();
        }

        $isExists = $student->is_registered || Freelancer::where('student_id', $validated['student_id'])->exists();
        if ($isExists) {
            return back()->withErrors(['student_id' => 'Akun freelancer untuk siswa ini sudah terdaftar. Silakan login.'])->withInput();
        }

        // 2. Bungkus dalam try-catch agar jika terjadi anomali DB tetap kembali ke form (bukan crash 500)
        try {
            $student->freelancer()->create([
                'student_id' => $validated['student_id'],
                'password' => Hash::make($validated['password']),
                'status' => 'Pending',
            ]);
            // Mark the student as registered so they won't appear in the registrable list anymore.
            try {
                $student->is_registered = true;
                $student->save();
            } catch (\Throwable $e) {
                // Log the error but don't fail the registration if this part fails
                Log::error('Failed to update SkomdaStudent after freelancer registration: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return back()->withErrors(['student_id' => 'Gagal mendaftarkan freelancer karena masalah internal database.'])->withInput();
        }

        return redirect('/login')->with('success', 'Registrasi freelancer berhasil. Silakan login.');
    }

    /**
     * Login All Role.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::guard('client')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('client.dashboard')
                ->with('success', 'Login sebagai client berhasil');
        }

        if (Auth::guard('administrator')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')
                ->with('success', 'Login sebagai administrator berhasil');
        }

        $freelancer = Freelancer::whereHas('skomda_student', function ($query) use ($credentials) {
            $query->where('email', $credentials['email']);
        })->first();

        if ($freelancer && Hash::check($credentials['password'], $freelancer->password)) {
            Auth::guard('freelancer')->login($freelancer);
            $request->session()->regenerate();

            return redirect()->route('freelancer.dashboard')
                ->with('success', 'Login sebagai freelancer berhasil');
        }

        $clientExists = Client::where('email', $credentials['email'])->exists();
        $adminExists = \App\Models\Administrator::where('email', $credentials['email'])->exists();

        if ($clientExists || $adminExists || $freelancer) {
            return back()
                ->withErrors(['email' => 'Password yang kamu masukkan salah. Silakan coba lagi.'])
                ->withInput()
                ->with('login_error', 'Password yang kamu masukkan salah. Silakan coba lagi.')
                ->with('error', 'Password salah');
        }

        return back()
            ->withErrors(['email' => 'Email tidak ditemukan. Silakan daftar terlebih dahulu.'])
            ->withInput()
            ->with('login_error', 'Email tidak ditemukan. Silakan daftar terlebih dahulu.')
            ->with('error', 'Akun tidak terdaftar');
    }

    public function logout(Request $request)
    {
        Auth::guard('administrator')->logout();
        Auth::guard('client')->logout();
        Auth::guard('freelancer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Berhasil logout');
    }
}
