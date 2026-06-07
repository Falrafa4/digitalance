<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PortofolioIndexRequest;
use App\Http\Requests\Api\PortofolioStoreRequest;
use App\Http\Requests\Api\PortofolioUpdateRequest;
use App\Http\Resources\PortofolioResource;
use App\Models\Freelancer;
use App\Models\Portofolio;
use App\Models\Service;
use App\Support\ImageStorage;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortofolioControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get all portofolios for administrator.
     */
    public function index(PortofolioIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $serviceId = $validated['service_id'] ?? null;
        $page = (int) ($validated['page'] ?? 1);

        $portofolios = Portofolio::query()
            ->with(['service.category:id,name', 'service.freelancer.skomda_student:id,name,email'])
            ->when($serviceId, fn($query) => $query->where('service_id', $serviceId))
            ->when($q !== '', fn($query) => $query->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('service', fn($serviceQuery) => $serviceQuery->where('title', 'like', "%{$q}%"))
                    ->orWhereHas('service.freelancer.skomda_student', fn($studentQuery) => $studentQuery->where('name', 'like', "%{$q}%"));
            }))
            ->latest()
            ->paginate(10, ['*'], 'page', $page)
            ->withQueryString();

        $portofolios->through(fn($portofolio) => (new PortofolioResource($portofolio))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'service_id' => $serviceId,
                'per_page' => 10,
                'page' => $page,
            ],
            'portofolios' => $portofolios,
        ], 'Data portofolio berhasil diambil');
    }

    /**
     * Get authenticated freelancer portofolios.
     */
    public function freelancerIndex(Request $request): JsonResponse
    {
        $freelancer = $request->user();

        $portofolios = Portofolio::query()
            ->with('service.category:id,name')
            ->whereHas('service', fn($query) => $query->where('freelancer_id', $freelancer->id))
            ->latest()
            ->get()
            ->map(fn($portofolio) => (new PortofolioResource($portofolio))->toArray($request))
            ->values();

        $services = $freelancer->services()
            ->with('category:id,name')
            ->latest()
            ->get();

        return $this->successResponse([
            'portofolios' => $portofolios,
            'services' => $services,
        ], 'Data portofolio freelancer berhasil diambil');
    }

    /**
     * Store a new portofolio for authenticated freelancer.
     */
    public function store(PortofolioStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $freelancer = $request->user();

        $service = Service::query()
            ->where('id', $validated['service_id'])
            ->where('freelancer_id', $freelancer->id)
            ->first();

        if (! $service) {
            return $this->errorResponse('Layanan tidak ditemukan atau bukan milik Anda.', 404);
        }

        if ($request->hasFile('media_file')) {
            $validated['media_url'] = ImageStorage::storeAsWebp($request->file('media_file'), 'portofolios');
        }

        unset($validated['media_file']);

        $portofolio = Portofolio::create($validated);

        return $this->successResponse(
            new PortofolioResource($portofolio->fresh(['service.category', 'service.freelancer.skomda_student'])),
            'Portofolio berhasil ditambahkan',
            201
        );
    }

    /**
     * Get one portofolio for authenticated freelancer.
     */
    public function show(Request $request, Portofolio $portofolio): JsonResponse
    {
        if (! $this->belongsToFreelancer($portofolio, $request->user())) {
            return $this->errorResponse('Anda tidak memiliki akses ke portofolio ini', 403);
        }

        return $this->successResponse(
            new PortofolioResource($portofolio->load(['service.category', 'service.freelancer.skomda_student'])),
            'Data portofolio berhasil diambil'
        );
    }

    /**
     * Get one portofolio for administrator.
     */
    public function adminShow(Portofolio $portofolio): JsonResponse
    {
        return $this->successResponse(
            new PortofolioResource($portofolio->load(['service.category', 'service.freelancer.skomda_student'])),
            'Data portofolio berhasil diambil'
        );
    }

    /**
     * Update a portofolio for authenticated freelancer.
     */
    public function update(PortofolioUpdateRequest $request, Portofolio $portofolio): JsonResponse
    {
        if (! $this->belongsToFreelancer($portofolio, $request->user())) {
            return $this->errorResponse('Anda tidak memiliki akses untuk mengedit portofolio ini', 403);
        }

        $validated = $this->validatedUpdateData($request, $portofolio, $request->user());
        $portofolio->update($validated);

        return $this->successResponse(
            new PortofolioResource($portofolio->fresh(['service.category', 'service.freelancer.skomda_student'])),
            'Portofolio berhasil diperbarui'
        );
    }

    /**
     * Delete a portofolio for authenticated freelancer.
     */
    public function destroy(Request $request, Portofolio $portofolio): JsonResponse
    {
        if (! $this->belongsToFreelancer($portofolio, $request->user())) {
            return $this->errorResponse('Anda tidak memiliki akses untuk menghapus portofolio ini', 403);
        }

        $this->deleteLocalMedia($portofolio->media_url);
        $portofolio->delete();

        return $this->successResponse(null, 'Portofolio berhasil dihapus');
    }

    /**
     * Update a portofolio for administrator.
     */
    public function adminUpdate(PortofolioUpdateRequest $request, Portofolio $portofolio): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('media_file')) {
            $this->deleteLocalMedia($portofolio->media_url);
            $validated['media_url'] = ImageStorage::storeAsWebp($request->file('media_file'), 'portofolios');
        } elseif (! array_key_exists('media_url', $validated) || ! $validated['media_url']) {
            $validated['media_url'] = $portofolio->media_url;
        }

        unset($validated['media_file']);
        $portofolio->update($validated);

        return $this->successResponse(
            new PortofolioResource($portofolio->fresh(['service.category', 'service.freelancer.skomda_student'])),
            'Portofolio berhasil diperbarui oleh Admin'
        );
    }

    /**
     * Delete a portofolio for administrator.
     */
    public function adminDestroy(Portofolio $portofolio): JsonResponse
    {
        $this->deleteLocalMedia($portofolio->media_url);
        $portofolio->delete();

        return $this->successResponse(null, 'Portofolio berhasil dihapus oleh Admin');
    }

    /**
     * Get all portofolios for a freelancer.
     */
    public function showAllFreelancerPortofolios(Freelancer $freelancer): JsonResponse
    {
        $portofolios = Portofolio::query()
            ->with(['service.category', 'service.freelancer.skomda_student'])
            ->whereHas('service', fn($query) => $query->where('freelancer_id', $freelancer->id))
            ->latest()
            ->get()
            ->map(fn($portofolio) => (new PortofolioResource($portofolio))->toArray(request()))
            ->values();

        return $this->successResponse($portofolios, 'Data portofolio freelancer berhasil diambil');
    }

    /**
     * Get one portofolio for client.
     */
    public function showFreelancerPortofolio(Portofolio $portofolio): JsonResponse
    {
        return $this->successResponse(
            new PortofolioResource($portofolio->load(['service.category', 'service.freelancer.skomda_student'])),
            'Data portofolio berhasil diambil'
        );
    }

    private function belongsToFreelancer(Portofolio $portofolio, ?Freelancer $freelancer): bool
    {
        if (! $freelancer) {
            return false;
        }

        $portofolio->loadMissing('service');

        return (int) $portofolio->service?->freelancer_id === (int) $freelancer->id;
    }

    private function validatedUpdateData(PortofolioUpdateRequest $request, Portofolio $portofolio, Freelancer $freelancer): array
    {
        $validated = $request->validated();

        $service = Service::query()
            ->where('id', $validated['service_id'])
            ->where('freelancer_id', $freelancer->id)
            ->first();

        if (! $service) {
            abort(response()->json([
                'success' => false,
                'message' => 'Layanan tidak ditemukan atau bukan milik Anda.',
                'errors' => null,
            ], 404));
        }

        if ($request->hasFile('media_file')) {
            $this->deleteLocalMedia($portofolio->media_url);
            $validated['media_url'] = ImageStorage::storeAsWebp($request->file('media_file'), 'portofolios');
        } elseif (! array_key_exists('media_url', $validated) || ! $validated['media_url']) {
            $validated['media_url'] = $portofolio->media_url;
        }

        unset($validated['media_file']);

        return $validated;
    }

    private function deleteLocalMedia(?string $path): void
    {
        if (! $path || filter_var($path, FILTER_VALIDATE_URL)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
