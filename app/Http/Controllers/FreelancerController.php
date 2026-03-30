<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFreelancerRequest;
use App\Http\Requests\UpdateFreelancerPasswordRequest;
use App\Http\Requests\UpdateFreelancerRequest;
use App\Models\Freelancer;
use Illuminate\Support\Facades\Hash;

class FreelancerController extends Controller
{
    public function profile()
    {
        // Hanya ngambil sesi freelancer
        $freelancer = auth('freelancer')->user();

        // Tarik relasi datanya
        $freelancer->load('skomda_student');

        // Akalin properti name & email biar sama kayak Admin & Client di Blade
        $freelancer->name = $freelancer->skomda_student->name ?? 'Siswa Skomda';
        $freelancer->email = $freelancer->skomda_student->email ?? '-';

        return view('freelancer.profile', [
            'user' => $freelancer,
            'role' => 'Freelancer'
        ]);
    }

    public function updateProfile(UpdateFreelancerRequest $request)
    {
        $freelancer = auth('freelancer')->user();
        $freelancer->update($request->only(['name', 'email', 'phone']));

        return redirect()->route('freelancer.profile')->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Update Freelancer Password
     */
    public function update_password(UpdateFreelancerPasswordRequest $request)
    {
        $freelancer = auth('freelancer')->user();

        // Cek password lama
        if (!Hash::check($request->current_password, $freelancer->password)) {
            return redirect()->route('freelancer.profile')->with('error', 'Password lama salah');
        }

        // Update password baru
        $freelancer->password = Hash::make($request->password);
        $freelancer->save();
        return redirect()->route('freelancer.profile')->with('success', 'Password berhasil diperbarui');
    }

    /**
     * Get All Freelancers
     */
    public function index()
    {
        $freelancers = Freelancer::with('skomda_student')->get();
        return view('dashboard.admin.freelancers', compact('freelancers'));
    }

    /**
     * Store New Freelancer
     */
    public function store(StoreFreelancerRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        Freelancer::create($data);
        return redirect()->route('admin.freelancers.index')->with('success', 'Akun freelancer berhasil dibuat');
    }

    /**
     * Get Freelancer By ID
     */
    public function show(Freelancer $freelancer)
    {
        $freelancer->load('skomda_student');
        return view('dashboard.admin.freelancers', compact('freelancer'));
    }

    /**
     * Get Freelancer Services By ID
     */
    public function showServices(Freelancer $freelancer)
    {
        $freelancer->load(['services', 'skomda_student', 'services.category']);
        return view('dashboard.admin.freelancers.services', compact('freelancer'));
    }

    /**
     * Update Freelancer By ID
     */
    public function update(UpdateFreelancerRequest $request, Freelancer $freelancer)
    {
        $freelancer->update($request->validated());
        return redirect()->route('admin.freelancers.index')->with('success', 'Akun freelancer berhasil diperbarui');
    }

    /**
     * Delete Freelancer By ID
     */
    public function destroy(string $id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->delete();

        return redirect()->route('admin.freelancers.index')->with('success', 'Akun freelancer berhasil dihapus');
    }
}
