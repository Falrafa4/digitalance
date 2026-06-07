<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ServiceIndexRequest;
use App\Http\Requests\Api\ServiceStatusUpdateRequest;
use App\Http\Requests\Api\ServiceStoreRequest;
use App\Http\Requests\Api\ServiceUpdateRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Notification;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get all services for administrator.
     */
    public function index(ServiceIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $status = trim((string) ($validated['status'] ?? ''));
        $page = (int) ($validated['page'] ?? 1);

        $services = Service::query()
            ->with(['category:id,name', 'freelancer.skomda_student:id,name,email'])
            ->when($status !== '' && $status !== 'all', fn($query) => $query->where('status', $status))
            ->when($q !== '', fn($query) => $query->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('category', fn($categoryQuery) => $categoryQuery->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('freelancer.skomda_student', fn($freelancerQuery) => $freelancerQuery->where('name', 'like', "%{$q}%"));
            }))
            ->latest()
            ->paginate(10, ['*'], 'page', $page)
            ->withQueryString();

        $services->through(fn($service) => (new ServiceResource($service))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'status' => $status,
                'per_page' => 10,
                'page' => $page,
            ],
            'services' => $services,
        ], 'Data layanan berhasil diambil');
    }

    /**
     * Get approved service catalog for public/client consumers.
     */
    public function catalog(ServiceIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $categoryId = $validated['category'] ?? null;
        $priceMin = $validated['price_min'] ?? null;
        $priceMax = $validated['price_max'] ?? null;
        $deliveryTime = $validated['delivery_time'] ?? null;
        $page = (int) ($validated['page'] ?? 1);

        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->withCount([
                'services as approved_services_count' => fn($query) => $query->where('status', 'Approved'),
            ])
            ->orderBy('name')
            ->get();

        $services = $this->approvedServiceQuery()
            ->when($categoryId, fn($query) => $query->where('category_id', $categoryId))
            ->when($q !== '', fn($query) => $this->applySearch($query, $q))
            ->when($priceMin !== null, fn($query) => $query->where('price_min', '>=', $priceMin))
            ->when($priceMax !== null, fn($query) => $query->where('price_max', '<=', $priceMax))
            ->when($deliveryTime !== null, fn($query) => $query->where('delivery_time', '<=', $deliveryTime))
            ->latest()
            ->paginate(10, ['*'], 'page', $page)
            ->withQueryString();

        $services->through(fn($service) => (new ServiceResource($service))->toArray($request));

        $featuredServices = $this->approvedServiceQuery()
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($service) => (new ServiceResource($service))->toArray($request))
            ->values();

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'category' => $categoryId,
                'price_min' => $priceMin,
                'price_max' => $priceMax,
                'delivery_time' => $deliveryTime,
                'per_page' => 10,
                'page' => $page,
            ],
            'categories' => $categories,
            'featured_services' => $featuredServices,
            'services' => $services,
        ], 'Katalog layanan berhasil diambil');
    }

    /**
     * Get authenticated freelancer services.
     */
    public function freelancerIndex(Request $request): JsonResponse
    {
        $freelancer = $request->user();

        $services = Service::query()
            ->with('category:id,name')
            ->where('freelancer_id', $freelancer->id)
            ->latest()
            ->get()
            ->map(fn($service) => (new ServiceResource($service))->toArray($request))
            ->values();

        return $this->successResponse($services, 'Data layanan freelancer berhasil diambil');
    }

    /**
     * Store new service for authenticated freelancer.
     */
    public function store(ServiceStoreRequest $request): JsonResponse
    {
        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        $validated = $request->validated();
        $formAction = $validated['form_action'] ?? 'draft';
        unset($validated['form_action']);

        $service = Service::create(array_merge($validated, [
            'freelancer_id' => $request->user()->id,
            'status' => $formAction === 'submit' ? 'Pending' : 'Draft',
        ]));

        $message = $formAction === 'submit'
            ? 'Layanan berhasil diajukan untuk tinjauan admin.'
            : 'Layanan berhasil disimpan sebagai draft.';

        return $this->successResponse(
            new ServiceResource($service->fresh(['category', 'freelancer.skomda_student'])),
            $message,
            201
        );
    }

    /**
     * Get one service for administrator.
     */
    public function adminShow(Service $service): JsonResponse
    {
        return $this->successResponse(
            new ServiceResource($service->load(['category:id,name', 'freelancer.skomda_student:id,name,email'])),
            'Data layanan berhasil diambil'
        );
    }

    /**
     * Get one service for authenticated freelancer.
     */
    public function show(Request $request, Service $service): JsonResponse
    {
        if ((int) $service->freelancer_id !== (int) $request->user()->id) {
            return $this->errorResponse('Anda tidak memiliki akses ke layanan ini', 403);
        }

        return $this->successResponse(
            new ServiceResource($service->load(['category:id,name', 'freelancer.skomda_student:id,name,email'])),
            'Data layanan berhasil diambil'
        );
    }

    /**
     * Get one approved service for client/public consumers.
     */
    public function clientShow(Service $service): JsonResponse
    {
        if ($service->status !== 'Approved' || ! $service->freelancer_id || ! $service->freelancer) {
            return $this->errorResponse('Layanan tidak tersedia.', 404);
        }

        $service->load([
            'category:id,name',
            'freelancer.skomda_student:id,name,email',
            'freelancer.portofolios' => fn($query) => $query->latest()->take(3),
        ]);

        $freelancerReviewSummary = Review::whereHas('order.service', function ($query) use ($service) {
            $query->where('freelancer_id', $service->freelancer_id);
        })->selectRaw('COUNT(*) as total_reviews, COALESCE(AVG(rating), 0) as average_rating')->first();

        $otherServices = Service::query()
            ->with('category:id,name')
            ->where('freelancer_id', $service->freelancer_id)
            ->where('id', '!=', $service->id)
            ->where('status', 'Approved')
            ->whereNotNull('freelancer_id')
            ->whereHas('freelancer')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn($item) => (new ServiceResource($item))->toArray(request()))
            ->values();

        return $this->successResponse([
            'service' => new ServiceResource($service),
            'other_services' => $otherServices,
            'freelancer_review_summary' => $freelancerReviewSummary,
        ], 'Detail layanan berhasil diambil');
    }

    /**
     * Update service for authenticated freelancer.
     */
    public function update(ServiceUpdateRequest $request, Service $service): JsonResponse
    {
        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        if ((int) $service->freelancer_id !== (int) $request->user()->id) {
            return $this->errorResponse('Anda tidak memiliki akses ke layanan ini', 403);
        }

        $service->update(array_merge($request->validated(), [
            'category_id' => $service->category_id,
            'freelancer_id' => $service->freelancer_id,
        ]));

        return $this->successResponse(
            new ServiceResource($service->fresh(['category', 'freelancer.skomda_student'])),
            'Layanan berhasil diperbarui'
        );
    }

    /**
     * Delete service for authenticated freelancer.
     */
    public function destroy(Request $request, Service $service): JsonResponse
    {
        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        if ((int) $service->freelancer_id !== (int) $request->user()->id) {
            return $this->errorResponse('Anda tidak memiliki akses ke layanan ini', 403);
        }

        $service->delete();

        return $this->successResponse(null, 'Layanan berhasil dihapus');
    }

    /**
     * Submit a draft service for administrator review.
     */
    public function submit(Request $request, Service $service): JsonResponse
    {
        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        if ((int) $service->freelancer_id !== (int) $request->user()->id) {
            return $this->errorResponse('Anda tidak memiliki akses ke layanan ini', 403);
        }

        if ($service->status !== 'Draft') {
            return $this->errorResponse('Hanya layanan Draft yang dapat diajukan.', 422);
        }

        $service->update(['status' => 'Pending']);

        return $this->successResponse(
            new ServiceResource($service->fresh(['category', 'freelancer.skomda_student'])),
            'Layanan berhasil diajukan untuk tinjauan admin.'
        );
    }

    /**
     * Update service status for administrator.
     */
    public function updateStatus(ServiceStatusUpdateRequest $request, Service $service): JsonResponse
    {
        $validated = $request->validated();
        $requestedStatus = $validated['status'];
        $finalStatus = $requestedStatus;

        if ($requestedStatus === 'Rejected') {
            $finalStatus = 'Draft';

            Notification::create([
                'title' => 'Layanan Perlu Perbaikan',
                'message' => "Layanan '{$service->title}' dikembalikan oleh admin. Alasan: " . ($validated['reject_reason'] ?? 'Tidak ada alasan spesifik') . '. Silakan perbaiki dan ajukan kembali!',
                'type' => 'warning',
                'role' => 'freelancer',
                'user_id' => $service->freelancer_id,
                'link' => url('/freelancer/services/' . $service->id . '/edit'),
            ]);
        }

        $service->update([
            'status' => $finalStatus,
            'reject_reason' => $requestedStatus === 'Rejected' ? ($validated['reject_reason'] ?? null) : null,
        ]);

        if ($requestedStatus === 'Approved') {
            Notification::create([
                'title' => 'Layanan Disetujui',
                'message' => "Layanan '{$service->title}' telah disetujui admin dan sudah tampil di katalog layanan.",
                'type' => 'success',
                'role' => 'freelancer',
                'user_id' => $service->freelancer_id,
                'link' => url('/freelancer/services/' . $service->id),
            ]);
        }

        $message = $requestedStatus === 'Rejected'
            ? 'Layanan telah dikembalikan ke freelancer untuk diperbaiki.'
            : 'Status layanan berhasil diperbarui';

        return $this->successResponse(
            new ServiceResource($service->fresh(['category', 'freelancer.skomda_student'])),
            $message
        );
    }

    private function approvedServiceQuery()
    {
        return Service::query()
            ->with(['category:id,name', 'freelancer.skomda_student:id,name,email'])
            ->where('status', 'Approved')
            ->whereNotNull('freelancer_id')
            ->whereHas('freelancer');
    }

    private function applySearch($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('category', fn($categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"))
                ->orWhereHas('freelancer.skomda_student', fn($freelancerQuery) => $freelancerQuery->where('name', 'like', "%{$search}%"));
        });
    }

    private function ensureApprovedFreelancer(Request $request): ?JsonResponse
    {
        $freelancer = $request->user();

        if (! $freelancer) {
            return $this->errorResponse('Tidak terautentikasi', 401);
        }

        if ($freelancer->status !== 'Approved') {
            return $this->errorResponse(
                'Akses terbatas. Mohon ajukan verifikasi ke admin melalui panduan onboarding.',
                403
            );
        }

        return null;
    }
}
