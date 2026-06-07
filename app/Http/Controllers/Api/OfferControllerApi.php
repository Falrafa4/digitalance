<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OfferIndexRequest;
use App\Http\Requests\Api\OfferStoreRequest;
use App\Http\Requests\Api\OfferUpdateRequest;
use App\Http\Resources\OfferResource;
use App\Models\Offer;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class OfferControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get offers scoped to the authenticated role.
     */
    public function index(OfferIndexRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Offer::class);

        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $status = $validated['status'] ?? null;
        $page = (int) ($validated['page'] ?? 1);

        $offersQuery = $this->baseOfferQuery();

        // Filter berdasarkan role
        $user = $request->user();
        if ($user?->getRole() === 'client') {
            $offersQuery->whereHas('order', fn($orderQuery) => $orderQuery->where('client_id', $user->id));
        } elseif ($user?->getRole() === 'freelancer') {
            $offersQuery->whereHas('order', function ($orderQuery) use ($user) {
                $orderQuery->where(function ($q) use ($user) {
                    $q->where('freelancer_id', $user->id)
                        ->orWhereHas('service', fn($serviceQuery) => $serviceQuery->where('freelancer_id', $user->id));
                });
            });
        }

        // Filter status
        if (isset($status)) {
            $offersQuery->where('status', $status);
        }

        // Filter pencarian
        if ($q !== '') {
            $offersQuery->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('order.client', fn($clientQuery) => $clientQuery->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('order.service', fn($serviceQuery) => $serviceQuery->where('title', 'like', "%{$q}%"));
            });
        }

        // Pagination
        $offers = $offersQuery->latest()->paginate(12, ['*'], 'page', $page)->withQueryString();

        $offers->through(fn($offer) => (new OfferResource($offer))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'status' => $status,
                'per_page' => 12,
                'page' => $page,
            ],
            'offers' => $offers,
        ], 'Data penawaran berhasil diambil');
    }

    /**
     * Store a new offer for an authenticated freelancer.
     */
    public function store(OfferStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', Offer::class);

        $validated = $request->validated();
        $order = Order::with('service')->findOrFail($validated['order_id']);

        Gate::authorize('createForOrder', [Offer::class, $order]);

        $offer = Offer::create([
            'order_id' => $order->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'offered_price' => $validated['offered_price'],
            'deadline' => $validated['deadline'],
            'status' => 'Sent',
        ]);

        return $this->successResponse(
            new OfferResource($offer->fresh($this->defaultRelations())),
            'Penawaran berhasil dibuat',
            201
        );
    }

    /**
     * Get a single offer.
     */
    public function show(Offer $offer): JsonResponse
    {
        Gate::authorize('view', $offer);

        return $this->successResponse(
            new OfferResource($offer->load($this->defaultRelations())),
            'Detail penawaran berhasil diambil'
        );
    }

    /**
     * Update a sent offer owned by an authenticated freelancer.
     */
    public function update(OfferUpdateRequest $request, Offer $offer): JsonResponse
    {
        Gate::authorize('update', $offer);

        if ($offer->status !== 'Sent') {
            return $this->errorResponse('Penawaran yang sudah diproses tidak bisa diubah.', 409);
        }

        $offer->update($request->validated());

        return $this->successResponse(
            new OfferResource($offer->fresh($this->defaultRelations())),
            'Penawaran berhasil diperbarui'
        );
    }

    /**
     * Delete an offer for administrator.
     */
    public function destroy(Offer $offer): JsonResponse
    {
        Gate::authorize('delete', $offer);

        $offer->delete();

        return $this->successResponse(null, 'Penawaran berhasil dihapus');
    }

    /**
     * Accept a sent offer for an authenticated client.
     */
    public function accept(Offer $offer): JsonResponse
    {
        Gate::authorize('accept', $offer);

        return $this->transitionClientOffer($offer, 'Accepted', 'Penawaran berhasil disetujui');
    }

    /**
     * Reject a sent offer for an authenticated client.
     */
    public function reject(Offer $offer): JsonResponse
    {
        Gate::authorize('reject', $offer);

        return $this->transitionClientOffer($offer, 'Rejected', 'Penawaran berhasil ditolak');
    }

    private function transitionClientOffer(Offer $offer, string $status, string $message): JsonResponse
    {
        if ($offer->status !== 'Sent') {
            return $this->errorResponse('Penawaran yang sudah diproses tidak bisa diproses ulang.', 409);
        }

        $offer->update(['status' => $status]);

        return $this->successResponse(
            new OfferResource($offer->fresh($this->defaultRelations())),
            $message
        );
    }

    private function baseOfferQuery()
    {
        return Offer::query()->with($this->defaultRelations());
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
