@extends('layouts.dashboard')
@section('title', 'Manajemen Transaksi | Digitalance')
@section('styles')
    <style>
        /* Pastikan CSS ini ada jika belum di global css */
        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-paid {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-refund {
            background-color: #f1f5f9;
            color: #475569;
        }

        .type-pill {
            display: inline-flex;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
            background: #e2e8f0;
            color: #475569;
        }
    </style>
@endsection
@section('content')
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Manajemen Transaksi</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Kelola dan pantau seluruh transaksi pembayaran, DP, serta
                pengembalian dana.</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div id="stats-row" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 animate-fadeUp-2"></div>

    <!-- Filter Bar -->
    <div class="flex items-center justify-between gap-4 mb-4 flex-wrap animate-fadeUp-3">
        <div class="flex gap-2 flex-wrap" id="filter-tabs">
            @php
                $currentQuery = request('q');
                $currentStatus = strtolower((string) request('status', 'all'));
                $filterLink = function (string $status) use ($currentQuery) {
                    return route('admin.transactions.index', array_filter([
                        'status' => $status !== 'all' ? $status : null,
                        'q' => $currentQuery !== null && $currentQuery !== '' ? $currentQuery : null,
                    ], fn($value) => $value !== null && $value !== ''));
                };
            @endphp
            <a href="{{ $filterLink('all') }}"
                class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] {{ $currentStatus === 'all' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }} font-bold text-[12.5px] cursor-pointer transition-all duration-150">Semua</a>
            <a href="{{ $filterLink('paid') }}"
                class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] {{ $currentStatus === 'paid' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }} font-bold text-[12.5px] cursor-pointer transition-all duration-150">Sudah
                Dibayar</a>
            <a href="{{ $filterLink('pending') }}"
                class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] {{ $currentStatus === 'pending' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }} font-bold text-[12.5px] cursor-pointer transition-all duration-150">Menunggu</a>
            <a href="{{ $filterLink('failed') }}"
                class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] {{ $currentStatus === 'failed' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }} font-bold text-[12.5px] cursor-pointer transition-all duration-150">Gagal</a>
        </div>

        <form action="{{ route('admin.transactions.index') }}" method="GET" class="relative">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <i
                class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari ID, Order ID…"
                class="pl-9 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
        </form>
    </div>

    <!-- Table -->
    <div class="table-wrap animate-fadeUp-3 bg-white rounded-[20px] border border-slate-200 shadow-sm overflow-hidden">
        <table class="data-table w-full text-left" id="trx-table">
            <thead>
                <tr
                    class="bg-slate-50 text-[12px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                    <th class="px-6 py-4">ID Transaksi</th>
                    <th class="px-6 py-4">ID Pesanan</th>
                    <th class="px-6 py-4">Nominal</th>
                    <th class="px-6 py-4">Tipe</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="trx-tbody" class="divide-y divide-slate-100 text-[13px] text-slate-700">
                @forelse($transactions as $trx)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium">#{{ $trx->id }}</td>
                        <td class="px-6 py-4">#{{ $trx->order_id }}</td>
                        <td class="px-6 py-4 font-bold text-emerald-700">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4"><span class="type-pill">{{ $trx->type ?? '-' }}</span></td>
                        <td class="px-6 py-4"><span
                                class="status-pill @if(($trx->status ?? '') == 'Paid') status-paid @elseif(($trx->status ?? '') == 'Pending') status-pending @elseif(($trx->status ?? '') == 'Failed') status-failed @else status-refund @endif">{{ $trx->status ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $trx->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <button type="button" data-trx-id="{{ $trx->id }}"
                                class="px-3 py-1.5 rounded-lg bg-[#0f766e]/10 text-[#0f766e] text-xs font-bold hover:bg-[#0f766e]/20 transition-colors">Detail</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div id="trx-empty" class="hidden py-16 px-5 text-center">
            <div class="text-[3rem] text-slate-300 mb-3"><i class="ri-bank-card-line"></i></div>
            <h3 class="font-display text-[1.1rem] font-bold text-slate-700 mb-1.5">Tidak ada transaksi ditemukan</h3>
            <p class="text-[13px] text-slate-400">Coba ubah filter atau kata kunci pencarian.</p>
        </div>

        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection

@section('modals')
    <!-- MODAL: Detail Transaction -->
    <div class="overlay fixed inset-0 z-[9999] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-detail-trx">
        <div
            class="modal-box bg-white rounded-[18px] w-full max-w-[520px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
            <div id="detail-trx-box" class="flex flex-col flex-1 overflow-hidden">
                <!-- Content injected by JS -->
            </div>
        </div>
    </div>

    <!-- MODAL: Report Fake Transaction -->
    <div class="overlay fixed inset-0 z-[9999] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
        id="modal-report-trx">
        <div class="modal-box bg-white rounded-[18px] w-full max-w-[450px] shadow-2xl overflow-hidden">
            <div class="px-[26px] pt-[30px] pb-[24px] text-center">
                <div
                    class="w-[72px] h-[72px] mx-auto mb-5 bg-red-50 rounded-full flex items-center justify-center text-[2rem] text-red-500">
                    <i class="ri-alarm-warning-line"></i>
                </div>
                <h3 class="font-display text-[1.2rem] font-extrabold text-slate-900 mb-2">Laporkan Transaksi Fake?</h3>
                <p class="text-[13.5px] text-slate-500 leading-relaxed mb-6">Tindakan ini akan mengubah status transaksi
                    menjadi <strong>Failed/Rejected</strong> dan mengirim notifikasi.</p>
                <textarea id="report-reason" rows="3" placeholder="Alasan pelaporan (opsional)..."
                    class="w-full py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none focus:border-red-400 focus:bg-white focus:shadow-[0_0_0_3px_rgba(239,68,68,0.08)] mb-4"></textarea>
            </div>
            <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50">
                <button onclick="window.closeModal('modal-report-trx')"
                    class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all">Batal</button>
                <button id="btn-confirm-report"
                    class="flex-1 py-[11px] rounded-[11px] bg-red-500 text-white font-bold text-[13px] cursor-pointer border-none shadow-[0_3px_10px_rgba(239,68,68,.25)] hover:bg-red-600 transition-all">Ya,
                    Laporkan</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="application/json" id="transactions-page-data">{!! json_encode([
        'data' => $transactions->items(),
        'stats' => $transactionStats ?? ['total' => 0, 'paid' => 0, 'pending' => 0, 'failed' => 0, 'revenue' => 0, 'refund' => 0],
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    <script src="{{ asset('js/dashboard/admin/transactions.js') }}"></script>
@endsection