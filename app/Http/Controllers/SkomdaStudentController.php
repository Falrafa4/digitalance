<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkomdaStudentRequest;
use App\Http\Requests\UpdateSkomdaStudentRequest;
use App\Models\SkomdaStudent;

class SkomdaStudentController extends Controller
{
    /**
     * Get All Skomda Students
     */
    public function index()
    {
        $skomdaStudents = SkomdaStudent::paginate(10);
        return view('dashboard.admin.skomda_students', compact('skomdaStudents'));
    }

    /**
     * Store New Skomda Student
     */
    public function store(StoreSkomdaStudentRequest $request)
    {
        $request->validated();

        SkomdaStudent::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        return view('dashboard.admin.skomda_students', [
            'skomdaStudents' => SkomdaStudent::paginate(10),
            'success' => 'Akun siswa SMK Telkom Sidoarjo berhasil ditambahkan'
        ]);
    }

    /**
     * Get Skomda Student By ID
     */
    public function show(string $id)
    {
        $skomda_student = SkomdaStudent::findOrFail($id);
        return view('dashboard.admin.skomda_students.show', compact('skomda_student'));
    }

    /**
     * Update Skomda Student By ID
     */
    public function update(UpdateSkomdaStudentRequest $request, string $id)
    {
        $skomda_student = SkomdaStudent::findOrFail($id);
        $skomda_student->update($request->validated());

        return view('dashboard.admin.skomda_students', [
            'skomdaStudents' => SkomdaStudent::paginate(10),
            'success' => 'Akun siswa SMK Telkom Sidoarjo berhasil diperbarui'
        ]);
    }

    /**
     * Delete Skomda Student By ID
     */
    public function destroy(string $id)
    {
        $skomda_student = SkomdaStudent::findOrFail($id);
        $skomda_student->delete();

        return view('dashboard.admin.skomda_students', [
            'skomdaStudents' => SkomdaStudent::paginate(10),
            'success' => 'Akun siswa SMK Telkom Sidoarjo berhasil dihapus'
        ]);
    }
}
