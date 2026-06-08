<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InteractsWithOrdersApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRejectRequest;
use App\Http\Requests\Api\OrderRevisionRequest;
use App\Http\Resources\NegotiationResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderRevisionRequestControllerApi extends Controller
{
    use ApiResponse, InteractsWithOrdersApi;

    public function store(OrderRevisionRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('requestRevision', $order);

        if (! in_array($order->status, ['In Progress', 'Completed'], true)) {
            return $this->errorResponse('Revision hanya bisa diminta pada pekerjaan yang sedang berlangsung atau sudah selesai.', 422);
        }

        $validated = $request->validated();

        $negotiation = $order->negotiations()->create([
            'sender' => 'client',
            'message' => 'Permintaan Revisi: '.$validated['reason']."\n\nDetail: ".($validated['description'] ?? '-'),
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'Pending',
        ]);

        $order->update(['status' => 'Revision']);

        $this->notifyFreelancer(
            $order->freelancer_id ?? $order->service?->freelancer_id,
            'Permintaan Revisi dari Klien',
            "Klien meminta perbaikan/revisi hasil kerja untuk project '".$this->projectTitle($order)."'. Alasan: {$validated['reason']}",
            'warning',
            url('/freelancer/orders/'.$order->id)
        );

        return $this->successResponse([
            'revision_request' => new NegotiationResource($negotiation->fresh(['order.client', 'order.service.category', 'order.service.service_category', 'order.service.freelancer.skomda_student'])),
            'order' => new OrderResource($order->fresh($this->detailRelations())),
        ], 'Permintaan revisi berhasil dikirim.', 201);
    }

    public function approve(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('approveRevision', $order);

        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        if ($order->status !== 'Revision') {
            return $this->errorResponse('Order bukan dalam status revisi.', 422);
        }

        $order->update(['status' => 'In Progress']);

        $negotiation = $order->negotiations()->create([
            'sender' => 'freelancer',
            'message' => '[REVISION APPROVED] Revisi telah disetujui dan akan segera dikerjakan.',
            'status' => 'Approved',
        ]);

        $this->notifyClient(
            $order->client_id,
            'Permintaan Revisi Disetujui',
            "Freelancer menyetujui pengerjaan revisi Anda. Pekerjaan kembali berstatus 'In Progress'.",
            'success',
            url('/client/orders/'.$order->id)
        );

        return $this->successResponse([
            'revision_request' => new NegotiationResource($negotiation->fresh(['order.client', 'order.service.category', 'order.service.service_category', 'order.service.freelancer.skomda_student'])),
            'order' => new OrderResource($order->fresh($this->detailRelations())),
        ], 'Revisi disetujui. Pengerjaan revisi dimulai.');
    }

    public function reject(OrderRejectRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('approveRevision', $order);

        if ($response = $this->ensureApprovedFreelancer($request)) {
            return $response;
        }

        if ($order->status !== 'Revision') {
            return $this->errorResponse('Order bukan dalam status revisi.', 422);
        }

        $reason = $request->validated('reason');

        $order->update(['status' => 'Completed']);

        $negotiation = $order->negotiations()->create([
            'sender' => 'freelancer',
            'message' => '[REVISION REJECTED] Revisi ditolak. Alasan: '.$reason,
            'reason' => $reason,
            'status' => 'Rejected',
        ]);

        $this->notifyClient(
            $order->client_id,
            'Permintaan Revisi Ditolak',
            "Freelancer menolak permintaan revisi Anda. Alasan: '{$reason}'. Status kembali ke Completed.",
            'danger',
            url('/client/orders/'.$order->id)
        );

        return $this->successResponse([
            'revision_request' => new NegotiationResource($negotiation->fresh(['order.client', 'order.service.category', 'order.service.service_category', 'order.service.freelancer.skomda_student'])),
            'order' => new OrderResource($order->fresh($this->detailRelations())),
        ], 'Revisi ditolak.');
    }
}
