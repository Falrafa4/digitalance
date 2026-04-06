@extends('layouts.dashboard')
@section('title', 'Portfolios Management | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/portofolios.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h1>Portfolios Management</h1>
            <p>Kelola portofolio freelancer. Setujui atau tolak karya baru yang diunggah.</p>
        </div>
        <div class="page-header-right">
            <button class="btn-primary" id="btn-add-port">
                <i class="ri-add-line"></i> Tambah Portofolio
            </button>
        </div>
    </div>

    <div class="stats-row" id="stats-row"></div>

    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">Semua</button>
        <button class="filter-tab" data-filter="pending">Pending</button>
        <button class="filter-tab" data-filter="approved">Approved</button>
        <button class="filter-tab" data-filter="rejected">Rejected</button>
    </div>

    <div class="cards-wrap" id="port-cards-wrap"></div>

    <div id="port-empty" class="empty-state" style="display:none;">
        <div class="empty-icon"><i class="ri-folder-user-line"></i></div>
        <h3>Tidak ada portofolio ditemukan</h3>
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
        window.__PORTOFOLIOS_PAGE__ = { data: @json($portofolios ?? []) };
    </script>
    <script src="{{ asset('js/dashboard/admin/portofolios.js') }}"></script>
@endsection