<?php

namespace App\Http\Controllers;

use App\Models\SkomdaStudent;
use Illuminate\Http\Request;

class SkomdaStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = SkomdaStudent::all();

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
            'email' => 'required|email|unique:skomda_students,email',
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $skomda_student = SkomdaStudent::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'status' => true,
            'data' => $skomda_student
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $skomda_student = SkomdaStudent::find($id);

        if (!$skomda_student) {
            return response()->json([
                'status' => false,
                'message' => 'Akun siswa SMK Telkom Sidoarjo tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $skomda_student
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $skomda_student = SkomdaStudent::find($id);

        if (!$skomda_student) {
            return response()->json([
                'status' => false,
                'message' => 'Akun siswa SMK Telkom Sidoarjo tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:skomda_students,email,' . $id,
            'phone' => 'required|string',
        ]);

        $skomda_student->update($request->all());

        return response()->json([
            'status' => true,
            'data' => $skomda_student
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $skomda_student = SkomdaStudent::find($id);

        if (!$skomda_student) {
            return response()->json([
                'status' => false,
                'message' => 'Akun siswa SMK Telkom Sidoarjo tidak ditemukan'
            ], 404);
        }

        $skomda_student->delete();

        return response()->json([
            'status' => true,
            'message' => 'Akun siswa SMK Telkom Sidoarjo berhasil dihapus'
        ]);
    }
}
