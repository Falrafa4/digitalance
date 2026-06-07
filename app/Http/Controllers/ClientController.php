<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientPasswordRequest;
use App\Http\Requests\UpdateClientProfileRequest;
use App\Models\Client;
use App\Models\SkomdaStudent;
use App\Support\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    private const DEFAULT_PROFILE_PHOTO = 'profiles/placeholder.webp';

    // ADMIN ONLY (CRUD & MANAGEMENT)

    /**
     * Get All Data for User Management (Admin Dashboard)
     */
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));
        $role = $this->normalizeAdminUserRole($request->query('role', 'all'));

        $clientsQuery = Client::query()->select('id', 'name', 'email', 'phone', 'profile_photo', DB::raw('NULL as avatar'), DB::raw("'Client' as role"), DB::raw("'Active' as status"), 'created_at');
        $skomdaQuery = SkomdaStudent::query()
            ->where('is_registered', false)
            ->whereDoesntHave('freelancer')
            ->select('id', 'name', 'email', 'phone', DB::raw('NULL as profile_photo'), DB::raw('NULL as avatar'), DB::raw("'Skomda Student' as role"), DB::raw("'Active' as status"), 'created_at');
        // Use the query builder so Freelancer::getNameAttribute() does not override the joined student name.
        $freelancersQuery = DB::table('freelancers')
            ->join('skomda_students', 'freelancers.student_id', '=', 'skomda_students.id')
            ->select('freelancers.id', 'skomda_students.name', 'skomda_students.email', 'skomda_students.phone', 'freelancers.profile_photo', DB::raw('NULL as avatar'), DB::raw("'Freelancer' as role"), 'freelancers.status', 'freelancers.created_at');

        if ($q !== '') {
            $clientsQuery->where(fn($query) => $query
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%"));

            $skomdaQuery->where(fn($query) => $query
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('nis', 'like', "%{$q}%"));

            $freelancersQuery->where(fn($query) => $query
                ->where('skomda_students.name', 'like', "%{$q}%")
                ->orWhere('skomda_students.email', 'like', "%{$q}%")
                ->orWhere('skomda_students.nis', 'like', "%{$q}%"));
        }

        if ($role === 'Client') {
            $users = $clientsQuery->latest()->paginate(12)->withQueryString();
        } elseif ($role === 'Freelancer') {
            $users = $freelancersQuery->orderByDesc('freelancers.created_at')->paginate(12)->withQueryString();
        } elseif ($role === 'Skomda Student') {
            $users = $skomdaQuery->latest()->paginate(12)->withQueryString();
        } else {
            // Combined using Union
            $combined = $clientsQuery->union($skomdaQuery)->union($freelancersQuery);
            $users = DB::table(DB::raw("({$combined->toSql()}) as combined"))
                ->mergeBindings($combined->getQuery())
                ->orderBy('created_at', 'desc')
                ->paginate(12)
                ->withQueryString();
        }

        // We still need skomdaData for the 'Add Freelancer' dropdown
        $skomdaAll = SkomdaStudent::where('is_registered', false)
            ->whereDoesntHave('freelancer')
            ->select('id', 'name', 'nis')
            ->orderBy('name')
            ->get();

        return view('dashboard.admin.clients', [
            'users' => $users,
            'role' => $role,
            'q' => $q,
            'skomdaAll' => $skomdaAll,
        ]);
    }

    private function normalizeAdminUserRole(?string $role): string
    {
        $normalized = trim((string) $role);

        return match ($normalized) {
            'Client', 'Freelancer', 'Skomda Student', 'all' => $normalized,
            'Siswa Skomda', 'Skomda Students', 'Skomda', 'skomda student' => 'Skomda Student',
            default => 'all',
        };
    }

    public function store(StoreClientRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $validated['profile_photo'] = $this->storeProfilePhoto($request) ?? self::DEFAULT_PROFILE_PHOTO;

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
        $validated = $request->validated();

        if ($profilePhoto = $this->storeProfilePhoto($request)) {
            $this->deleteProfilePhoto($client->profile_photo);
            $validated['profile_photo'] = $profilePhoto;
        }

        $client->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun client berhasil diperbarui'], 200);
        }

        return redirect()->route('admin.clients.index')->with('success', 'Akun client berhasil diperbarui');
    }

    public function destroy(Request $request, Client $client)
    {
        $this->deleteProfilePhoto($client->profile_photo);
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
            'role' => 'Client',
        ]);
    }

    public function settings()
    {
        $user = auth('client')->user();

        return view('dashboard.client.settings', [
            'user' => $user,
            'role' => 'Client',
        ]);
    }

    public function updateProfile(UpdateClientProfileRequest $request)
    {
        /** @var Client $client */
        $client = auth('client')->user();
        $validated = $request->validated();

        if ($profilePhoto = $this->storeProfilePhoto($request)) {
            $this->deleteProfilePhoto($client->profile_photo);
            $validated['profile_photo'] = $profilePhoto;
        }

        $client->update($validated);

        return redirect()->route('client.profile')->with('success', 'Profil berhasil diperbarui');
    }

    public function updatePassword(UpdateClientPasswordRequest $request)
    {
        /** @var Client $client */
        $client = auth('client')->user();

        if (!Hash::check($request->current_password, $client->password)) {
            return redirect()->route('client.profile')->withErrors('Password saat ini salah');
        }

        $client->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('client.profile')->with('success', 'Password berhasil diperbarui');
    }

    public function updateClientPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $client = Client::findOrFail($id);
        $client->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.clients.index')->with('success', 'Password ' . $client->name . ' berhasil diperbarui');
    }

    public function updateFreelancerPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $freelancer = \App\Models\Freelancer::findOrFail($id);
        $freelancer->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.clients.index', ['role' => 'Freelancer'])
            ->with('success', 'Password ' . ($freelancer->skomda_student->name ?? 'Freelancer') . ' berhasil diperbarui');
    }

    public function updateSkomdaPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $skomdaStudent = \App\Models\SkomdaStudent::findOrFail($id);
        $skomdaStudent->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.clients.index', ['role' => 'Skomda Student'])
            ->with('success', 'Password ' . $skomdaStudent->name . ' berhasil diperbarui');
    }

    // ==========================================
    // FREELANCER ONLY
    // ==========================================

    public function freelancerIndex()
    {
        $clients = Client::all();

        return view('dashboard.freelancer.clients', compact('clients'));
    }

    private function storeProfilePhoto(Request $request): ?string
    {
        if (!$request->hasFile('profile_photo')) {
            return null;
        }

        return ImageStorage::storeAsWebp($request->file('profile_photo'), 'profiles');
    }

    private function deleteProfilePhoto(?string $path): void
    {
        if (!$path || $path === self::DEFAULT_PROFILE_PHOTO) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
