@extends('layouts.dashboard')
@section('title', 'Order Management | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/orders.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h1>Order Management</h1>
            <p>Kelola data pesanan antara client dan freelancer.</p>
        </div>
        <div class="page-header-right">
            <button class="btn-primary" id="btn-add-order">
                <i class="ri-add-line"></i> Tambah Order
            </button>
        </div>
    </div>

    <div class="stats-row" id="stats-row"></div>

    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">Semua</button>
        <button class="filter-tab" data-filter="pending">Pending</button>
        <button class="filter-tab" data-filter="in_progress">In Progress</button>
        <button class="filter-tab" data-filter="completed">Completed</button>
        <button class="filter-tab" data-filter="revision">Revision</button>
    </div>

    <div class="table-wrap">
        <div class="overflow-x-auto">
            <table class="data-table" id="order-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Client & Freelancer</th>
                        <th>Service ID</th>
                        <th>Harga Disepakati</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="order-tbody"></tbody>
            </table>
        </div>
        <div id="order-empty" class="empty-state" style="display:none;">
            <div class="empty-icon"><i class="ri-file-list-3-line"></i></div>
            <h3>Tidak ada order ditemukan</h3>
            <p>Coba ubah filter atau kata kunci pencarian.</p>
        </div>
    </div>
@endsection

@section('modals')
    <div class="modal-overlay" id="detail-modal-overlay">
        <div class="modal-box" id="detail-modal-box"></div>
    </div>
@endsection

@section('scripts')
    <script>
        window.__ORDERS_PAGE__ = { 
            data: @json($orders ?? []),
            clients: @json($clients ?? []), 
            freelancers: @json($freelancers ?? []),
            services: @json($services ?? [])
        };
    </script>
    <script src="{{ asset('js/dashboard/admin/orders.js') }}"></script>
@endsection