@extends('layouts.dashboard')
@section('title', 'Transaction Detail | Digitalance')

@section('content')
    <div class="animate-fadeUp max-w-3xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('freelancer.transactions.index') }}"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-semibold text-sm">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-[24px] p-8 shadow-sm">
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 mb-6">
                <div>
                    <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900 mb-1">Detail Transaksi</h1>
                    <p class="text-slate-500 text-[13px]">Order #{{ $transaction->order_id }} -
                        {{ $transaction->order->service->title ?? '-' }}</p>
                </div>
                @php
                    $status = $transaction->status ?? 'Pending';
                    $statusClass = match ($status) {
                        'Paid', 'Success' => 'bg-emerald-50 text-emerald-700',
                        'Failed', 'Cancelled' => 'bg-red-50 text-red-700',
                        default => 'bg-amber-50 text-amber-700',
                    };
                @endphp
                <span class="px-3 py-1 rounded-lg font-bold text-[12px] {{ $statusClass }}">{{ $status }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-slate-50 rounded-[14px] p-4 border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Transaction ID</p>
                    <p class="font-semibold text-slate-800">#{{ $transaction->id }}</p>
                </div>
                <div class="bg-slate-50 rounded-[14px] p-4 border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Amount</p>
                    <p class="font-semibold text-slate-800">Rp
                        {{ number_format((float) ($transaction->amount ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="bg-slate-50 rounded-[14px] p-4 border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Type</p>
                    <p class="font-semibold text-slate-800">{{ $transaction->type ?? '-' }}</p>
                </div>
                <div class="bg-slate-50 rounded-[14px] p-4 border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal</p>
                    <p class="font-semibold text-slate-800">
                        {{ optional($transaction->created_at)->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection