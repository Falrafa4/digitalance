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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        //
    }

    // FREELANCER ONLY
    // TODO: selesaikan fitur review untuk freelancer (all), termasuk validasi dan return view yang sesuai
}
