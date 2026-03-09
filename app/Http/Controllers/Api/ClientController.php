<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    /**
     * Get All Client.
     */
    public function index()
    {
        $data = Client::all();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Store New Client
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'status' => true,
            'data' => $client
        ], 201);
    }

    /**
     * Get Client By ID
     */
    public function show(string $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'Akun client tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $client
        ]);
    }

    /**
     * Update Client By ID
     */
    public function update(Request $request, string $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'Akun client tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:clients,email,' . $id,
            'phone' => 'required|string',
        ]);

        $client->update($request->all());

        return response()->json([
            'status' => true,
            'data' => $client
        ]);
    }

    /**
     * Update Client Profile
     */
    public function update_profile(Request $request)
    {
        $client = $request->user();

        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'Akun client tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'required|string',
        ]);

        $client->update($request->only(['name', 'email', 'phone']));

        return response()->json([
            'status' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $client
        ]);
    }

    /**
     * Update Client Password
     */
    public function update_password(Request $request)
    {
        $client = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $client->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password lama salah'
            ], 422);
        }

        $client->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diperbarui'
        ]);
    }

    /**
     * Delete Client By ID
     */
    public function destroy(string $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'status' => false,
                'message' => 'Akun client tidak ditemukan'
            ], 404);
        }

        $client->delete();

        return response()->json([
            'status' => true,
            'message' => 'Akun client berhasil dihapus'
        ]);
    }
}
