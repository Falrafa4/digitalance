<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TransactionIndexRequest;
use App\Http\Requests\Api\TransactionStoreRequest;
use App\Http\Requests\Api\TransactionUpdateRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TransactionControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get transactions scoped to the authenticated role.
     */
    public function index(TransactionIndexRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Transaction::class);

        $validated = $request->validated();
        $status = strtolower(trim((string) ($validated['status'] ?? 'all')));
        $search = trim((string) ($validated['q'] ?? ''));
        $type = $validated['type'] ?? null;
        $orderId = $validated['order_id'] ?? null;
        $page = (int) ($validated['page'] ?? 1);
        $user = $request->user();

        $transactionsQuery = $this->baseTransactionQuery();

        if ($user?->getRole() === 'client') {
            $transactionsQuery->whereHas('order', fn ($orderQuery) => $orderQuery->where('client_id', $user->id));
        } elseif ($user?->getRole() === 'freelancer') {
            $transactionsQuery->whereHas('order', function ($orderQuery) use ($user) {
                $orderQuery->where(function ($query) use ($user) {
                    $query->where('freelancer_id', $user->id)
                        ->orWhereHas('service', fn ($serviceQuery) => $serviceQuery->where('freelancer_id', $user->id));
                });
            });
        }

        if ($status !== 'all' && in_array($status, ['paid', 'pending', 'failed', 'refund'], true)) {
            if ($status === 'refund') {
                $transactionsQuery->whereRaw('LOWER(type) = ?', ['refund']);
            } else {
                $transactionsQuery->whereRaw('LOWER(status) = ?', [$status]);
            }
        }

        if ($type) {
            $transactionsQuery->where('type', $type);
        }

        if ($orderId) {
            $transactionsQuery->where('order_id', $orderId);
        }

        if ($search !== '') {
            $transactionsQuery->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhereHas('order.client', fn ($clientQuery) => $clientQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('order.service', fn ($serviceQuery) => $serviceQuery->where('title', 'like', "%{$search}%"));
            });
        }

        $transactions = $transactionsQuery
            ->latest()
            ->paginate(15, ['*'], 'page', $page)
            ->withQueryString();

        $transactions->through(fn ($transaction) => (new TransactionResource($transaction))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $search,
                'status' => $status,
                'type' => $type,
                'order_id' => $orderId,
                'per_page' => 15,
                'page' => $page,
            ],
            'transactions' => $transactions,
        ], 'Data transaksi berhasil diambil');
    }

    /**
     * Store a new transaction for administrator.
     */
    public function store(TransactionStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', Transaction::class);

        $validated = $request->validated();

        $transaction = Transaction::create([
            'order_id' => $validated['order_id'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'status' => $validated['status'] ?? 'Pending',
        ]);

        return $this->successResponse(
            new TransactionResource($transaction->fresh($this->defaultRelations())),
            'Transaksi berhasil dibuat',
            201
        );
    }

    /**
     * Get a single transaction.
     */
    public function show(Transaction $transaction): JsonResponse
    {
        Gate::authorize('view', $transaction);

        return $this->successResponse(
            new TransactionResource($transaction->load($this->defaultRelations())),
            'Detail transaksi berhasil diambil'
        );
    }

    /**
     * Update a transaction for administrator.
     */
    public function update(TransactionUpdateRequest $request, Transaction $transaction): JsonResponse
    {
        Gate::authorize('update', $transaction);

        $transaction->update($request->validated());

        return $this->successResponse(
            new TransactionResource($transaction->fresh($this->defaultRelations())),
            'Transaksi berhasil diperbarui'
        );
    }

    /**
     * Delete a transaction for administrator.
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        Gate::authorize('delete', $transaction);

        $transaction->delete();

        return $this->successResponse(null, 'Transaksi berhasil dihapus');
    }

    private function baseTransactionQuery()
    {
        return Transaction::query()->with($this->defaultRelations())
            ->whereHas('order.service', function ($query) {
                $query->whereNotNull('freelancer_id')
                    ->whereHas('freelancer');
            });
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
