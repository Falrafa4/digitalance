<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReviewIndexRequest;
use App\Http\Requests\Api\ReviewStoreRequest;
use App\Http\Requests\Api\ReviewUpdateRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Order;
use App\Models\Review;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ReviewControllerApi extends Controller
{
    use ApiResponse;

    /**
     * Get reviews scoped to the authenticated role.
     */
    public function index(ReviewIndexRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Review::class);

        $validated = $request->validated();
        $q = trim((string) ($validated['q'] ?? ''));
        $rating = $validated['rating'] ?? null;
        $orderId = $validated['order_id'] ?? null;
        $page = (int) ($validated['page'] ?? 1);
        $user = $request->user();

        $reviewsQuery = $this->baseReviewQuery();

        if ($user?->getRole() === 'client') {
            $reviewsQuery->whereHas('order', fn ($orderQuery) => $orderQuery->where('client_id', $user->id));
        } elseif ($user?->getRole() === 'freelancer') {
            $reviewsQuery->whereHas('order', function ($orderQuery) use ($user) {
                $orderQuery->where(function ($query) use ($user) {
                    $query->where('freelancer_id', $user->id)
                        ->orWhereHas('service', fn ($serviceQuery) => $serviceQuery->where('freelancer_id', $user->id));
                });
            });
        }

        if ($orderId) {
            $reviewsQuery->where('order_id', $orderId);
        }

        if ($rating) {
            if ($rating === 'low') {
                $reviewsQuery->where('rating', '<=', 3);
            } else {
                $reviewsQuery->where('rating', (int) $rating);
            }
        }

        if ($q !== '') {
            $reviewsQuery->where(function ($query) use ($q) {
                $query->where('comment', 'like', "%{$q}%")
                    ->orWhereHas('order.client', fn ($clientQuery) => $clientQuery->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('order.service', fn ($serviceQuery) => $serviceQuery->where('title', 'like', "%{$q}%"));
            });
        }

        $reviews = $reviewsQuery
            ->latest()
            ->paginate(12, ['*'], 'page', $page)
            ->withQueryString();

        $reviews->through(fn ($review) => (new ReviewResource($review))->toArray($request));

        return $this->successResponse([
            'filters' => [
                'q' => $q,
                'rating' => $rating,
                'order_id' => $orderId,
                'per_page' => 12,
                'page' => $page,
            ],
            'reviews' => $reviews,
        ], 'Data review berhasil diambil');
    }

    /**
     * Store a new review for an authenticated client.
     */
    public function store(ReviewStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', Review::class);

        $validated = $request->validated();
        $order = Order::with('review')->findOrFail($validated['order_id']);

        Gate::authorize('createForOrder', [Review::class, $order]);

        if ($order->status !== 'Completed') {
            return $this->errorResponse('Review hanya bisa dikirim setelah order selesai.', 422);
        }

        if ($order->review) {
            return $this->errorResponse('Review untuk order ini sudah ada.', 422);
        }

        $review = Review::create($validated);

        return $this->successResponse(
            new ReviewResource($review->fresh($this->defaultRelations())),
            'Review berhasil ditambahkan',
            201
        );
    }

    /**
     * Get a single review.
     */
    public function show(Review $review): JsonResponse
    {
        Gate::authorize('view', $review);

        return $this->successResponse(
            new ReviewResource($review->load($this->defaultRelations())),
            'Detail review berhasil diambil'
        );
    }

    /**
     * Update a review for administrator.
     */
    public function update(ReviewUpdateRequest $request, Review $review): JsonResponse
    {
        Gate::authorize('update', $review);

        $review->update($request->validated());

        return $this->successResponse(
            new ReviewResource($review->fresh($this->defaultRelations())),
            'Review berhasil diperbarui'
        );
    }

    /**
     * Delete a review for administrator or owner client.
     */
    public function destroy(Review $review): JsonResponse
    {
        Gate::authorize('delete', $review);

        $review->delete();

        return $this->successResponse(null, 'Review berhasil dihapus.');
    }

    private function baseReviewQuery()
    {
        return Review::query()->with($this->defaultRelations());
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
