<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        $offers = Offer::with('order.service.freelancer')->get();
        return view('dashboard.admin.offers', compact('offers'));
    }

    // FREELANCER ONLY
    // TODO: selesaikan fitur offer untuk freelancer (all), termasuk validasi dan return view yang sesuai
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offer $offer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        //
    }

    // CLIENT ONLY
    // TODO: selesaikan fitur offer untuk client (all), termasuk validasi dan return view yang sesuai
}
