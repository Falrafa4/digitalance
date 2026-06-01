@extends('layouts.dashboard')

@section('title', 'Detail Payout Order | Digitalance')

@section('styles')
<style>
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-paid {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .type-pill {
        display: inline-flex;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 600;
        background: #e2e8f0;
        color: #475569;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .info-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .info-card h3 {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-card p {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #059669;
    }

    .table-wrap {
        margin-top: 2rem;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .action-btn.primary {
        background-color: #0f766e;
        color: white;
    }

    .action-btn.primary:hover {
        background-color: #0d6e6d;
    }

    .action-btn.secondary {
        background-color: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .action-btn.secondary:hover {
        background-color: #e2e8f0;
    }

    .action-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endsection

@section('content')
<div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
    <div>
        <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Detail Payout Order</h1>
        <p class="text-slate-500 text-[0.95rem] mt-1">Informasi detail pembayaran ke freelancer untuk order ini.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.orders.index') }}" class="action-btn secondary">
            <i class="ri-arrow-left-line"></i> Kembali ke Daftar Order
        </a>
    </div>
</div>

<div class="bg-white rounded-[24px] border border-slate-200 overflow-hidden animate-fadeUp-3">
    <div class="p-6">
        <!-- Order Info -->
        <div class="mb-6">
            <h2 class="font-display text-[1.5rem] font-bold text-slate-900 mb-4">Informasi Order</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Layanan</h3>
                    <p class="text-[1.1rem] font-black text-slate-900 line-clamp-2">{{ $order->service->title ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Status Order</h3>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                        @if($order->status === 'Completed') bg-emerald-50 text-emerald-700 border border-emerald-100
                        @elseif($order->status === 'In Progress') bg-blue-50 text-blue-700 border border-blue-100
                        @elseif($order->status === 'Pending') bg-amber-50 text-amber-700 border border-amber-100
                        @elseif($order->status === 'Cancelled') bg-red-50 text-red-700 border border-red-100
                        @else bg-gray-50 text-gray-700 border border-gray-100
                        endif">
                        {{ $order->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Client & Freelancer -->
        <div class="mb-6">
            <h2 class="font-display text-[1.5rem] font-bold text-slate-900 mb-4">Klien & Freelancer</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pembeli (Klien)</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-[11px]">
                            C
                        </div>
                        <span class="text-[13px] font-bold text-slate-700">{{ $order->client->name ?? 'Tidak tersedia' }}</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Penyedia Layanan (Freelancer)</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-[11px]">
                            F
                        </div>
                        <span class="text-[13px] font-bold text-slate-700">
                            {{ $order->service->freelancer?->skomda_student->name ?? 'Tidak tersedia' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payout Info -->
        <div class="mb-6">
            <h2 class="font-display text-[1.5rem] font-bold text-slate-900 mb-4">Informasi Payout</h2>
            <div class="info-grid">
                <div class="info-card">
                    <h3><i class="ri-money-dollar-circle-line"></i> Nominal Payout</h3>
                    <p class="amount">Rp{{ number_format($payoutAmount, 0, ',', '.') }}</p>
                </div>
                <div class="info-card">
                    <h3><i class="ri-file-list-3-line"></i> Status Payout</h3>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                        @if($payoutDone) bg-emerald-50 text-emerald-700 border border-emerald-100
                        @else bg-amber-50 text-amber-700 border border-amber-100
                        endif">
                        <i class="ri-{{ $payoutDone ? 'check-circle-line' : 'time-line' }}"></i>
                        {{ $payoutDone ? 'Sudah Ditransfer' : 'Belum Ditransfer' }}
                    </span>
                </div>
                <div class="info-card">
                    <h3><i class="ri-calendar-line"></i> Tanggal Payout</h3>
                    <p class="text-[1.1rem] font-black text-slate-700">
                        @if($payoutDone && $payoutTransactions->isNotEmpty())
                            {{ $payoutTransactions->first()->created_at->format('d M Y') }}
                        @else
                            Belum ada payout
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Payout Transactions Table -->
        <div class="table-wrap">
            <h2 class="font-display text-[1.5rem] font-bold text-slate-900 mb-4">Riwayat Transaksi Payout</h2>
            <div class="bg-white rounded-[20px] border border-slate-200 shadow-sm overflow-hidden">
                <table class="data-table w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-[12px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <th class="px-6 py-4">ID Transaksi</th>
                            <th class="px-6 py-4">Nominal</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-[13px] text-slate-700">
                        @if($payoutTransactions->isNotEmpty())
                            @foreach($payoutTransactions as $trx)
                                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 font-medium">#{{ $trx->id }}</td>
                                    <td class="px-6 py-4 font-bold text-emerald-700">Rp{{ number_format($trx->amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4"><span class="type-pill">{{ $trx->type ?? '-' }}</span></td>
                                    <td class="px-6 py-4"><span class="status-pill 
                                        @if(($trx->status ?? '') == 'Paid') status-paid
                                        @elseif(($trx->status ?? '') == 'Pending') status-pending
                                        @elseif(($trx->status ?? '') == 'Failed') status-failed
                                        @else status-refund
                                        @endif">{{ $trx->status ?? '-' }}</span></td>
                                    <td class="px-6 py-4 text-slate-500">{{ $trx->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">Belum ada transaksi payout untuk order ini.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Button -->
        @if(!$payoutDone)
            <div class="mt-8">
                <form action="{{ route('admin.orders.transfer', $order->id) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="action-btn primary">
                        <i class="ri-exchange-dollar-line"></i> Catat Transfer ke Freelancer
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Any page-specific scripts can go here
</script>
@endsection