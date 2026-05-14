@extends('layouts.dashboard')
@section('title', 'Service Detail | Digitalance')

@section('content')
    <div class="animate-fadeUp max-w-4xl mx-auto px-4 py-8">
        <div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
            <a href="{{ route('freelancer.services.index') }}"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-semibold text-sm">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <div class="flex items-center gap-2">
                @if($service->status === 'Draft')
                <form id="form-submit-service" action="{{ route('freelancer.services.submit', $service->id) }}" method="POST">
                    @csrf
                    <button type="button" onclick="window.confirmSubmitService()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-orange-500 text-white font-semibold hover:bg-orange-600 transition-colors shadow-orange-sm">
                        <i class="ri-send-plane-fill"></i> Submit for Review
                    </button>
                </form>
                @endif
                <a href="{{ route('freelancer.services.edit', $service->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#0f766e] text-white font-semibold hover:bg-teal-800 transition-colors">
                    <i class="ri-pencil-line"></i> Edit Service
                </a>
            </div>
        </div>

        @if($service->reject_reason)
        <div class="mb-6 p-5 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-4 shadow-sm animate-pulse-slow">
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                <i class="ri-error-warning-line text-xl"></i>
            </div>
            <div>
                <h4 class="text-amber-900 font-bold text-sm mb-1">Catatan Perbaikan Admin</h4>
                <p class="text-amber-800 text-[13px] leading-relaxed font-medium">
                    {{ $service->reject_reason }}
                </p>
                <p class="text-amber-600 text-[11px] mt-2 font-bold uppercase tracking-wider italic">* Silakan edit dan ajukan kembali layanan ini agar dapat dipublikasikan.</p>
            </div>
        </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl p-8">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-6 mb-6">
                <div class="flex-1">
                    <h1 class="font-display text-[2rem] font-extrabold text-slate-900 mb-2">{{ $service->title }}</h1>
                    <p class="text-slate-500 text-sm">Kategori: <span
                            class="font-semibold">{{ $service->service_category->name ?? '-' }}</span></p>
                </div>
                <x-ui.status-badge :status="$service->status ?? 'Draft'" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-slate-50 rounded-lg p-5 border border-slate-100">
                    <p class="text-xs font-semibold text-slate-500 uppercase mb-1">Min Price</p>
                    <p class="text-lg font-bold text-slate-900">
                        Rp{{ number_format((float) ($service->price_min ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="bg-slate-50 rounded-lg p-5 border border-slate-100">
                    <p class="text-xs font-semibold text-slate-500 uppercase mb-1">Max Price</p>
                    <p class="text-lg font-bold text-slate-900">
                        Rp{{ number_format((float) ($service->price_max ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="bg-slate-50 rounded-lg p-5 border border-slate-100">
                    <p class="text-xs font-semibold text-slate-500 uppercase mb-1">Delivery Time</p>
                    <p class="text-lg font-bold text-slate-900">{{ $service->delivery_time ?? '-' }} days</p>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 mb-3">Description</h3>
                <div
                    class="bg-slate-50 rounded-lg p-5 border border-slate-100 text-slate-700 leading-relaxed whitespace-pre-wrap">
                    {{ $service->description ?: 'No description provided.' }}
                </div>
            </div>
        </div>
    </div>
    <script>
        window.confirmSubmitService = async function() {
            if (await window.customConfirm('Yakin ingin mengajukan layanan ini untuk ditinjau admin? Pastikan semua informasi sudah benar.')) {
                document.getElementById('form-submit-service').submit();
            }
        };
    </script>
@endsection