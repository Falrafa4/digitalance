<?php

namespace App\Http\Controllers;

use App\Models\Administrator;

class AdministratorController extends Controller
{
    /**
     * Get All Administrators
     */
    public function profile()
    {
        // Hanya ngambil sesi admin
        $user = auth()->guard('administrator')->user();

        return view('profile', [
            'user' => $user,
            'role' => 'Admin'
        ]);
    }

    public function index()
    {
        $administrators = Administrator::all();
        return view('admin-admins', compact('administrators'));
    }

    /**
     * Get Administrator By ID
     */
    public function show(Administrator $administrator)
    {
        return view('dashboard.admin.administrators.show', compact('administrator'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->guard('administrator')->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:administrators,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'current_password' => 'required_with:password',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
