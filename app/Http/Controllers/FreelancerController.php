<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFreelancerRequest;
use App\Http\Requests\UpdateFreelancerPasswordRequest;
use App\Http\Requests\UpdateFreelancerRequest;
use App\Models\Freelancer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class FreelancerController extends Controller
{
    // FREELANCER ONLY
    public function profile()
    {
        $freelancer = auth('freelancer')->user();

        $freelancer->load('skomda_student');

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

    public function updatePassword(UpdateFreelancerPasswordRequest $request)
    {
        $freelancer = auth('freelancer')->user();

        if (!Hash::check($request->current_password, $freelancer->password)) {
            return redirect()->route('freelancer.profile')->with('error', 'Password lama salah');
        }

        $freelancer->password = Hash::make($request->password);
        $freelancer->save();
        return redirect()->route('freelancer.profile')->with('success', 'Password berhasil diperbarui');
    }

    public function deleteAccount(Request $request)
    {
        $freelancer = auth('freelancer')->user();

        if (!Hash::check($request->password, $freelancer->password)) {
            return redirect()->route('freelancer.profile')->with('error', 'Password salah');
        }

        $freelancer->delete();
        return redirect()->route('home')->with('success', 'Akun freelancer berhasil dihapus');
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

    /**
     * Verify Freelancer
     */
    public function verify($id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update([
            'status' => 'Approved'
        ]);

        return redirect()->route('admin.freelancers.index')
            ->with('success', 'Freelancer berhasil diverifikasi');
    }

    /**
     * Suspend Freelancer
     */
    public function suspend($id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update([
            'status' => 'Suspended'
        ]);

        return redirect()->route('admin.freelancers.index')
            ->with('success', 'Freelancer berhasil disuspend');
    }

    /**
     * Unsuspend Freelancer
     */
    public function unsuspend($id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update([
            'status' => 'Active'
        ]);

        return redirect()->route('admin.freelancers.index')
            ->with('success', 'Freelancer berhasil diaktifkan kembali');
    }
}
