<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use Illuminate\Http\Request;

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
