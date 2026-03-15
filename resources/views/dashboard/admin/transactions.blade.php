@extends('layouts.dashboard')
@section('title', 'Transaction Management | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/transactions.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h1>Transactions Management</h1>
            <p>Kelola dan pantau seluruh transaksi pembayaran, DP, serta pengembalian dana.</p>
        </div>
        <div class="page-header-right">
            <button class="btn-primary" id="btn-add-trx">
                <i class="ri-add-line"></i> Tambah Transaksi
            </button>
        </div>
    </div>

    <div class="stats-row" id="stats-row"></div>

    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">Semua</button>
        <button class="filter-tab" data-filter="paid">Paid</button>
        <button class="filter-tab" data-filter="pending">Pending</button>
        <button class="filter-tab" data-filter="failed">Failed</button>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="trx-table">
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Order ID</th>
                    <th>Nominal</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="trx-tbody"></tbody>
        </table>
        <div id="trx-empty" class="empty-state" style="display:none;">
            <div class="empty-icon"><i class="ri-bank-card-line"></i></div>
            <h3>Tidak ada transaksi ditemukan</h3>
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
    <script src="{{ asset('js/dashboard/admin/transactions.js') }}"></script>
@endsection
