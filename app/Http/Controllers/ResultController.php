<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        $results = Result::with('order.service.freelancer')->get();
        return view('dashboard.admin.results', compact('results'));
    }

    // FREELANCER ONLY
    // TODO: selesaikan fitur result untuk freelancer (all), termasuk validasi dan return view yang sesuai
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Result $result)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Result $result)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Result $result)
    {
        //
    }

    // CLIENT ONLY
    // TODO: selesaikan fitur result untuk client (all), termasuk validasi dan return view yang sesuai
}
