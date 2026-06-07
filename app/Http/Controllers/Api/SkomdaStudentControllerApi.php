<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SkomdaStudentIndexRequest;
use App\Http\Requests\Api\SkomdaStudentStoreRequest;
use App\Http\Requests\Api\SkomdaStudentUpdateRequest;
use App\Http\Resources\SkomdaStudentResource;
use App\Models\SkomdaStudent;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class SkomdaStudentControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get Skomda student list.
     */
    public function index(SkomdaStudentIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $major = trim((string) ($validated['major'] ?? ''));
        $available = $request->filled('available') ? $request->boolean('available') : null;
        $page = (int) ($validated['page'] ?? 1);

        $students = SkomdaStudent::query()
            ->with('freelancer')
            ->when($q !== '', fn($query) => $query->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%")
                    ->orWhere('class', 'like', "%{$q}%");
            }))
            ->when($major !== '', fn($query) => $query->where('major', $major))
            ->when($available === true, fn($query) => $query
                ->where('is_registered', false)
                ->whereDoesntHave('freelancer'))
            ->when($available === false, fn($query) => $query
                ->where(fn($query) => $query
                    ->where('is_registered', true)
                    ->orWhereHas('freelancer')))
            ->latest()
            ->paginate(10, ['*'], 'page', $page)
            ->withQueryString();

        $students->through(fn($student) => (new SkomdaStudentResource($student))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'major' => $major,
                'available' => $available,
                'per_page' => 10,
                'page' => $page,
            ],
            'skomda_students' => $students,
        ], 'Data siswa Skomda berhasil diambil');
    }

    /**
     * Store a new Skomda student.
     */
    public function store(SkomdaStudentStoreRequest $request): JsonResponse
    {
        $student = SkomdaStudent::create($request->validated());

        return $this->successResponse(
            new SkomdaStudentResource($student->fresh('freelancer')),
            'Akun siswa SMK Telkom Sidoarjo berhasil ditambahkan',
            201
        );
    }

    /**
     * Get a single Skomda student.
     */
    public function show(SkomdaStudent $skomdaStudent): JsonResponse
    {
        return $this->successResponse(
            new SkomdaStudentResource($skomdaStudent->load('freelancer')),
            'Data siswa Skomda berhasil diambil'
        );
    }

    /**
     * Update a Skomda student.
     */
    public function update(SkomdaStudentUpdateRequest $request, SkomdaStudent $skomdaStudent): JsonResponse
    {
        $skomdaStudent->update($request->validated());

        return $this->successResponse(
            new SkomdaStudentResource($skomdaStudent->fresh('freelancer')),
            'Akun siswa SMK Telkom Sidoarjo berhasil diperbarui'
        );
    }

    /**
     * Delete a Skomda student.
     */
    public function destroy(SkomdaStudent $skomdaStudent): JsonResponse
    {
        if ($skomdaStudent->freelancer()->exists()) {
            return $this->errorResponse(
                'Siswa tidak dapat dihapus karena masih terhubung dengan akun freelancer.',
                409
            );
        }

        $skomdaStudent->delete();

        return $this->successResponse(null, 'Akun siswa SMK Telkom Sidoarjo berhasil dihapus');
    }

    /**
     * Get Skomda students for freelancer registration/reference.
     */
    public function freelancerIndex(SkomdaStudentIndexRequest $request): JsonResponse
    {
        $request->merge(['available' => true]);

        return $this->index($request);
    }
}
