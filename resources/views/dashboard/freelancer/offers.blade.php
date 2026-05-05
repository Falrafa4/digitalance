@extends('layouts.dashboard')
@section('title', 'Offers | Digitalance')

@section('content')
<div class="animate-fadeUp flex-1 px-8 py-7 overflow-y-auto">
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900 leading-tight">My Offers</h1>
            <p class="text-slate-500 mt-1 text-[0.95rem]">Pantau penawaran harga yang kamu ajukan ke klien.</p>
        </div>
        <!-- Modal Trigger for adding a new offer could be placed here if needed -->
    </div>

    @if($offers->isEmpty())
        <div class="text-center py-16 px-5 bg-white border-2 border-dashed border-slate-200 rounded-[20px]">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mx-auto mb-4">
                <i class="ri-price-tag-3-line"></i>
            </div>
            <h3 class="font-display text-[1.15rem] font-bold text-slate-700 mb-1">Belum Ada Penawaran</h3>
            <p class="text-[13px] text-slate-400">Kamu belum mengajukan penawaran harga untuk order apapun.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($offers as $offer)
                <div class="bg-white border border-slate-200 rounded-[20px] p-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-start justify-between mb-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Order #{{ $offer->order_id }}</span>
                            <h3 class="font-bold text-slate-900">{{ $offer->order->service->title ?? 'Service' }}</h3>
                        </div>
                        @php
                            $statusColors = [
                                'Sent' => 'bg-amber-100 text-amber-700',
                                'Accepted' => 'bg-emerald-100 text-emerald-700',
                                'Rejected' => 'bg-red-100 text-red-700',
                            ];
                            $color = $statusColors[$offer->status] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $color }}">
                            {{ $offer->status }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 overflow-hidden">
                            <i class="ri-briefcase-line text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase">Klien</p>
                            <p class="text-[13px] font-bold text-slate-800">{{ $offer->order->client->user->name ?? $offer->order->client->name ?? 'Klien' }}</p>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4 mb-4">
                        <p class="text-[11px] font-bold text-slate-400 uppercase mb-1">Harga Penawaran</p>
                        <p class="text-[18px] font-extrabold text-[#0f766e]">Rp {{ number_format($offer->offered_price, 0, ',', '.') }}</p>
                    </div>

                    <div class="bg-white border border-slate-100 rounded-xl p-4 mb-4 text-[13px] text-slate-600 truncate" title="{{ $offer->message }}">
                        {{ $offer->message ?: 'Tidak ada pesan' }}
                    </div>

                    @if($offer->status === 'Sent')
                        <!-- Button trigger modal untuk edit offer (jika diinginkan, cukup arahkan/ganti ke form action atau logic JS) -->
                        <div class="text-center text-xs font-bold text-amber-600 bg-amber-50 rounded-lg p-2">Menunggu konfirmasi klien</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
