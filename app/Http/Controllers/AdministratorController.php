<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminProfileRequest;
use App\Http\Requests\UpdateAdminProfileRequest;
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AdministratorController extends Controller
{
    // ADMIN ONLY
    public function profile()
    {
        // Hanya ngambil sesi admin
        $user = auth()->guard('administrator')->user();

        return view('dashboard.admin.profile', [
            'user' => $user,
            'role' => 'Admin'
        ]);
    }

    public function index()
    {
        $administrators = Administrator::all();
        return view('dashboard.admin.admins', compact('administrators'));
    }
    
    public function show(Administrator $administrator)
    {
        return view('dashboard.admin.admins', compact('administrator'));
    }

    public function store(StoreAdminProfileRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        Administrator::create($data);
        return redirect()->route('admin.admins.index')->with('success', 'Akun administrator berhasil dibuat');
    }

    public function update(UpdateAdminProfileRequest $request, Administrator $administrator) {
        $data = $request->validated();

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $administrator->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $administrator->update($data);
        return redirect()->route('admin.admins.index')->with('success', 'Profil administrator berhasil diperbarui.');
    }

    public function destroy(Administrator $administrator)
    {
        $administrator->delete();
        return redirect()->route('admin.admins.index')->with('success', 'Akun administrator berhasil dihapus');
    }
    
    public function updateProfile(Request $request)
    {
        $user = auth()->guard('administrator')->user();
        
        // Validasi cuma untuk name dan email
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:administrators,email,' . $user->id,
        ]);

        $user->update($data);
        return back()->with('success', 'Profil Administrator berhasil diperbarui.');
    }

    // --- FUNGSI BARU KHUSUS UNTUK UPDATE PASSWORD ---

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->guard('administrator')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('password_success', 'Password berhasil diperbarui.');
    }
}