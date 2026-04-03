@extends('layouts.dashboard')
@section('title', 'Offers & Negotiations | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/offers.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h1>Offers & Negotiations</h1>
            <p>Pantau tawaran jasa masuk dan log pesan negosiasi antar pengguna.</p>
        </div>
    </div>

    <div class="stats-row" id="stats-row"></div>

    @section('Section Tabs')
        <div class="section-tabs">
            <button class="section-tab active" data-target="offers-section">
                <i class="ri-price-tag-3-line"></i> Data Tawaran (Offers)
            </button>
            <button class="section-tab" data-target="nego-section">
                <i class="ri-discuss-line"></i> Pesan Negosiasi
            </button>
        </div>
    @endsection

    @section('Offers Section')
        <div id="offers-section" class="tab-content active">
            <div class="filter-tabs" id="offers-filters">
                <button class="filter-tab active" data-filter="all">Semua</button>
                <button class="filter-tab" data-filter="pending">Pending</button>
                <button class="filter-tab" data-filter="accepted">Accepted</button>
                <button class="filter-tab" data-filter="rejected">Rejected</button>
            </div>
            <div class="table-wrap">
                <div class="overflow-x-auto">
                    <table class="data-table" id="offers-table">
                        <thead>
                            <tr>
                                <th>Offer ID</th>
                                <th>Klien & Freelancer</th>
                                <th>Detail Tawaran</th>
                                <th>Harga & Waktu</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="offers-tbody"></tbody>
                    </table>
                </div>
                <div id="offers-empty" class="empty-state" style="display:none;">
                    <div class="empty-icon"><i class="ri-price-tag-3-line"></i></div>
                    <h3>Tidak ada tawaran ditemukan</h3>
                    <p>Coba ubah kata kunci pencarian.</p>
                </div>
            </div>
        </div>
    @endsection

    @section('Negotiation Section')
        <div id="nego-section" class="tab-content">
            <div class="table-wrap">
                <div class="overflow-x-auto">
                    <table class="data-table" id="nego-table">
                        <thead>
                            <tr>
                                <th>Nego ID</th>
                                <th>Order ID Terkait</th>
                                <th>Pengirim (Sender)</th>
                                <th>Pesan Negosiasi</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="nego-tbody"></tbody>
                    </table>
                </div>
                <div id="nego-empty" class="empty-state" style="display:none;">
                    <div class="empty-icon"><i class="ri-discuss-line"></i></div>
                    <h3>Tidak ada negosiasi ditemukan</h3>
                    <p>Coba ubah kata kunci pencarian.</p>
                </div>
            </div>
        </div>
    @endsection
@endsection

@section('modals')
    <div class="modal-overlay" id="detail-modal-overlay">
        <div class="modal-box" id="detail-modal-box"></div>
    </div>
@endsection

@section('scripts')
    <script>
        window.__OFFERS_PAGE__ = {
            offers: @json($offers ?? []),
            negotiations: @json($negotiations ?? [])
        };
    </script>
    <script src="{{ asset('js/dashboard/admin/offers.js') }}"></script>
@endsection