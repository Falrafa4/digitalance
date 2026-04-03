<?php

namespace App\Http\Controllers;

use App\Models\Negotiation;
use Illuminate\Http\Request;

class NegotiationController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        $negotiations = Negotiation::with('order.service.freelancer')->get();
        return view('dashboard.admin.negotiations', compact('negotiations'));
    }

    public function store(Request $request)
    {
    }       

    /**
     * Display the specified resource.
     */
    public function show(Negotiation $negotiation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Negotiation $negotiation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Negotiation $negotiation)
    {
        //
    }

    // FREELANCER ONLY
    // TODO: selesaikan fitur negosiasi untuk freelancer (all), termasuk validasi dan return view yang sesuai
}
