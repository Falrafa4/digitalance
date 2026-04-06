<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkomdaStudentRequest;
use App\Http\Requests\UpdateSkomdaStudentRequest;
use App\Models\SkomdaStudent;

class SkomdaStudentController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        $skomdaStudents = SkomdaStudent::all();
        return view('dashboard.admin.skomda_students', compact('skomdaStudents'));
    }

    public function store(StoreSkomdaStudentRequest $request)
    {
        $request->validated();

        SkomdaStudent::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('admin.skomda-students.index')->with('success', 'Akun siswa SMK Telkom Sidoarjo berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $skomdaStudent = SkomdaStudent::findOrFail($id);
        return view('dashboard.admin.skomda_students', compact('skomdaStudent'));
    }

    public function update(UpdateSkomdaStudentRequest $request, string $id)
    {
        $skomdaStudent = SkomdaStudent::findOrFail($id);
        $skomdaStudent->update($request->validated());

        return redirect()->route('admin.skomda-students.index')->with('success', 'Akun siswa SMK Telkom Sidoarjo berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $skomdaStudent = SkomdaStudent::findOrFail($id);
        $skomdaStudent->delete();

        return redirect()->route('admin.skomda-students.index')->with('success', 'Akun siswa SMK Telkom Sidoarjo berhasil dihapus');
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
