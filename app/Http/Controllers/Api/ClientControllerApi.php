<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserStoreRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Http\Requests\UpdateClientPasswordRequest;
use App\Http\Requests\UpdateClientProfileRequest;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\SkomdaStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientControllerApi extends Controller
{
    /**
     * Get All Data for User Management (Admin Dashboard)
     */
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));
        $role = $request->query('role', 'all');

        $clientsQuery = Client::query()->select('id', 'name', 'email', 'phone', DB::raw("'Client' as role"), DB::raw("'Active' as status"), 'created_at');
        $skomdaQuery = SkomdaStudent::query()->select('id', 'name', 'email', 'phone', DB::raw("'Skomda Student' as role"), DB::raw("'Active' as status"), 'created_at');
        // Use the query builder so Freelancer::getNameAttribute() does not override the joined student name.
        $freelancersQuery = DB::table('freelancers')
            ->join('skomda_students', 'freelancers.student_id', '=', 'skomda_students.id')
            ->select('freelancers.id', 'skomda_students.name', 'skomda_students.email', 'skomda_students.phone', DB::raw("'Freelancer' as role"), 'freelancers.status', 'freelancers.created_at');

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
        $skomdaAll = SkomdaStudent::select('id', 'name', 'nis')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data users berhasil diambil',
            'data' => [
                'q' => $q,
                'role' => $role,
                'users' => $users,
                'skomdaAll' => $skomdaAll,
            ],
        ]);
    }

    /**
     * Store Client Data
     */
    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $client = Client::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Klien berhasil ditambahkan',
            'data' => $client,
        ], 201);
    }

    /**
     * Get Single Client Data
     */
    public function show(string $id)
    {
        $client = Client::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data klien berhasil diambil',
            'data' => $client,
        ]);
    }

    /**
     * Update Client Data
     */
    public function update(UserUpdateRequest $request, Client $client)
    {
        $client->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Akun klien berhasil diperbarui',
            'data' => $client,
        ]);
    }

    /**
     * Delete Client Data
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Akun klien berhasil dihapus',
        ]);
    }

    // CLIENT SELF-SERVICE (PROFILE & PASSWORD)
    /**
     * Display the client's profile.
     */
    public function profile()
    {
        $user = auth('client')->user();

        return response()->json([
            'status' => 'success',
            'message' => 'Data profil berhasil diambil',
            'data' => [
                'user' => $user,
                'role' => 'Client',
            ],
        ]);
    }

    /**
     * Update the client's profile.
     */
    public function updateProfile(UpdateClientProfileRequest $request)
    {
        /** @var Client $client */
        $client = auth('client')->user();
        $client->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data' => $client,
        ]);
    }

    /**
     * Update the client's password.
     */
    public function updatePassword(UpdateClientPasswordRequest $request)
    {
        /** @var Client $client */
        $client = auth('client')->user();

        if (! Hash::check($request->current_password, $client->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password saat ini salah',
            ], 422);
        }

        $client->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diperbarui',
            'data' => $client,
        ]);
    }

    // ==========================================
    // ADMIN-ONLY (MANAGE ANY CLIENT)
    // ==========================================
    /**
     * Update a client's password.
     */
    public function updateClientPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $client = Client::findOrFail($id);
        $client->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password ' . $client->name . ' berhasil diperbarui',
            'data' => $client,
        ]);
    }

    /**
     * Update a freelancer's password.
     */
    public function updateFreelancerPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $freelancer = Freelancer::findOrFail($id);
        $freelancer->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password ' . ($freelancer->skomda_student->name ?? 'Freelancer') . ' berhasil diperbarui',
            'data' => $freelancer,
        ]);
    }

    /**
     * Update a Skomda student's password.
     */
    public function updateSkomdaPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $skomdaStudent = \App\Models\SkomdaStudent::findOrFail($id);
        $skomdaStudent->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password ' . $skomdaStudent->name . ' berhasil diperbarui',
            'data' => $skomdaStudent,
        ]);
    }

    // ==========================================
    // FREELANCER ONLY
    // ==========================================

    /**
     * Get all clients (for freelancers to view potential clients).
     */
    public function freelancerIndex()
    {
        $clients = Client::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Data klien berhasil diambil',
            'data' => $clients,
        ]);
    }
}
