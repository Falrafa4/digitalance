<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FreelancerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Freelancer::with('skomda_student')->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:freelancers,email',
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $freelancer = Freelancer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'status' => true,
            'data' => $freelancer
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $freelancer = Freelancer::with('skomda_student')->where('id', $id)->first();

        if (!$freelancer) {
            return response()->json([
                'status' => false,
                'message' => 'Akun freelancer tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $freelancer
        ]);
    }

    public function show_services(string $id)
    {
        $freelancer = Freelancer::with('services', 'skomda_student')->where('id', $id)->first();

        if (!$freelancer) {
            return response()->json([
                'status' => false,
                'message' => 'Akun freelancer tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $freelancer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $freelancer = Freelancer::find($id);

        if (!$freelancer) {
            return response()->json([
                'status' => false,
                'message' => 'Akun freelancer tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:freelancers,email,' . $id,
            'phone' => 'required|string',
        ]);

        $freelancer->update($request->all());

        return response()->json([
            'status' => true,
            'data' => $freelancer
        ]);
    }

    public function update_profile(Request $request)
    {
        $freelancer = $request->user();

        if (!$freelancer) {
            return response()->json([
                'status' => false,
                'message' => 'Akun freelancer tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:freelancers,email,' . $freelancer->id,
            'phone' => 'required|string',
        ]);

        $freelancer->update($request->only(['name', 'email', 'phone']));

        return response()->json([
            'status' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $freelancer
        ]);
    }

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
     * Remove the specified resource from storage.
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
