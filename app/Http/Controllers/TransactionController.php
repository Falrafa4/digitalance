<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // ADMIN ONLY
    public function index(Request $request)
    {
        $status = strtolower(trim((string) $request->query('status', 'all')));
        $search = trim((string) $request->query('q', ''));

        // PERBAIKAN: Tambahkan order.service ke dalam eager loading
        $baseQuery = Transaction::with(['order.client', 'order.service']);

        if ($status !== 'all' && in_array($status, ['paid', 'pending', 'failed', 'refund'], true)) {
            $baseQuery->whereRaw('LOWER(status) = ?', [$status]);
        }
        
        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('order_id', 'like', '%' . $search . '%');
            });
        }

        $transactions = (clone $baseQuery)->latest()->paginate(15)->withQueryString();

        $transactionStats = [
            'total' => (clone $baseQuery)->count(),
            'paid' => (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['paid'])->count(),
            'pending' => (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['pending'])->count(),
            'failed' => (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['failed'])->count(),
            'revenue' => (float) (clone $baseQuery)
                ->whereRaw('LOWER(status) = ?', ['paid'])
                ->whereRaw('LOWER(type) <> ?', ['refund'])
                ->sum('amount'),
            'refund' => (float) (clone $baseQuery)
                ->whereRaw('LOWER(type) = ?', ['refund'])
                ->whereRaw('LOWER(status) = ?', ['paid'])
                ->sum('amount'),
        ];

        return view('dashboard.admin.transactions', compact('transactions', 'transactionStats', 'status', 'search'));
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
        $freelancer = auth('freelancer')->user();

        $transaction = Transaction::with('order.service.freelancer')
            ->whereHas('order', function ($query) use ($orderId, $freelancer) {
                $query->where('id', $orderId)
                    ->whereHas('service', function ($q) use ($freelancer) {
                        $q->where('freelancer_id', $freelancer->id);
                    });
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
        $validated = $request->validated();

        $transaction = Transaction::create([
            'order_id' => $validated['order_id'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'status' => $validated['status'] ?? 'Pending',
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil dibuat');
    }
}
