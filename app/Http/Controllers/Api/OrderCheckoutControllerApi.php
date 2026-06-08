<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InteractsWithOrdersApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderPaymentRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\TransactionResource;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderCheckoutControllerApi extends Controller
{
    use ApiResponse, InteractsWithOrdersApi;

    public function show(Order $order): JsonResponse
    {
        Gate::authorize('checkout', $order);

        if ($response = $this->ensureCheckoutableOrder($order)) {
            return $response;
        }

        return $this->successResponse([
            'order' => new OrderResource($order->load($this->detailRelations())),
            'summary' => $this->checkoutSummary($order),
        ], 'Ringkasan checkout berhasil diambil');
    }

    public function store(OrderPaymentRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('pay', $order);

        if ($response = $this->ensureCheckoutableOrder($order)) {
            return $response;
        }

        if ($order->transactions()->where('status', 'Paid')->exists()) {
            return $this->errorResponse('Pembayaran sudah pernah dilakukan.', 409);
        }

        $summary = $this->checkoutSummary($order);
        $paymentMethod = $request->validated('payment_method');

        $transaction = DB::transaction(function () use ($order, $summary) {
            $order->update(['status' => 'Paid']);

            return $order->transactions()->create([
                'order_id' => $order->id,
                'amount' => $summary['total'],
                'type' => 'Full',
                'status' => 'Paid',
            ]);
        });

        $this->notifyFreelancer(
            $order->freelancer_id ?? $order->service?->freelancer_id,
            'Pembayaran Pesanan Diterima',
            "Klien telah melunasi pembayaran untuk pesanan '".$this->projectTitle($order)."'. Status berubah menjadi 'Paid'. Silakan mulai pengerjaan project.",
            'success',
            url('/freelancer/orders/'.$order->id)
        );

        $methodLabel = $this->paymentMethodLabels()[$paymentMethod] ?? 'QRIS';

        return $this->successResponse([
            'transaction' => new TransactionResource($transaction->fresh(['order.client', 'order.service.category', 'order.service.service_category', 'order.service.freelancer.skomda_student'])),
            'order' => new OrderResource($order->fresh($this->detailRelations())),
            'summary' => $summary,
        ], 'Pembayaran sebesar Rp '.number_format($summary['total'], 0, ',', '.').' via '.$methodLabel.' berhasil!');
    }
}
