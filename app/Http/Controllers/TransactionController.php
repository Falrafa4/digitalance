<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use App\Models\Order;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // ADMIN ONLY
    public function index()
    {
        $transactions = Transaction::with([
            'order.client'
        ])->latest()->get();

        return view('dashboard.admin.transactions', compact('transactions'));
    }

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

    // CLIENT ONLY
    public function clientIndex()
    {
        $client = auth('client')->user();

        $transactions = Transaction::with('order.service')
            ->whereHas('order', fn($q) => $q->where('client_id', $client->id))
            ->latest()
            ->get();

        return view('dashboard.client.payments.index', compact('transactions'));
    }

    public function clientShowByOrderId(Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        $transaction = Transaction::with('order.service')
            ->where('order_id', $order->id)
            ->latest()
            ->firstOrFail();

        return view('dashboard.client.payments.show', compact('transaction'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $request->validated();

        $transaction = Transaction::create([
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'type' => $request->type,
            'status' => $request->status ?? 'Pending',
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil dibuat');
    }
}