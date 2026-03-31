<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        $reviews = Review::with('order.service.freelancer')->get();
        return view('dashboard.admin.reviews', compact('reviews'));
    }

    // CLIENT ONLY
    // TODO: selesaikan fitur review untuk client (all), termasuk validasi dan return view yang sesuai

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
}
