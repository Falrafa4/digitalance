@extends('layouts.dashboard')
@section('title', 'Service Management | Digitalance')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/services.css') }}">
@endsection

@section('content')
    <div class="content-scroll flex-1 px-8 py-7 overflow-y-auto">
        <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
            <div>
                <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Services Management</h1>
                <p class="text-slate-500 text-[0.95rem] mt-1">
                    Kelola dan pantau seluruh layanan yang ditawarkan oleh freelancer.
                </p>
            </div>
            <div class="header-actions">
                </div>
        </div>

        <div id="stats-row" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 animate-fadeUp-2">
            </div>

        <div class="flex items-center justify-start gap-2 mb-6 flex-wrap animate-fadeUp-2" id="filter-tabs">
            <button class="filter-tab px-[20px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all active" data-filter="all">Semua Layanan</button>
            <button class="filter-tab px-[20px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="pending">Pending</button>
            <button class="filter-tab px-[20px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="approved">Approved</button>
            <button class="filter-tab px-[20px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="rejected">Rejected</button>
        </div>

        <div class="cards-wrap grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fadeUp-3" id="service-cards-wrap">
            </div>

        <div id="service-empty" class="py-20 text-center hidden">
            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-tools-line text-3xl"></i>
            </div>
            <h3 class="text-slate-900 font-bold">Layanan Tidak Ditemukan</h3>
            <p class="text-slate-400 text-sm">Tidak ada layanan dengan status ini.</p>
        </div>
    </div>
@endsection

@section('modals')
    <div class="modal-overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-200" id="detail-modal-overlay">
        <div class="modal-box bg-white rounded-[28px] w-full max-w-[550px] shadow-2xl overflow-hidden" id="detail-modal-box">
            </div>
    </div>
    <!-- MODAL: Delete Service -->
<div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200" id="modal-delete-service">
    <div class="modal-box bg-white rounded-[18px] w-full max-w-[400px] shadow-2xl overflow-hidden">
        <div class="px-[26px] pt-[30px] pb-[24px] text-center">
            <div class="w-[72px] h-[72px] mx-auto mb-5 bg-red-50 rounded-full flex items-center justify-center text-[2rem] text-red-500">
                <i class="ri-error-warning-fill"></i>
            </div>
            <h3 class="font-display text-[1.2rem] font-extrabold text-slate-900 mb-2">Hapus Layanan?</h3>
            <p class="text-[13.5px] text-slate-500 leading-relaxed" id="delete-service-text"></p>
        </div>
        <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50">
            <button onclick="window.closeModal('modal-delete-service')" class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all">Batal</button>
            <button id="btn-confirm-delete-service" class="flex-1 py-[11px] rounded-[11px] bg-red-500 text-white font-bold text-[13px] cursor-pointer border-none shadow-[0_3px_10px_rgba(239,68,68,.25)] hover:bg-red-600 transition-all">Ya, Hapus</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        window.__SERVICES_PAGE__ = { data: @json($services ?? []) };
    </script>
    <script src="{{ asset('js/dashboard/admin/services.js') }}"></script>
@endsection
