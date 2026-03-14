<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientPasswordRequest;
use App\Http\Requests\UpdateClientProfileRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function profile()
    {
        $client = auth('client')->user();
        return view('dashboard.client.profile', compact('client'));
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
        $clients = Client::all();
        return view('dashboard.admin.clients.index', compact('clients'));
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
