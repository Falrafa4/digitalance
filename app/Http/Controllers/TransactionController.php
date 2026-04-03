<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{    
    /**
     * Tampilan untuk Admin (DASHBOARD ADMIN)
     */
    public function index()
    {
        // Tetap pakai eager loading agar nama client muncul dan anti N+1
        $transactions = Transaction::with([
            'order.client.skomda_student' 
        ])->latest()->get();

        return view('dashboard.admin.transactions', compact('transactions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Logika simpan transaksi baru
    }

    /**
     * Remove the specified resource from storage (FITUR ADMIN KITA)
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return back()->with('success', 'Data transaksi berhasil dihapus');
    }

    /**
     * Tampilan Transaksi untuk Freelancer
     */
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

    /**
     * Detail Transaksi berdasarkan Order ID untuk Freelancer
     */
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