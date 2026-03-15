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
    public function profile()
    {
        // Hanya ngambil sesi client
        $user = auth('client')->user();

        return view('profile', [
            'user' => $user,
            'role' => 'Client'
        ]);
    }

    /**
     * Update Client Profile
     */
    public function updateProfile(UpdateClientProfileRequest $request)
    {
        $client = auth('client')->user();
        $client->update($request->validated());

        return redirect()->route('dashboard.client.profile')->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Update Client Password
     */
    public function updatePassword(UpdateClientPasswordRequest $request)
    {
        $client = auth('client')->user();

        if (!Hash::check($request->current_password, $client->password)) {
            return redirect()->route('dashboard.client.profile')->withErrors('Password saat ini salah');
        }

        $client->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('dashboard.client.profile')->with('success', 'Password berhasil diperbarui');
    }

    /**
     * Get All Client.
     */
    public function index()
    {
        $clientsData = Client::paginate(9)->map(fn($c) => [
            'id'          => $c->id,
            'name'        => $c->name,
            'email'       => $c->email,
            'phone'       => $c->phone ?? '-',
            'role'        => 'Client',
            'status'      => 'Active',
            'joinDate'    => $c->created_at?->format('d M Y') ?? '-',
            'location'    => '-',
            'skills'      => [],
            'totalOrders' => 0,
            'totalEarning' => 'Rp 0',
            'lastActive'  => '-',
            'bio'         => '-',
            'avatar'      => 'https://ui-avatars.com/api/?name=' . urlencode($c->name) . '&background=0f766e&color=fff',
        ]);

        return view('dashboard.admin.clients', compact('clientsData'));
    }

    public function create()
    {
        return view('dashboard.admin.clients.create');
    }

    /**
     * Store New Client
     */
    public function store(StoreClientRequest $request)
    {
        Client::create($request->validated());
        return redirect()->route('clients.index')->with('success', 'Akun client berhasil dibuat');
    }

    /**
     * Get Client By ID
     */
    public function show(string $id)
    {
        $client = Client::findOrFail($id);
        return view('dashboard.admin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('dashboard.admin.clients.edit', compact('client'));
    }

    /**
     * Update Client By ID
     */
    public function update(Request $request, Client $client)
    {
        $client->update($request->validated());
        return redirect()->route('clients.index')->with('success', 'Akun client berhasil diperbarui');
    }

    /**
     * Delete Client By ID
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Akun client berhasil dihapus');
    }
}
