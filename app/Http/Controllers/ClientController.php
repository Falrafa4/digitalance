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
    // ADMIN ONLY (CRUD)
    public function index()
    {
        $clientsData = Client::paginate(3);
        $clientsData->getCollection()->transform(fn($c) => [
            'id'          => $c->id,
            'name'        => $c->name,
            'email'       => $c->email,
            'phone'       => $c->phone,
            'role'        => 'Client',
            'status'      => 'Active',
            'joinDate'    => $c->created_at?->format('d M Y') ?? '-',
            'avatar'      => 'https://ui-avatars.com/api/?name=' . urlencode($c->name) . '&background=0f766e&color=fff',
        ]);

        $freelancersData = Freelancer::with('skomda_student')->paginate(3);
        $freelancersData->getCollection()->transform(fn($f) => [
            'id'          => $f->id,
            'name'        => $f->skomda_student->name ?? '-',
            'email'       => $f->skomda_student->email ?? '-',
            'phone'       => $f->skomda_student->phone ?? '-',
            'role'        => 'Freelancer',
            'status'      => ucfirst($f->status ?? 'Pending'),
            'joinDate'    => $f->created_at?->format('d M Y') ?? '-',
            'avatar'      => 'https://ui-avatars.com/api/?name=' . urlencode($f->skomda_student->name ?? 'F') . '&background=0f766e&color=fff',
        ]);

        $skomdaData = SkomdaStudent::paginate(3);
        $skomdaData->getCollection()->transform(fn($s) => [
            'id'          => $s->id,
            'name'        => $s->name,
            'email'       => $s->email,
            'phone'       => $s->phone ?? '-',
            'role'        => 'Skomda Student',
            'status'      => 'Active',
            'joinDate'    => $s->created_at?->format('d M Y') ?? '-',
            'avatar'      => 'https://ui-avatars.com/api/?name=' . urlencode($s->name) . '&background=0f766e&color=fff',
        ]);

        return view('dashboard.admin.clients', compact('clientsData', 'freelancersData', 'skomdaData'));
    }

    public function store(StoreClientRequest $request)
    {
        Client::create($request->validated());
        return redirect()->route('admin.clients.index')->with('success', 'Akun client berhasil dibuat');
    }

    public function show(string $id)
    {
        $client = Client::findOrFail($id);
        return view('dashboard.admin.clients', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $client->update($request->validated());
        return redirect()->route('admin.clients.index')->with('success', 'Akun client berhasil diperbarui');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Akun client berhasil dihapus');
    }

    // CLIENT ONLY (profil & update profil & update password)
    public function profile()
    {
        // Hanya ngambil sesi client
        $user = auth('client')->user();

        return view('dashboard.client.profile', [
            'user' => $user,
            'role' => 'Client'
        ]);
    }

    public function updateProfile(UpdateClientProfileRequest $request)
    {
        $client = auth('client')->user();
        $client->update($request->validated());

        return redirect()->route('dashboard.client.profile')->with('success', 'Profil berhasil diperbarui');
    }

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

    // FREELANCER ONLY (lihat daftar client)
    public function freelancerIndex()
    {
        // untuk ditampilkan di halaman freelancer, agar freelancer bisa melihat daftar client yang ada
        $clients = Client::all();
        return view('dashboard.freelancer.clients', compact('clients'));
    }
}
