<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // ADMIN ONLY
    public function index(Request $request)
    {
        $status = strtolower(trim((string) $request->query('status', 'all')));
        $search = trim((string) $request->query('q', ''));

        // PERBAIKAN: Tambahkan order.service ke dalam eager loading
        $baseQuery = Transaction::with(['order.client', 'order.service.freelancer.skomda_student'])
            ->whereHas('order.service', function ($query) {
                $query->whereNotNull('freelancer_id')
                    ->whereHas('freelancer');
            });

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

    public function export(Request $request)
    {
        $status = strtolower(trim((string) $request->query('status', 'all')));
        $search = trim((string) $request->query('q', ''));

        $baseQuery = Transaction::with(['order.client', 'order.service.freelancer.skomda_student'])
            ->whereHas('order.service', function ($query) {
                $query->whereNotNull('freelancer_id')
                    ->whereHas('freelancer');
            });

        if ($status !== 'all' && in_array($status, ['paid', 'pending', 'failed', 'refund'], true)) {
            $baseQuery->whereRaw('LOWER(status) = ?', [$status]);
        }

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('order_id', 'like', '%' . $search . '%');
            });
        }

        $rows = (clone $baseQuery)->latest()->get();
        $filename = 'payout-report-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['transaction_id', 'order_id', 'client', 'freelancer', 'amount', 'type', 'status', 'created_at']);

            foreach ($rows as $trx) {
                fputcsv($handle, [
                    $trx->id,
                    $trx->order_id,
                    $trx->order?->client?->name ?? '-',
                    $trx->order?->service?->freelancer?->skomda_student?->name ?? '-',
                    $trx->amount,
                    $trx->type,
                    $trx->status,
                    optional($trx->created_at)->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // FREELANCER ONLY
    public function freelancerIndex(Request $request)
    {
        $freelancer = auth('freelancer')->user();

        $transactions = Transaction::with('order.service.freelancer.skomda_student')
            ->whereHas('order.service', function ($query) use ($freelancer) {
                $query->where('freelancer_id', $freelancer->id)
                    ->whereNotNull('freelancer_id')
                    ->whereHas('freelancer');
            })
            ->get();

        return view('dashboard.freelancer.transactions', compact('transactions'));
    }

    public function showTransactionByOrderId(string $orderId)
    {
        $freelancer = auth('freelancer')->user();

        $transaction = Transaction::with('order.service.freelancer.skomda_student')
            ->whereHas('order', function ($query) use ($orderId, $freelancer) {
                $query->where('id', $orderId)
                    ->whereHas('service', function ($q) use ($freelancer) {
                        $q->where('freelancer_id', $freelancer->id)
                            ->whereNotNull('freelancer_id')
                            ->whereHas('freelancer');
                    });
            })
            ->firstOrFail();

        return view('dashboard.freelancer.transaction-detail', compact('transaction'));
    }

    // CLIENT ONLY
    public function clientIndex()
    {
        $client = auth('client')->user();

        $transactions = Transaction::with('order.service.freelancer.skomda_student')
            ->whereHas('order', fn($q) => $q->where('client_id', $client->id))
            ->whereHas('order.service', function ($query) {
                $query->whereNotNull('freelancer_id')
                    ->whereHas('freelancer');
            })
            ->latest()
            ->get();

        return view('dashboard.client.payments.index', compact('transactions'));
    }

    public function clientShowByOrderId(Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client->id, 403);

        abort_unless($order->service_id && $order->service?->freelancer_id, 404);

        $transaction = Transaction::with('order.service.freelancer.skomda_student')
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

    public function adminTransfer(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:2000',
        ]);

        abort_unless($order->status === 'Completed', 422, 'Transfer hanya tersedia untuk order yang sudah completed.');

        if (!$order->service?->freelancer_id) {
            return back()->with('error', 'Freelancer untuk order ini tidak ditemukan.');
        }

        $alreadyTransferred = Transaction::where('order_id', $order->id)
            ->where('type', 'Full')
            ->where('status', 'Paid')
            ->exists();

        if ($alreadyTransferred) {
            return back()->with('warning', 'Transfer untuk order ini sudah pernah dicatat.');
        }

        $amount = $validated['amount'] ?? (float) ($order->agreed_price ?? 0);

        DB::transaction(function () use ($order, $amount, $validated) {
            Transaction::create([
                'order_id' => $order->id,
                'amount' => $amount,
                'type' => 'Full',
                'status' => 'Paid',
            ]);

            $order->update([
                'status' => 'Completed',
            ]);

            Notification::create([
                'title' => 'Transfer ke Freelancer Sudah Dicatat',
                'message' => 'Transfer untuk order #' . $order->id . ' sebesar Rp ' . number_format($amount, 0, ',', '.') . ' sudah dicatat oleh admin.',
                'type' => 'success',
                'role' => 'freelancer',
                'user_id' => $order->service?->freelancer_id,
                'link' => route('freelancer.transactions.showByOrderId', $order->id),
            ]);
        });

        // No rekening in admin: this records the payout as completed in the system.
        return back()->with('success', 'Transfer ke freelancer berhasil dicatat.');
    }

    public function adminPayoutDetail(Order $order)
    {
        $order->load(['client', 'service.freelancer.skomda_student', 'transactions']);

        $payoutTransactions = $order->transactions
            ->where('type', 'Full')
            ->where('status', 'Paid')
            ->values();

        $payoutDone = $payoutTransactions->isNotEmpty();
        $payoutAmount = $payoutDone ? (float) $payoutTransactions->first()->amount : (float) ($order->agreed_price ?? 0);

        return view('dashboard.admin.order-payout-detail', compact('order', 'payoutTransactions', 'payoutDone', 'payoutAmount'));
    }
}
