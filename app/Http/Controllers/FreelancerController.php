<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFreelancerRequest;
use App\Http\Requests\UpdateFreelancerPasswordRequest;
use App\Http\Requests\UpdateFreelancerRequest;
use App\Models\Freelancer;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FreelancerController extends Controller
{
    // =========================
    // FREELANCER ONLY
    // =========================
    public function profile()
    {
        $freelancer = auth('freelancer')->user();

        $freelancer->load('skomda_student');

        $freelancer->name = $freelancer->skomda_student->name ?? 'Siswa Skomda';
        $freelancer->email = $freelancer->skomda_student->email ?? '-';

        return view('dashboard.freelancer.profile', [
            'user' => $freelancer,
            'role' => 'Freelancer',
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

        if (! Hash::check($request->current_password, $freelancer->password)) {
            return redirect()->route('freelancer.profile')->with('error', 'Password lama salah');
        }

        $freelancer->password = Hash::make($request->password);
        $freelancer->save();

        return redirect()->route('freelancer.profile')->with('success', 'Password berhasil diperbarui');
    }

    public function deleteAccount(Request $request)
    {
        $freelancer = auth('freelancer')->user();

        if (! Hash::check($request->password, $freelancer->password)) {
            return redirect()->route('freelancer.profile')->with('error', 'Password salah');
        }

        $freelancer->delete();

        return redirect()->route('home')->with('success', 'Akun freelancer berhasil dihapus');
    }

    // =========================
    // ADMIN ONLY
    // =========================
    public function index(Request $request)
    {
        return redirect()->route('admin.clients.index', ['role' => 'Freelancer']);
    }

    public function store(StoreFreelancerRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        Freelancer::create($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun freelancer berhasil dibuat'], 201);
        }

        return redirect()->route('admin.freelancers.index')->with('success', 'Akun freelancer berhasil dibuat');
    }

    public function show(Freelancer $freelancer)
    {
        $freelancer->load('skomda_student');

        return view('dashboard.admin.freelancers', compact('freelancer'));
    }

    public function showServices(Freelancer $freelancer)
    {
        $freelancer->load(['services', 'skomda_student', 'services.category']);

        return view('dashboard.admin.freelancers.services', compact('freelancer'));
    }

    public function update(UpdateFreelancerRequest $request, Freelancer $freelancer)
    {
        $validated = $request->validated();

        // Update Freelancer fields
        $freelancer->update([
            'bio' => $validated['bio'] ?? $freelancer->bio,
            'status' => $validated['status'] ?? $freelancer->status,
        ]);

        // Update linked SkomdaStudent fields
        if ($freelancer->skomda_student) {
            $studentData = [];
            if (isset($validated['name'])) {
                $studentData['name'] = $validated['name'];
            }
            if (isset($validated['email'])) {
                $studentData['email'] = $validated['email'];
            }
            if (isset($validated['phone'])) {
                $studentData['phone'] = $validated['phone'];
            }

            if (! empty($studentData)) {
                $freelancer->skomda_student->update($studentData);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun freelancer berhasil diperbarui'], 200);
        }

        return redirect()->route('admin.freelancers.index')->with('success', 'Akun freelancer berhasil diperbarui');
    }

    public function destroy(Request $request, string $id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun freelancer berhasil dihapus'], 200);
        }

        return redirect()->route('admin.freelancers.index')->with('success', 'Akun freelancer berhasil dihapus');
    }

    public function verify($id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update([
            'status' => 'Approved',
        ]);

        return redirect()->route('admin.freelancers.index')
            ->with('success', 'Freelancer berhasil diverifikasi');
    }

    public function suspend($id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update([
            'status' => 'Suspended',
        ]);

        return redirect()->route('admin.freelancers.index')
            ->with('success', 'Freelancer berhasil disuspend');
    }

    public function unsuspend($id)
    {
        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update([
            'status' => 'Approved',
        ]);

        return redirect()->route('admin.freelancers.index')
            ->with('success', 'Freelancer berhasil diaktifkan kembali');
    }

    // =========================
    // CLIENT ONLY (Find Talent)
    // =========================

    public function clientFindTalent()
    {
        $freelancers = Freelancer::with('skomda_student')
            ->where('status', 'Approved')
            ->latest()
            ->get();

        foreach ($freelancers as $f) {
            $f->services_count = Service::where('freelancer_id', $f->id)
                ->where('status', 'Approved')
                ->count();
        }

        return view('dashboard.client.talents.find-talent', compact('freelancers'));
    }

    /**
     * CLIENT: Talent detail (profil + list services)
     */
    public function clientTalentShow(Freelancer $freelancer)
    {
        if ($freelancer->status !== 'Approved') {
            return redirect()->route('client.talents.index')->with('warning', 'Freelancer tidak tersedia.');
        }

        $freelancer->load('skomda_student');

        $services = Service::with('service_category:id,name')
            ->where('freelancer_id', $freelancer->id)
            ->where('status', 'Approved')
            ->latest()
            ->get();

        return view('dashboard.client.talents.talent-show', compact('freelancer', 'services'));
    }
}
