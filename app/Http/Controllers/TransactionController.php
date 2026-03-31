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

    // FREELANCER ONLY
    public function freelancerIndex(Request $request)
    {
        $freelancer = auth('freelancer')->user();

        $transactions = Transaction::with('order.service')
            ->whereHas('order.service', function ($query) use ($freelancer) {
                $query->where('freelancer_id', $freelancer->id);
            })
            ->get();

        return view('dashboard.freelancer.transactions', compact('transactions'));
    }

    public function showTransactionByOrderId(string $orderId)
    {
        $transaction = Transaction::with('order.service.freelancer')
            ->whereHas('order', function ($query) use ($orderId) {
                $query->where('id', $orderId);
            })
            ->firstOrFail();

        return view('dashboard.freelancer.transaction-detail', compact('transaction'));
    }
}
