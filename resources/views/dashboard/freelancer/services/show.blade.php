@extends('layouts.dashboard')
@section('title', 'Service Detail | Digitalance')

@section('content')
    <div class="animate-fadeUp max-w-4xl mx-auto px-4 py-8">
        <div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
            <a href="{{ route('freelancer.services.index') }}"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-semibold text-sm">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <a href="{{ route('freelancer.services.edit', $service->id) }}"
                class="px-4 py-2 rounded-[11px] bg-[#0f766e] text-white font-bold text-[12.5px] hover:bg-[#0c615a] transition-all">
                <i class="ri-pencil-line"></i> Edit Service
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-[22px] p-7">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-5 mb-5">
                <div>
                    <h1 class="font-display text-[1.7rem] font-extrabold text-slate-900">{{ $service->title }}</h1>
                    <p class="text-slate-500 text-[13px] mt-1">Kategori: {{ $service->service_category->name ?? '-' }}</p>
                </div>
                <span
                    class="px-3 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-[12px]">{{ $service->status ?? 'Draft' }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div class="bg-slate-50 rounded-[14px] p-4 border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Min Price</p>
                    <p class="font-semibold text-slate-800">Rp
                        {{ number_format((float) ($service->price_min ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="bg-slate-50 rounded-[14px] p-4 border border-slate-100">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Max Price</p>
                    <p class="font-semibold text-slate-800">Rp
                        {{ number_format((float) ($service->price_max ?? 0), 0, ',', '.') }}</p>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 mb-3 text-[15px]">Deskripsi</h3>
                <div
                    class="bg-slate-50 rounded-[16px] p-5 border border-slate-100 text-slate-700 text-[14px] leading-relaxed whitespace-pre-wrap">
                    {{ $service->description ?: 'Belum ada deskripsi.' }}</div>
            </div>
        </div>
    </div>
@endsection