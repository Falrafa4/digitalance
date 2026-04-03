@extends('layouts.dashboard')
@section('title', 'Service Management | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/services.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h1>Services Management</h1>
            <p>Kelola layanan freelancer. Setujui atau tolak layanan baru dengan alasan.</p>
        </div>
        <div class="page-header-right">
            <button class="btn-primary" id="btn-add-service">
                <i class="ri-add-line"></i> Tambah Layanan
            </button>
        </div>
    </div>

    <div class="stats-row" id="stats-row"></div>

    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">Semua</button>
        <button class="filter-tab" data-filter="pending">Pending</button>
        <button class="filter-tab" data-filter="approved">Approved</button>
        <button class="filter-tab" data-filter="rejected">Rejected</button>
        <button class="filter-tab" data-filter="draft">Draft</button>
    </div>

    <div class="cards-wrap" id="service-cards-wrap"></div>

    <div id="service-empty" class="empty-state" style="display:none;">
        <div class="empty-icon"><i class="ri-tools-line"></i></div>
        <h3>Tidak ada layanan ditemukan</h3>
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
        window.__SERVICES_PAGE__ = { data: @json($services ?? []) };
    </script>
    <script src="{{ asset('js/dashboard/admin/services.js') }}"></script>
@endsection
