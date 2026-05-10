<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // ADMIN ONLY
    public function index(Request $request)
    {
        $rating = $request->query('rating');
        $search = $request->query('q');

        $query = Review::with([
            'order.client',
            'order.service.freelancer.skomda_student'
        ]);

        if ($rating) {
            if ($rating === 'low') {
                $query->where('rating', '<=', 3);
            } else {
                $query->where('rating', $rating);
            }
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('order.client', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $reviews = $query->latest()->paginate(12)->withQueryString();

        return view('dashboard.admin.reviews', compact('reviews'));
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->back()->with('success', 'Review berhasil dihapus.');
    }

    public function clientIndex()
    {
        $client = auth('client')->user();

        $reviews = Review::with(['order.service'])
            ->whereHas('order', function ($q) use ($client) {
                $q->where('client_id', $client->id);
            })
            ->latest()
            ->paginate(12);

        return view('dashboard.client.reviews', compact('reviews'));
    }

    public function clientShowByOrderId(string $orderId)
    {
        $review = Review::with('order.service.freelancer')
            ->where('order_id', $orderId)
            ->firstOrFail();

        return view('dashboard.client.review-detail', compact('review'));
    }
    
    public function clientCreate(Request $request, string $orderId)
    {
        // Pastikan order tersebut milik client yang sedang login
        $client = auth('client')->user();
        $order = $client->orders()->where('id', $orderId)->firstOrFail();

        // Tampilkan form review untuk client
        return view('dashboard.client.reviews.create', compact('order'));
    }
    
    public function clientStore(StoreReviewRequest $request)
    {
        // Logika simpan review
        $request->validated();

        Review::create($request->only(['order_id', 'rating', 'comment']));

        return redirect()->back()->with('success', 'Review berhasil ditambahkan');
    }

    // FREELANCER ONLY
    public function freelancerIndex(Request $request)
    {
        $freelancer = auth('freelancer')->user();

        $reviews = Review::with('order.service')
            ->whereHas('order.service', function ($query) use ($freelancer) {
                $query->where('freelancer_id', $freelancer->id);
            })
            ->get();

        return view('dashboard.freelancer.reviews', compact('reviews'));
    }

    public function showReviewByOrderId(string $orderId)
    {
        $review = Review::with('order.service.freelancer')
            ->where('order_id', $orderId)
            ->firstOrFail();

        return view('dashboard.freelancer.review-detail', compact('review'));
    }

    public function clientDestroy(string $orderId)
    {
        $client = auth('client')->user();
        $review = Review::where('order_id', $orderId)->firstOrFail();

        if ($review->order->client_id !== $client->id) {
            abort(403, 'Akses ditolak.');
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review berhasil dihapus.');
    }
}