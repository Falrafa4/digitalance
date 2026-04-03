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
            <button class="btn-primary" id="btn-add-review">
                <i class="ri-add-line"></i> Tambah Review
            </button>
        </div>
    </div>

    <div class="stats-row" id="stats-row"></div>

    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">Semua</button>
        <button class="filter-tab" data-filter="5">5 Bintang</button>
        <button class="filter-tab" data-filter="4">4 Bintang</button>
        <button class="filter-tab" data-filter="low">3 Bintang ke Bawah</button>
    </div>

    <div class="cards-wrap" id="review-cards-wrap"></div>

    <div id="review-empty" class="empty-state" style="display:none;">
        <div class="empty-icon"><i class="ri-star-line"></i></div>
        <h3>Tidak ada ulasan ditemukan</h3>
        <p>Coba ubah filter atau kata kunci pencarian.</p>
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