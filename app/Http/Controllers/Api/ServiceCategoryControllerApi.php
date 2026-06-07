<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ServiceCategoryIndexRequest;
use App\Http\Requests\Api\ServiceCategoryStoreRequest;
use App\Http\Requests\Api\ServiceCategoryUpdateRequest;
use App\Http\Resources\ServiceCategoryResource;
use App\Models\ServiceCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ServiceCategoryControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get service category list for administrator.
     */
    public function index(ServiceCategoryIndexRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', ServiceCategory::class);

        if ($request->user()?->getRole() !== 'administrator') {
            return $this->activeCategoryOptions($request, 'Data kategori layanan berhasil diambil');
        }

        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $isActive = $request->has('is_active') && $request->filled('is_active')
            ? $request->boolean('is_active')
            : null;
        $page = (int) ($validated['page'] ?? 1);

        $categories = ServiceCategory::query()
            ->withCount('services')
            ->when($q !== '', fn($query) => $query->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            }))
            ->when($isActive !== null, fn($query) => $query->where('is_active', $isActive))
            ->orderBy('name')
            ->paginate(10, ['*'], 'page', $page)
            ->withQueryString();

        $categories->through(fn($category) => (new ServiceCategoryResource($category))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'is_active' => $isActive,
                'per_page' => 10,
                'page' => $page,
            ],
            'service_categories' => $categories,
        ], 'Data kategori layanan berhasil diambil');
    }

    /**
     * Store a new service category.
     */
    public function store(ServiceCategoryStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', ServiceCategory::class);

        $category = ServiceCategory::create($this->normalizedData($request->validated()));

        return $this->successResponse(
            new ServiceCategoryResource($category->fresh()),
            'Kategori layanan berhasil ditambahkan',
            201
        );
    }

    /**
     * Get a single service category.
     */
    public function show(ServiceCategory $serviceCategory): JsonResponse
    {
        Gate::authorize('view', $serviceCategory);

        return $this->successResponse(
            new ServiceCategoryResource($serviceCategory->loadCount('services')),
            'Data kategori layanan berhasil diambil'
        );
    }

    /**
     * Update a service category.
     */
    public function update(ServiceCategoryUpdateRequest $request, ServiceCategory $serviceCategory): JsonResponse
    {
        Gate::authorize('update', $serviceCategory);

        $serviceCategory->update($this->normalizedData($request->validated()));

        return $this->successResponse(
            new ServiceCategoryResource($serviceCategory->fresh()->loadCount('services')),
            'Kategori layanan berhasil diperbarui'
        );
    }

    /**
     * Delete a service category.
     */
    public function destroy(ServiceCategory $serviceCategory): JsonResponse
    {
        Gate::authorize('delete', $serviceCategory);

        if ($serviceCategory->services()->exists()) {
            return $this->errorResponse(
                'Kategori layanan tidak dapat dihapus karena masih digunakan oleh layanan.',
                409
            );
        }

        $serviceCategory->delete();

        return $this->successResponse(null, 'Kategori layanan berhasil dihapus');
    }

    /**
     * Get active service categories for freelancers.
     */
    public function freelancerIndex(ServiceCategoryIndexRequest $request): JsonResponse
    {
        return $this->activeCategoryOptions($request, 'Data kategori layanan freelancer berhasil diambil');
    }

    /**
     * Get active service categories for clients.
     */
    public function clientIndex(ServiceCategoryIndexRequest $request): JsonResponse
    {
        return $this->activeCategoryOptions($request, 'Data kategori layanan client berhasil diambil');
    }

    private function activeCategoryOptions(ServiceCategoryIndexRequest $request, string $message): JsonResponse
    {
        $q = trim((string) ($request->validated()['q'] ?? ''));

        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->withCount([
                'services as approved_services_count' => fn($query) => $query->where('status', 'Approved'),
            ])
            ->when($q !== '', fn($query) => $query->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            }))
            ->orderBy('name')
            ->get()
            ->map(fn($category) => (new ServiceCategoryResource($category))->toArray($request))
            ->values();

        return $this->successResponse($categories, $message);
    }

    private function normalizedData(array $validated): array
    {
        if (array_key_exists('is_active', $validated)) {
            $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
        }

        return $validated;
    }
}
