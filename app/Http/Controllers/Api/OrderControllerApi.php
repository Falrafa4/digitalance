<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InteractsWithOrdersApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderIndexRequest;
use App\Http\Requests\Api\OrderStoreRequest;
use App\Http\Requests\Api\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class OrderControllerApi extends Controller
{
    use ApiResponse, InteractsWithOrdersApi;

    public function index(OrderIndexRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Order::class);

        $validated = $request->validated();
        $status = $validated['status'] ?? null;
        $search = trim((string) ($validated['q'] ?? ''));
        $payout = strtolower(trim((string) ($validated['payout'] ?? 'all')));
        $page = (int) ($validated['page'] ?? 1);

        $ordersQuery = $this->scopeOrdersForUser($this->baseOrderQuery(), $request->user());

        $orders = $ordersQuery
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhere('brief', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($clientQuery) => $clientQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('service', fn ($serviceQuery) => $serviceQuery->where('title', 'like', "%{$search}%"));
            }))
            ->when($payout === 'paid', fn ($query) => $query->whereHas('transactions', function ($transactionQuery) {
                $transactionQuery->where('type', 'Full')->where('status', 'Paid');
            }))
            ->when($payout === 'pending', fn ($query) => $query->where('status', 'Completed')
                ->whereDoesntHave('transactions', function ($transactionQuery) {
                    $transactionQuery->where('type', 'Full')->where('status', 'Paid');
                }))
            ->latest()
            ->paginate(12, ['*'], 'page', $page)
            ->withQueryString();

        $orders->through(fn ($order) => (new OrderResource($order))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $search,
                'status' => $status,
                'payout' => $payout,
                'per_page' => 12,
                'page' => $page,
            ],
            'orders' => $orders,
        ], 'Data pesanan berhasil diambil');
    }

    public function store(OrderStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', Order::class);

        $validated = $request->validated();
        $user = $request->user();

        if ($user?->getRole() === 'client') {
            Gate::authorize('createForClient', Order::class);

            $service = Service::with('freelancer')->findOrFail($validated['service_id']);

            if ($response = $this->ensureOrderableService($service)) {
                return $response;
            }

            $order = Order::create([
                'service_id' => $service->id,
                'client_id' => $user->id,
                'freelancer_id' => $service->freelancer_id,
                'brief' => $validated['brief'],
                'deadline' => $validated['deadline'] ?? null,
                'status' => 'Pending',
                'agreed_price' => null,
            ]);

            $this->storeUploadedAttachments($order, $request->file('attachments') ?? [], 'client');
            $this->notifyFreelancer(
                $service->freelancer_id,
                'Pesanan Baru Masuk',
                "Klien mengajukan pesanan baru untuk layanan '{$service->title}'. Silakan periksa detail pesanan.",
                'info',
                url('/freelancer/orders/'.$order->id)
            );

            return $this->successResponse(
                new OrderResource($order->fresh($this->detailRelations())),
                'Pesanan berhasil dibuat',
                201
            );
        }

        $service = Service::findOrFail($validated['service_id']);

        $order = Order::create([
            'client_id' => $validated['client_id'],
            'service_id' => $service->id,
            'freelancer_id' => $service->freelancer_id,
            'brief' => $validated['brief'],
            'status' => $validated['status'] ?? 'Pending',
            'agreed_price' => $validated['agreed_price'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
        ]);

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Pesanan berhasil dibuat',
            201
        );
    }

    public function show(Order $order): JsonResponse
    {
        Gate::authorize('view', $order);

        return $this->successResponse(
            new OrderResource($order->load($this->detailRelations())),
            'Detail pesanan berhasil diambil'
        );
    }

    public function update(OrderUpdateRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('update', $order);

        $validated = $request->validated();

        if (array_key_exists('service_id', $validated)) {
            $service = Service::findOrFail($validated['service_id']);
            $validated['freelancer_id'] = $service->freelancer_id;
        }

        $order->update($validated);

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Pesanan berhasil diperbarui'
        );
    }

    public function destroy(Order $order): JsonResponse
    {
        Gate::authorize('delete', $order);

        $order->delete();

        return $this->successResponse(null, 'Pesanan berhasil dihapus');
    }
}
