@extends('layouts.dashboard')
@section('title', 'Review Management | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/reviews.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h1>Review Management</h1>
            <p>Kelola ulasan dan penilaian yang diberikan oleh pengguna platform.</p>
        </div>
        <div class="page-header-right">
            </div>
    </div>

    <div class="stats-row" id="stats-row"></div>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div class="filter-tabs" style="margin-bottom: 0;">
            <button class="filter-tab active" data-filter="all">Semua</button>
            <button class="filter-tab" data-filter="5">5 Bintang</button>
            <button class="filter-tab" data-filter="4">4 Bintang</button>
            <button class="filter-tab" data-filter="low">3 Bintang ke Bawah</button>
        </div>
        <div class="relative">
            <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
            <input type="text" id="review-search" placeholder="Cari client atau service..." class="pl-9 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
        </div>
    </div>

    <div class="cards-wrap" id="review-cards-wrap"></div>
    
    <div id="pagination-wrap" class="flex justify-end gap-2 mt-6"></div>

    <div id="review-empty" class="empty-state" style="display:none;">
        <div class="empty-icon"><i class="ri-star-line"></i></div>
        <h3>Tidak ada ulasan ditemukan</h3>
        <p>Coba ubah filter rating Anda.</p>
    </div>
@endsection

@section('modals')
    <div class="modal-overlay" id="detail-modal-overlay">
        <div class="modal-box" id="detail-modal-box"></div>
    </div>
@endsection

@section('scripts')
        <script>
            window.__REVIEWS_PAGE__ = { data: @json($reviews ?? []) };
        </script>
    <script src="{{ asset('js/dashboard/admin/reviews.js') }}"></script>
@endsection