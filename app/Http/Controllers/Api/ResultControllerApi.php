<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ResultIndexRequest;
use App\Http\Requests\Api\ResultStoreRequest;
use App\Http\Requests\Api\ResultUpdateRequest;
use App\Http\Resources\ResultResource;
use App\Models\Order;
use App\Models\Result;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ResultControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get results scoped to the authenticated role.
     */
    public function index(ResultIndexRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Result::class);

        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $orderId = $validated['order_id'] ?? null;
        $resultMode = $validated['result_mode'] ?? null;
        $page = (int) ($validated['page'] ?? 1);
        $user = $request->user();

        $resultsQuery = $this->baseResultQuery();

        if ($user?->getRole() === 'client') {
            $resultsQuery->whereHas('order', fn ($orderQuery) => $orderQuery->where('client_id', $user->id));
        } elseif ($user?->getRole() === 'freelancer') {
            $resultsQuery->whereHas('order', function ($orderQuery) use ($user) {
                $orderQuery->where(function ($query) use ($user) {
                    $query->where('freelancer_id', $user->id)
                        ->orWhereHas('service', fn ($serviceQuery) => $serviceQuery->where('freelancer_id', $user->id));
                });
            });
        }

        if ($orderId) {
            $resultsQuery->where('order_id', $orderId);
        }

        if ($resultMode) {
            $resultsQuery->where('result_mode', $resultMode);
        }

        if ($q !== '') {
            $resultsQuery->where(function ($query) use ($q) {
                $query->where('note', 'like', "%{$q}%")
                    ->orWhere('version', 'like', "%{$q}%")
                    ->orWhere('file_url', 'like', "%{$q}%")
                    ->orWhereHas('order.client', fn ($clientQuery) => $clientQuery->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('order.service', fn ($serviceQuery) => $serviceQuery->where('title', 'like', "%{$q}%"));
            });
        }

        $results = $resultsQuery
            ->latest()
            ->paginate(12, ['*'], 'page', $page)
            ->withQueryString();

        $results->through(fn ($result) => (new ResultResource($result))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'order_id' => $orderId,
                'result_mode' => $resultMode,
                'per_page' => 12,
                'page' => $page,
            ],
            'results' => $results,
        ], 'Data hasil kerja berhasil diambil');
    }

    /**
     * Store a new result for an authenticated freelancer.
     */
    public function store(ResultStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', Result::class);

        $validated = $request->validated();
        $order = Order::with('service')->findOrFail($validated['order_id']);

        Gate::authorize('createForOrder', [Result::class, $order]);

        $resultMode = $validated['result_mode'];
        $filePath = $resultMode === 'file'
            ? $request->file('file')->store('results', 'public')
            : $validated['result_link'];

        try {
            $result = DB::transaction(function () use ($order, $validated, $resultMode, $filePath) {
                $order->update(['status' => 'In Progress']);

                return Result::create([
                    'order_id' => $order->id,
                    'file_url' => $filePath,
                    'result_mode' => $resultMode,
                    'note' => $validated['note'] ?? '',
                    'version' => $validated['version'],
                ]);
            });
        } catch (\Throwable $e) {
            if ($resultMode === 'file' && $filePath) {
                Storage::disk('public')->delete($filePath);
            }

            throw $e;
        }

        return $this->successResponse(
            new ResultResource($result->fresh($this->defaultRelations())),
            'Hasil kerja berhasil dikirim',
            201
        );
    }

    /**
     * Get a single result.
     */
    public function show(Result $result): JsonResponse
    {
        Gate::authorize('view', $result);

        return $this->successResponse(
            new ResultResource($result->load($this->defaultRelations())),
            'Detail hasil kerja berhasil diambil'
        );
    }

    /**
     * Update a result for administrator.
     */
    public function update(ResultUpdateRequest $request, Result $result): JsonResponse
    {
        Gate::authorize('update', $result);

        $result->update($request->validated());

        return $this->successResponse(
            new ResultResource($result->fresh($this->defaultRelations())),
            'Hasil kerja berhasil diperbarui'
        );
    }

    /**
     * Delete a result for administrator.
     */
    public function destroy(Result $result): JsonResponse
    {
        Gate::authorize('delete', $result);

        if ($result->result_mode === 'file' && $result->file_url) {
            Storage::disk('public')->delete($result->file_url);
        }

        $result->delete();

        return $this->successResponse(null, 'Hasil kerja berhasil dihapus');
    }

    private function baseResultQuery()
    {
        return Result::query()->with($this->defaultRelations());
    }

    /**
     * @return array<int, string>
     */
    private function defaultRelations(): array
    {
        return [
            'order.client',
            'order.service.category',
            'order.service.service_category',
            'order.service.freelancer.skomda_student',
        ];
    }
}
