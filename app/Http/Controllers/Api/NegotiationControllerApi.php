<?php

namespace App\Http\Controllers\Api;

use App\Events\NegotiationSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\NegotiationIndexRequest;
use App\Http\Requests\Api\NegotiationStoreRequest;
use App\Http\Requests\Api\NegotiationUpdateRequest;
use App\Http\Resources\NegotiationResource;
use App\Models\Negotiation;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class NegotiationControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get negotiations scoped to the authenticated role.
     */
    public function index(NegotiationIndexRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Negotiation::class);

        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $orderId = $validated['order_id'] ?? null;
        $status = $validated['status'] ?? null;
        $page = (int) ($validated['page'] ?? 1);
        $user = $request->user();

        $negotiationsQuery = $this->baseNegotiationQuery();

        if ($user?->getRole() === 'client') {
            $negotiationsQuery->whereHas('order', fn ($orderQuery) => $orderQuery->where('client_id', $user->id));
        } elseif ($user?->getRole() === 'freelancer') {
            $negotiationsQuery->whereHas('order', function ($orderQuery) use ($user) {
                $orderQuery->where(function ($query) use ($user) {
                    $query->where('freelancer_id', $user->id)
                        ->orWhereHas('service', fn ($serviceQuery) => $serviceQuery->where('freelancer_id', $user->id));
                });
            });
        }

        if ($orderId) {
            $negotiationsQuery->where('order_id', $orderId);
        }

        if ($status) {
            $negotiationsQuery->where('status', $status);
        }

        if ($q !== '') {
            $negotiationsQuery->where(function ($query) use ($q) {
                $query->where('message', 'like', "%{$q}%")
                    ->orWhere('reason', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('order.client', fn ($clientQuery) => $clientQuery->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('order.service', fn ($serviceQuery) => $serviceQuery->where('title', 'like', "%{$q}%"));
            });
        }

        $negotiations = $negotiationsQuery
            ->latest()
            ->paginate(12, ['*'], 'page', $page)
            ->withQueryString();

        $negotiations->through(fn ($negotiation) => (new NegotiationResource($negotiation))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'order_id' => $orderId,
                'status' => $status,
                'per_page' => 12,
                'page' => $page,
            ],
            'negotiations' => $negotiations,
        ], 'Data negosiasi berhasil diambil');
    }

    /**
     * Store a message or price negotiation for an authenticated client/freelancer.
     */
    public function store(NegotiationStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', Negotiation::class);

        $validated = $request->validated();
        $order = Order::with('service')->findOrFail($validated['order_id']);

        Gate::authorize('createForOrder', [Negotiation::class, $order]);

        $sender = $request->user()->getRole();
        $message = $this->buildMessage($validated);
        $proposedPrice = $validated['new_price'] ?? null;

        $negotiation = Negotiation::create([
            'order_id' => $order->id,
            'sender' => $sender,
            'message' => $message,
            'proposed_price' => $proposedPrice,
            'reason' => $validated['reason'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $proposedPrice ? 'Pending' : null,
        ]);

        broadcast(new NegotiationSent($negotiation))->toOthers();

        if ($proposedPrice && $sender === 'client') {
            $order->update([
                'status' => 'Negotiated',
                'agreed_price' => $proposedPrice,
            ]);
        } elseif ($proposedPrice && $sender === 'freelancer') {
            $order->update([
                'status' => 'Negotiated',
                'agreed_price' => $proposedPrice,
            ]);
        } elseif ($order->status === 'Pending') {
            $order->update(['status' => 'Negotiated']);
        }

        return $this->successResponse(
            new NegotiationResource($negotiation->fresh($this->defaultRelations())),
            'Negosiasi berhasil dikirim',
            201
        );
    }

    /**
     * Get a single negotiation.
     */
    public function show(Negotiation $negotiation): JsonResponse
    {
        Gate::authorize('view', $negotiation);

        return $this->successResponse(
            new NegotiationResource($negotiation->load($this->defaultRelations())),
            'Detail negosiasi berhasil diambil'
        );
    }

    /**
     * Update a negotiation for administrator.
     */
    public function update(NegotiationUpdateRequest $request, Negotiation $negotiation): JsonResponse
    {
        Gate::authorize('update', $negotiation);

        $negotiation->update($request->validated());

        return $this->successResponse(
            new NegotiationResource($negotiation->fresh($this->defaultRelations())),
            'Negosiasi berhasil diperbarui'
        );
    }

    /**
     * Delete a negotiation for administrator.
     */
    public function destroy(Negotiation $negotiation): JsonResponse
    {
        Gate::authorize('delete', $negotiation);

        $negotiation->delete();

        return $this->successResponse(null, 'Negosiasi berhasil dihapus');
    }

    /**
     * Accept a client price negotiation for an authenticated freelancer.
     */
    public function accept(Negotiation $negotiation): JsonResponse
    {
        Gate::authorize('accept', $negotiation);

        return $this->transitionFreelancerNegotiation(
            $negotiation,
            'Accepted',
            '[SISTEM: Negosiasi harga diterima oleh Freelancer]',
            'Negosiasi berhasil diterima.'
        );
    }

    /**
     * Reject a client price negotiation for an authenticated freelancer.
     */
    public function reject(Negotiation $negotiation): JsonResponse
    {
        Gate::authorize('reject', $negotiation);

        return $this->transitionFreelancerNegotiation(
            $negotiation,
            'Rejected',
            '[SISTEM: Negosiasi harga ditolak oleh Freelancer]',
            'Negosiasi berhasil ditolak.'
        );
    }

    private function transitionFreelancerNegotiation(
        Negotiation $negotiation,
        string $status,
        string $systemMessage,
        string $responseMessage
    ): JsonResponse {
        if ($negotiation->status && $negotiation->status !== 'Pending') {
            return $this->errorResponse('Negosiasi yang sudah diproses tidak bisa diproses ulang.', 409);
        }

        $negotiation->update([
            'status' => $status,
            'message' => rtrim($negotiation->message)."\n\n{$systemMessage}",
        ]);

        if ($status === 'Accepted' && $negotiation->proposed_price) {
            $negotiation->order?->update([
                'status' => 'Negotiated',
                'agreed_price' => $negotiation->proposed_price,
            ]);
        }

        return $this->successResponse(
            new NegotiationResource($negotiation->fresh($this->defaultRelations())),
            $responseMessage
        );
    }

    private function buildMessage(array $validated): string
    {
        if (! empty($validated['new_price'])) {
            return 'Negosiasi harga: '.$validated['reason']
                ."\nHarga tawaran: Rp ".number_format($validated['new_price'], 0, ',', '.')
                ."\nDeskripsi: ".($validated['description'] ?? '-');
        }

        return $validated['message'];
    }

    private function baseNegotiationQuery()
    {
        return Negotiation::query()->with($this->defaultRelations());
    }

    /**
     * @return array<int, string>
     */
    private function defaultRelations(): array
    {
        return [
            'order.client',
            'order.offers',
            'order.service.category',
            'order.service.service_category',
            'order.service.freelancer.skomda_student',
        ];
    }
}
