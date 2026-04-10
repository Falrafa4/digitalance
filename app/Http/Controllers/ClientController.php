<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientPasswordRequest;
use App\Http\Requests\UpdateClientProfileRequest;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\SkomdaStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    // ADMIN ONLY (CRUD & MANAGEMENT)

    /**
     * Get All Data for User Management (Admin Dashboard)
     */
    public function index()
    {
        $clientsData = Client::all()->transform(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'email' => $c->email,
            'phone' => $c->phone,
            'role' => 'Client',
            'status' => 'Active',
            'joinDate' => $c->created_at?->format('d M Y') ?? '-',
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($c->name) . '&background=0f766e&color=fff',
        ]);

        $freelancersData = Freelancer::with('skomda_student')->get()
            ->map(function ($f) {
                return [
                    'id' => $f->id,
                    'name' => $f->skomda_student->name ?? '-',
                    'email' => $f->skomda_student->email ?? '-',
                    'phone' => $f->skomda_student->phone ?? '-',
                    'location' => $f->skomda_student->location ?? 'Unknown',
                    'skills' => $f->skills ?? [],
                    'role' => 'Freelancer',
                    'status' => ucfirst($f->status ?? 'Pending'),
                    'joinDate' => $f->created_at?->format('d M Y') ?? '-',
                    'avatar' => 'https://ui-avatars.com/api/?name='
                        . urlencode($f->skomda_student->name ?? 'F')
                        . '&background=0f766e&color=fff',
                ];
            });

        $skomdaData = SkomdaStudent::all()
            ->transform(fn($s) => [
                'id' => $s->id,
                'nis' => $s->nis,
                'name' => $s->name,
                'email' => $s->email,
                'class' => $s->class,
                'major' => $s->major,
                'phone' => $s->phone ?? '-',
                'role' => 'Skomda Student',
                'status' => 'Active',
                'joinDate' => $s->created_at?->format('d M Y') ?? '-',
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($s->name) . '&background=0f766e&color=fff',
            ]);

        return view('dashboard.admin.clients', [
            'clientsData' => ['data' => $clientsData],
            'freelancersData' => ['data' => $freelancersData],
            'skomdaData' => ['data' => $skomdaData],
        ]);
    }

    public function store(StoreClientRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        Client::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun client berhasil dibuat'], 201);
        }

        return redirect()->route('admin.clients.index')->with('success', 'Akun client berhasil dibuat');
    }

    public function show(string $id)
    {
        $client = Client::findOrFail($id);
        return view('dashboard.admin.clients', compact('client'));
    }

    public function update(UpdateClientProfileRequest $request, Client $client)
    {
        $client->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun client berhasil diperbarui'], 200);
        }

        return redirect()->route('admin.clients.index')->with('success', 'Akun client berhasil diperbarui');
    }

    public function destroy(Request $request, Client $client)
    {
        $client->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun client berhasil dihapus'], 200);
        }

        return redirect()->route('admin.clients.index')->with('success', 'Akun client berhasil dihapus');
    }

    // CLIENT SELF-SERVICE (PROFILE & PASSWORD)

    public function profile()
    {
        $user = auth('client')->user();

        return view('dashboard.client.profile', [
            'user' => $user,
            'role' => 'Client'
        ]);
    }

    public function updateProfile(UpdateClientProfileRequest $request)
    {
        /** @var Client $client */
        $client = auth('client')->user();
        $client->update($request->validated());

        return redirect()->route('dashboard.client.profile')->with('success', 'Profil berhasil diperbarui');
    }

    public function updatePassword(UpdateClientPasswordRequest $request)
    {
        /** @var Client $client */
        $client = auth('client')->user();

        if (!Hash::check($request->current_password, $client->password)) {
            return redirect()->route('dashboard.client.profile')->withErrors('Password saat ini salah');
        }

        $client->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('dashboard.client.profile')->with('success', 'Password berhasil diperbarui');
    }

    // ==========================================
    // FREELANCER ONLY
    // ==========================================

    public function freelancerIndex()
    {
        $clients = Client::all();
        return view('dashboard.freelancer.clients', compact('clients'));
    }
}