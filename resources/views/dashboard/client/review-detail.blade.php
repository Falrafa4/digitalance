@extends('layouts.dashboard')
@section('title', 'Detail Review | Digitalance')

@section('content')
    <section class="animate-fadeUp max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('client.reviews.index') }}"
                class="text-slate-500 font-bold text-[13px] hover:text-slate-900 inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Detail Ulasan</h1>
            <p class="text-slate-500 mt-1">Order #{{ $review->order_id }} - {{ $review->order->service->title ?? '-' }}</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-[18px] p-6 space-y-6">
            <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Penilaian</p>
                    <p class="font-extrabold text-slate-900 text-[1.4rem]">
                        {{ number_format((float) ($review->rating ?? 0), 1) }}/5</p>
                </div>
                <span class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 font-bold text-[12px]">Ulasan</span>
            </div>

            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Komentar</p>
                <div class="rounded-[16px] border border-slate-200 bg-slate-50 p-5">
                    <p class="text-slate-700 text-[14px] leading-relaxed whitespace-pre-wrap">
                        {{ $review->comment ?: 'Tanpa komentar.' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-slate-50 rounded-[14px] p-4 border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Freelancer</p>
                    <p class="font-semibold text-slate-800">
                        {{ optional($review->order->service->freelancer->skomda_student)->name ?? '-' }}</p>
                </div>
                <div class="bg-slate-50 rounded-[14px] p-4 border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal</p>
                    <p class="font-semibold text-slate-800">{{ optional($review->created_at)->format('d M Y H:i') ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('client.orders.show', $review->order_id) }}"
                    class="flex-1 px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all text-center">
                    Lihat Order
                </a>
                <a href="{{ route('client.reviews.index') }}"
                    class="flex-1 px-5 py-3 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
                    Kembali
                </a>
            </div>
        </div>
    </section>
@endsection