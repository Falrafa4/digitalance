<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource (ADMIN ONLY)
     */
    public function index()
    {
        // Pakai perbaikan audit kita agar nama asli muncul (eager loading skomda_student)
        $reviews = Review::with([
            'order.client', 
            'order.service.freelancer.skomda_student' 
        ])->latest()->get();

        return view('dashboard.admin.reviews', compact('reviews'));
    }

    /**
     * Store a newly created resource in storage 
     */
    public function store(Request $request)
    {
        // Logika simpan review
    }

    /**
     * Remove the specified resource from storage (FITUR ADMIN KITA)
     */
    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review berhasil dihapus');
    }

    /**
     * Tampilan Review untuk Freelancer
     */
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

    /**
     * Detail Review berdasarkan Order ID untuk Freelancer
     */
    public function showReviewByOrderId(string $orderId)
    {
        $review = Review::with('order.service.freelancer')
            ->where('order_id', $orderId)
            ->firstOrFail();

        return view('dashboard.freelancer.review-detail', compact('review'));
    }
}