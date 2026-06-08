<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\InteractsWithOrdersApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderAttachmentStoreRequest;
use App\Http\Resources\OrderAttachmentResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class OrderAttachmentControllerApi extends Controller
{
    use ApiResponse, InteractsWithOrdersApi;

    public function store(OrderAttachmentStoreRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('attachFiles', $order);

        $attachments = $this->storeUploadedAttachments($order, $request->file('file') ?? [], 'client');

        return $this->successResponse([
            'attachments' => OrderAttachmentResource::collection(collect($attachments)),
            'order' => new OrderResource($order->fresh($this->detailRelations())),
        ], 'Attachment berhasil diupload');
    }
}
