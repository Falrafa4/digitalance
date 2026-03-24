<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminProfileRequest;
use App\Http\Requests\UpdateAdminProfileRequest;
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;

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

    public function updateProfile(UpdateAdminProfileRequest $request)
    {
        $user = auth()->guard('administrator')->user();

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->update($request->validated());
        return back()->with('success', 'Profil Administrator berhasil diperbarui.');
    }
}
