<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFreelancerRequest;
use App\Http\Requests\UpdateFreelancerRequest;
use App\Models\Freelancer;
use Illuminate\Http\Request;
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

    return view('profile', [
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
    public function update_password(Request $request)
    {
        $freelancer = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $freelancer->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password lama salah'
            ], 422);
        }

        $freelancer->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diperbarui'
        ]);
    }

    /**
     * Get All Freelancers
     */
    public function index()
    {
        $freelancers = Freelancer::with('skomda_student')->get();
        return view('dashboard.admin.freelancers.index', compact('freelancers'));
    }

    public function create()
    {
        return view('dashboard.admin.freelancers.create');
    }

    /**
     * Store New Freelancer
     */
    public function store(StoreFreelancerRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        Freelancer::create($data);
        return redirect()->route('dashboard.admin.freelancers.index')->with('success', 'Akun freelancer berhasil dibuat');
    }

    /**
     * Get Freelancer By ID
     */
    public function show(Freelancer $freelancer)
    {
        $freelancer->load('skomda_student');
        return view('dashboard.admin.freelancers.show', compact('freelancer'));
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
        return redirect()->route('dashboard.admin.freelancers.show', $freelancer->id)->with('success', 'Akun freelancer berhasil diperbarui');
    }

    /**
     * Delete Freelancer By ID
     */
    public function destroy(string $id)
    {
        $freelancer = Freelancer::find($id);

        if (!$freelancer) {
            return response()->json([
                'status' => false,
                'message' => 'Akun freelancer tidak ditemukan'
            ], 404);
        }

        $freelancer->delete();

        return response()->json([
            'status' => true,
            'message' => 'Akun freelancer berhasil dihapus'
        ]);
    }
}
