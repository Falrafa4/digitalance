<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkomdaStudentRequest;
use App\Http\Requests\UpdateSkomdaStudentRequest;
use App\Models\SkomdaStudent;
use Illuminate\Http\Request;

class SkomdaStudentController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        return redirect()->route('admin.clients.index', ['role' => 'Skomda Student']);
    }

    public function store(StoreSkomdaStudentRequest $request)
    {
        $skomdaStudent = SkomdaStudent::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun siswa SMK Telkom Sidoarjo berhasil ditambahkan'], 201);
        }

        return redirect()->route('admin.clients.index', ['role' => 'Skomda Student'])->with('success', 'Akun siswa SMK Telkom Sidoarjo berhasil ditambahkan');
    }

    public function show(string $id)
    {
        return redirect()->route('admin.clients.index', ['role' => 'Skomda Student']);
    }

    public function update(UpdateSkomdaStudentRequest $request, string $id)
    {
        $skomdaStudent = SkomdaStudent::findOrFail($id);
        $skomdaStudent->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun siswa SMK Telkom Sidoarjo berhasil diperbarui'], 200);
        }

        return redirect()->route('admin.clients.index', ['role' => 'Skomda Student'])->with('success', 'Akun siswa SMK Telkom Sidoarjo berhasil diperbarui');
    }

    public function destroy(Request $request, string $id)
    {
        $skomdaStudent = SkomdaStudent::findOrFail($id);
        $skomdaStudent->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun siswa SMK Telkom Sidoarjo berhasil dihapus'], 200);
        }

        return redirect()->route('admin.clients.index', ['role' => 'Skomda Student'])->with('success', 'Akun siswa SMK Telkom Sidoarjo berhasil dihapus');
    }

    // FREELANCER ONLY
    public function freelancerIndex()
    {
        // Freelancer hanya bisa melihat data siswa, tidak bisa melakukan CRUD
        // data siswa untuk mendaftarkan diri mereka ke freelancer, jadi freelancer harus berasal dari siswa SMK Telkom Sidoarjo
        $skomdaStudents = SkomdaStudent::paginate(10);
        return view('dashboard.freelancer.skomda_students', compact('skomdaStudents'));
    }
}
