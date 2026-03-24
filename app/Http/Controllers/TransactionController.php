<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{    
    // ADMIN ONLY
    public function index()
    {
        $transactions = Transaction::with('order')->get();
        return view('dashboard.admin.transactions', compact('transactions'));
    }

    // CLIENT ONLY
    // TODO: selesaikan fitur transaction untuk client (all), termasuk validasi dan return view yang sesuai
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    // FREELANCER ONLY
    // TODO: selesaikan fitur transaction untuk freelancer (all), termasuk validasi dan return view yang sesuai
}
