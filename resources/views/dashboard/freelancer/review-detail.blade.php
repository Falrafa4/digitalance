@extends('layouts.dashboard')
@section('title', 'Review Detail | Digitalance')

@section('content')
    <div class="animate-fadeUp max-w-3xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('freelancer.reviews.index') }}"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-semibold text-sm">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-[24px] p-8 shadow-sm">
            <div class="flex items-start justify-between gap-4 pb-6 border-b border-slate-100 mb-6">
                <div>
                    <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900 mb-1">Detail Review</h1>
                    <p class="text-slate-500 text-[13px]">Order #{{ $review->order_id }} -
                        {{ $review->order->service->title ?? '-' }}</p>
                </div>
                <span class="px-3 py-1 rounded-lg bg-amber-50 text-amber-700 font-bold text-[12px]">
                    Rating {{ number_format((float) ($review->rating ?? 0), 1) }}/5
                </span>
            </div>

            <div class="mb-6">
                <h3 class="font-bold text-slate-900 mb-3 text-[15px]">Komentar Client</h3>
                <div class="bg-slate-50 rounded-[16px] p-5 border border-slate-100">
                    <p class="text-slate-700 text-[14px] leading-relaxed whitespace-pre-wrap">
                        {{ $review->comment ?: 'Tidak ada komentar.' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-slate-50 rounded-[14px] p-4 border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Client</p>
                    <p class="font-semibold text-slate-800">{{ $review->order->client->name ?? '-' }}</p>
                </div>
                <div class="bg-slate-50 rounded-[14px] p-4 border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal</p>
                    <p class="font-semibold text-slate-800">{{ optional($review->created_at)->format('d M Y H:i') ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection