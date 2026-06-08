<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InteractsWithOrdersApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderNegotiationRequest;
use App\Http\Resources\NegotiationResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class OrderNegotiationControllerApi extends Controller
{
    use ApiResponse, InteractsWithOrdersApi;

    public function store(OrderNegotiationRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('requestNegotiation', $order);

        $validated = $request->validated();

        $negotiation = $order->negotiations()->create([
            'sender' => 'client',
            'message' => 'Negosiasi harga: '.$validated['reason']."\n\nHarga tawaran: Rp ".number_format($validated['new_price'], 0, ',', '.')."\n\nDetail: ".($validated['description'] ?? '-'),
            'proposed_price' => $validated['new_price'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'Pending',
        ]);

        if ($order->status === 'Pending') {
            $order->update([
                'status' => 'Negotiated',
                'agreed_price' => $validated['new_price'],
            ]);
        }

        $this->notifyFreelancer(
            $order->freelancer_id ?? $order->service?->freelancer_id,
            'Permintaan Negosiasi Klien',
            'Klien mengajukan tawaran harga baru sebesar Rp '.number_format($validated['new_price'], 0, ',', '.').'.',
            'warning',
            url('/freelancer/orders/'.$order->id)
        );

        return $this->successResponse([
            'negotiation' => new NegotiationResource($negotiation->fresh(['order.client', 'order.service.category', 'order.service.service_category', 'order.service.freelancer.skomda_student'])),
            'order' => new OrderResource($order->fresh($this->detailRelations())),
        ], 'Negosiasi berhasil dikirim ke freelancer.', 201);
    }
}
