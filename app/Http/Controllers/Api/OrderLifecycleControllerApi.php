<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InteractsWithOrdersApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderAgreedPriceRequest;
use App\Http\Requests\Api\OrderRejectRequest;
use App\Http\Resources\NegotiationResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderLifecycleControllerApi extends Controller
{
    use ApiResponse, InteractsWithOrdersApi;

    public function accept(OrderAgreedPriceRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('accept', $order);

        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        if ($order->status !== 'Pending') {
            return $this->errorResponse('Pesanan tidak dapat diterima pada status saat ini.', 422);
        }

        $validated = $request->validated();

        $order->update([
            'agreed_price' => $validated['agreed_price'],
            'status' => 'Negotiated',
        ]);

        $negotiation = null;

        if ($request->filled('note')) {
            $negotiation = $order->negotiations()->create([
                'sender' => 'freelancer',
                'message' => $validated['note'],
                'proposed_price' => $validated['agreed_price'],
                'status' => 'Pending',
            ]);
        }

        $this->notifyClient(
            $order->client_id,
            'Tawaran Harga dari Freelancer',
            'Freelancer mengajukan kesepakatan harga baru sebesar Rp '.number_format((float) $validated['agreed_price'], 0, ',', '.').'. Silakan lakukan checkout pembayaran.',
            'warning',
            url('/client/orders/'.$order->id)
        );

        return $this->successResponse([
            'order' => new OrderResource($order->fresh($this->detailRelations())),
            'negotiation' => $negotiation ? new NegotiationResource($negotiation->fresh(['order.client', 'order.service.category', 'order.service.service_category', 'order.service.freelancer.skomda_student'])) : null,
        ], 'Pesanan diterima dengan penawaran baru');
    }

    public function reject(OrderRejectRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('reject', $order);

        $user = $request->user();

        if ($user?->getRole() === 'freelancer' && $response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        $reason = $request->validated('reason');

        if ($user?->getRole() === 'freelancer') {
            $order->update([
                'status' => 'Cancelled',
                'brief' => $order->brief."\n\nRejection Reason: ".$reason,
            ]);

            $this->notifyClient(
                $order->client_id,
                'Pesanan Ditolak Freelancer',
                "Freelancer menolak pesanan Anda. Alasan: '{$reason}'",
                'danger',
                url('/client/orders/'.$order->id)
            );
        } else {
            $order->update(['status' => 'Cancelled']);
        }

        $negotiation = $order->negotiations()->create([
            'sender' => $user?->getRole() === 'freelancer' ? 'freelancer' : 'client',
            'message' => 'Order ditolak. Alasan: '.$reason,
            'reason' => $reason,
        ]);

        return $this->successResponse([
            'order' => new OrderResource($order->fresh($this->detailRelations())),
            'negotiation' => new NegotiationResource($negotiation->fresh(['order.client', 'order.service.category', 'order.service.service_category', 'order.service.freelancer.skomda_student'])),
        ], 'Pesanan telah ditolak');
    }

    public function complete(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('complete', $order);

        if ($order->status !== 'In Progress') {
            return $this->errorResponse('Order tidak dalam tahap pengerjaan.', 422);
        }

        if ($order->results()->count() === 0) {
            return $this->errorResponse('Belum ada hasil kerja dari freelancer.', 422);
        }

        $order->update(['status' => 'Completed']);

        $this->notifyFreelancer(
            $order->freelancer_id ?? $order->service?->freelancer_id,
            'Project Diterima & Selesai',
            "Selamat! Klien telah menerima hasil pekerjaan Anda untuk project '".$this->projectTitle($order)."'. Status pesanan: Completed.",
            'success',
            url('/freelancer/orders/'.$order->id)
        );

        return $this->successResponse(
            new OrderResource($order->fresh($this->detailRelations())),
            'Hasil pekerjaan berhasil diterima. Terima kasih!'
        );
    }

    public function updatePrice(OrderAgreedPriceRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('updatePrice', $order);

        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        if ($order->status !== 'Negotiated') {
            return $this->errorResponse('Harga hanya dapat diperbarui saat order berada pada tahap negosiasi.', 422);
        }

        $validated = $request->validated();

        $order->update([
            'agreed_price' => $validated['agreed_price'],
        ]);

        $negotiation = null;

        if ($request->filled('note')) {
            $negotiation = $order->negotiations()->create([
                'sender' => 'freelancer',
                'message' => $validated['note'],
                'proposed_price' => $validated['agreed_price'],
                'status' => 'Pending',
            ]);
        }

        $this->notifyClient(
            $order->client_id,
            'Tawaran Harga dari Freelancer',
            'Freelancer memperbarui kesepakatan harga menjadi Rp '.number_format((float) $validated['agreed_price'], 0, ',', '.').'. Silakan lakukan checkout pembayaran.',
            'warning',
            url('/client/orders/'.$order->id)
        );

        return $this->successResponse([
            'order' => new OrderResource($order->fresh($this->detailRelations())),
            'negotiation' => $negotiation ? new NegotiationResource($negotiation->fresh(['order.client', 'order.service.category', 'order.service.service_category', 'order.service.freelancer.skomda_student'])) : null,
        ], 'Penawaran harga berhasil dikirim');
    }
}
