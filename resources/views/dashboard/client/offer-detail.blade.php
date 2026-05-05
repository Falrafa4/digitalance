@extends('layouts.dashboard')
@section('title', 'Offer Detail | Digitalance')

@section('content')
<div class="animate-fadeUp max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('client.offers.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-semibold text-sm">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-[24px] p-8 shadow-sm">
        <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-100">
            <div>
                <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900 mb-1">Detail Penawaran</h1>
                <p class="text-slate-500 text-[13px]">Order #{{ $offer->order_id }} - {{ $offer->order->service->title ?? '-' }}</p>
            </div>
            @php
                $statusColors = [
                    'Sent' => 'bg-amber-100 text-amber-700',
                    'Accepted' => 'bg-emerald-100 text-emerald-700',
                    'Rejected' => 'bg-red-100 text-red-700',
                ];
                $color = $statusColors[$offer->status] ?? 'bg-slate-100 text-slate-600';
            @endphp
            <span class="px-4 py-1.5 rounded-xl text-[12.5px] font-bold {{ $color }}">
                {{ $offer->status }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
            <div class="bg-slate-50 rounded-[16px] p-5">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Freelancer</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-[#0f766e]">
                        <i class="ri-user-smile-line text-lg"></i>
                    </div>
                    <p class="font-bold text-slate-900">{{ $offer->order->service->freelancer->user->name ?? $offer->order->service->freelancer->name ?? 'Freelancer' }}</p>
                </div>
            </div>
            <div class="bg-emerald-50 rounded-[16px] p-5">
                <p class="text-[11px] font-bold text-emerald-600/70 uppercase tracking-wider mb-1">Harga Ditawarkan</p>
                <p class="text-[24px] font-display font-extrabold text-emerald-700 leading-none mt-2">Rp {{ number_format($offer->offered_price, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="font-bold text-slate-900 mb-3 text-[15px]">Pesan dari Freelancer</h3>
            <div class="bg-slate-50 rounded-[16px] p-5 border border-slate-100">
                <p class="text-slate-700 text-[14px] leading-relaxed whitespace-pre-wrap">{{ $offer->message ?: 'Tidak ada pesan yang disertakan.' }}</p>
            </div>
        </div>

        @if($offer->status === 'Sent')
            <div class="flex gap-4 pt-6 border-t border-slate-100">
                <form action="{{ route('client.offers.reject', $offer->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" onclick="return confirm('Tolak penawaran ini?')" class="w-full py-3.5 rounded-[14px] bg-white border border-red-200 text-red-600 font-bold text-[14px] hover:bg-red-50 hover:border-red-300 transition-all">
                        Tolak
                    </button>
                </form>
                <form action="{{ route('client.offers.accept', $offer->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" onclick="return confirm('Terima penawaran ini?')" class="w-full py-3.5 rounded-[14px] bg-[#0f766e] text-white font-bold text-[14px] shadow-teal-sm hover:bg-[#0a5e58] hover:-translate-y-0.5 transition-all">
                        Terima Penawaran
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
