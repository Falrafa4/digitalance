@extends('layouts.dashboard')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $orders */
@endphp

@section('title', 'Manajemen Pesanan | Digitalance')
@section('styles')
    <link class="hidden" rel="stylesheet" href="{{ asset('css/dashboard/admin/orders.css') }}">
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Manajemen Pesanan</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Kelola dan pantau seluruh transaksi pesanan di platform.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-white px-5 py-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-sm">
                    <i class="ri-file-list-3-line"></i>
                </div>
                <div>
                    <div class="text-[1.2rem] font-black text-slate-900 leading-none">
                        {{ $orders instanceof \Illuminate\Pagination\LengthAwarePaginator ? $orders->total() : $orders->count() }}
                    </div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Pesanan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between gap-4 mb-8 flex-wrap animate-fadeUp-2">
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.orders.index') }}"
                class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ !request('status') ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                Semua
            </a>
            @foreach(['Pending', 'Paid', 'In Progress', 'Completed', 'Cancelled'] as $status)
                <a href="{{ route('admin.orders.index', ['status' => $status]) }}"
                    class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ request('status') == $status ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                    {{ $status }}
                </a>
            @endforeach
            <a href="{{ route('admin.orders.index', array_filter(['payout' => 'paid', 'status' => request('status')])) }}"
                class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ request('payout') === 'paid' ? 'border-emerald-600 bg-emerald-600 text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                Sudah Ditransfer
            </a>
            <a href="{{ route('admin.orders.index', array_filter(['payout' => 'pending', 'status' => request('status')])) }}"
                class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ request('payout') === 'pending' ? 'border-amber-500 bg-amber-500 text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                Belum Ditransfer
            </a>
        </div>

        <form action="{{ route('admin.orders.index') }}" method="GET" class="relative">
            <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[15px]"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari ID order atau klien..."
                class="pl-10 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] transition-all" />
        </form>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-200 overflow-hidden animate-fadeUp-3">
        <div class="overflow-x-auto w-full block scrollbar-thin">
            <table class="w-full text-left border-collapse min-w-[800px] data-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Info Pesanan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Klien & Freelancer</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Jumlah</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-[13px] font-black text-slate-900">#ORD-{{ $order->id }}</span>
                                    <span class="text-[11px] text-slate-400 font-bold uppercase mt-0.5 truncate max-w-[150px]">{{ $order->service->title ?? 'Tidak tersedia' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold">C</div>
                                        <span class="text-[12px] font-bold text-slate-700">{{ $order->client->name ?? 'Tidak tersedia' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-bold">F</div>
                                        <span class="text-[12px] font-medium text-slate-500">{{ $order->service->freelancer->skomda_student->name ?? 'Tidak tersedia' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    <span class="text-[13px] font-black text-slate-900">Rp{{ number_format($order->agreed_price ?? 0, 0, ',', '.') }}</span>
                                    @php
                                        $payoutDone = ($order->transactions ?? collect())->contains(function ($trx) {
                                            return $trx->type === 'Full' && $trx->status === 'Paid';
                                        });
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $payoutDone ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                        <i class="ri-{{ $payoutDone ? 'checkbox-circle-line' : 'time-line' }}"></i>
                                        {{ $payoutDone ? 'Sudah Ditransfer' : 'Belum Ditransfer' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-ui.status-badge :status="$order->status" />
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[12px] text-slate-500 font-medium">{{ $order->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button data-order-id="{{ $order->id }}"
                                        class="btn-open-order-detail w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-all"
                                        title="Detail Pesanan">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button data-order-id="{{ $order->id }}"
                                        class="btn-open-order-modal w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-[#0f766e] hover:text-white transition-all"
                                        title="Ubah Status">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    @if($order->status === 'Completed')
                                        @php
                                            $payoutDone = ($order->transactions ?? collect())->contains(function ($trx) {
                                                return $trx->type === 'Full' && $trx->status === 'Paid';
                                            });
                                        @endphp
                                        @if(!$payoutDone)
                                            <button data-order-id="{{ $order->id }}"
                                                class="btn-open-transfer-order w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all"
                                                title="Transfer ke Freelancer">
                                                <i class="ri-exchange-dollar-line"></i>
                                            </button>
                                        @else
                                            <button data-order-id="{{ $order->id }}"
                                                class="btn-open-transfer-detail w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all"
                                                title="Detail Transfer (Sudah Ditransfer)">
                                                <i class="ri-check-double-line"></i>
                                            </button>
                                        @endif
                                    @endif
                                    <button data-order-id="{{ $order->id }}"
                                        class="btn-open-delete-order w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all"
                                        title="Hapus Pesanan">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-ui.empty-state icon="ri-file-list-3-line" title="Tidak Ada Pesanan Ditemukan"
                                    description="Belum ada pesanan yang sesuai dengan filter ini." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator && $orders->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('modals')
    <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300"
        id="modal-order-overlay">
        <div class="bg-white rounded-[32px] w-full max-w-[600px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300"
            id="modal-order-box">
        </div>
    </div>
@endsection

@section('scripts')
    <script id="__ORDERS_DATA_JSON__" type="application/json">{!! base64_encode(json_encode(collect($orders instanceof \Illuminate\Pagination\LengthAwarePaginator ? $orders->items() : $orders)->map(function($order) {
        return [
            'id' => $order->id,
            'status' => $order->status,
            'agreed_price' => $order->agreed_price,
            'created_at' => $order->created_at ? $order->created_at->toIso8601String() : null,
            'client' => [
                'name' => $order->client?->name ?? 'Tidak tersedia'
            ],
            'service' => [
                'title' => $order->service?->title ?? 'Tidak tersedia',
                'freelancer' => [
                    'skomda_student' => [
                        'name' => $order->service?->freelancer?->skomda_student?->name ?? 'Tidak tersedia'
                    ]
                ]
            ],
            'transactions' => $order->transactions ?? []
        ];
    })->values()->all())) !!}</script>

    <script>
        (function () {
            try {
                var rawBase = document.getElementById('__ORDERS_DATA_JSON__')?.textContent || '';
                var raw = rawBase ? atob(rawBase) : '[]';
                window.__ORDERS_DATA__ = JSON.parse(raw);
            } catch (e) {
                console.error("Gagal melakukan parse orders data:", e);
                window.__ORDERS_DATA__ = [];
            }

            // Fallback formatRupiah jika tidak tersedia secara global
            window.DigitalanceUtils = window.DigitalanceUtils || {};
            if (!window.DigitalanceUtils.formatRupiah) {
                window.DigitalanceUtils.formatRupiah = function (num) {
                    return 'Rp' + Number(num).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                };
            }
        })();
    </script>

    <script>
        // Helper XSS: Melindungi modal dari data input yang jahat (XSS Injection)
        function escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        // Helper Rute Dinamis untuk mencegah hardcode URL admin
        function getOrderRoute(type, id) {
            const routes = {
                status: '/admin/orders/:id/status',
                transfer: '/admin/orders/:id/transfer',
                delete: '/admin/orders/:id'
            };
            return (routes[type] || '').replace(':id', id);
        }

        // Tutup modal secara aman dengan pemeriksaan elemen null
        window.closeOrderModal = function () {
            const overlay = document.getElementById('modal-order-overlay');
            const box = document.getElementById('modal-order-box');
            if (overlay) overlay.classList.add('opacity-0', 'pointer-events-none');
            if (box) box.classList.add('scale-95');
        };

        // Event listener klik overlay luar untuk menutup modal
        document.addEventListener('DOMContentLoaded', function () {
            const overlay = document.getElementById('modal-order-overlay');
            if (overlay) {
                overlay.addEventListener('click', function (e) {
                    if (e.target === overlay) {
                        window.closeOrderModal();
                    }
                });
            }
        });

        // 1. MODAL: UBAH STATUS
        window.openOrderModal = function (id) {
            const o = window.__ORDERS_DATA__.find(x => Number(x.id) === Number(id));
            if (!o) return;

            const box = document.getElementById('modal-order-box');
            const overlay = document.getElementById('modal-order-overlay');
            if (!box || !overlay) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const actionUrl = getOrderRoute('status', o.id);

            box.innerHTML = `
                <div class="p-8">
                    <div class="flex justify-between items-center mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-teal-50 text-[#0f766e] flex items-center justify-center text-2xl">
                            <i class="ri-edit-line"></i>
                        </div>
                        <button type="button" onclick="window.closeOrderModal()" class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                    <h2 class="text-[1.5rem] font-black text-slate-900 mb-1">Ubah Status Pesanan</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mb-8">Order ID: #ORD-${escapeHtml(o.id)}</p>
                    <form action="${actionUrl}" method="POST">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <div class="space-y-6 mb-10">
                            <div class="field-group">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Status Baru</label>
                                <div class="relative">
                                    <select name="status" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:border-[#0f766e] focus:bg-white outline-none transition-all appearance-none">
                                        ${['Pending', 'Negotiated', 'Paid', 'In Progress', 'Revision', 'Completed', 'Cancelled'].map(s => `
                                        <option value="${s}" ${o.status === s ? 'selected' : ''}>${s}</option>
                                        `).join('')}
                                    </select>
                                    <i class="ri-arrow-down-s-line absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>
                            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
                                <p class="text-[11px] text-amber-700 font-bold leading-relaxed flex items-start gap-2">
                                    <i class="ri-information-line mt-0.5"></i>
                                    <span>Perubahan status akan langsung terlihat oleh Klien dan Freelancer.</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="window.closeOrderModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-2xl text-[13px] hover:bg-slate-200 transition-all">Batal</button>
                            <button type="submit" class="flex-1 py-4 bg-[#0f766e] text-white font-bold rounded-2xl text-[13px] hover:bg-[#0a5e58] transition-all shadow-lg shadow-teal-sm">Perbarui Status</button>
                        </div>
                    </form>
                </div>`;
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
        };

        // 2. MODAL: TRANSFER TO FREELANCER
        window.openTransferOrder = function (id) {
            const o = window.__ORDERS_DATA__.find(x => Number(x.id) === Number(id));
            if (!o) return;

            const box = document.getElementById('modal-order-box');
            const overlay = document.getElementById('modal-order-overlay');
            if (!box || !overlay) return;

            const amount = Number(o.agreed_price || 0);
            const payoutTransactions = Array.isArray(o.transactions)
                ? o.transactions.filter((trx) => trx.type === 'Full' && trx.status === 'Paid')
                : [];
            const payoutDone = payoutTransactions.length > 0;
            const payoutDate = payoutDone && payoutTransactions[0]?.created_at
                ? new Date(payoutTransactions[0].created_at).toLocaleString('id-ID')
                : '-';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const actionUrl = getOrderRoute('transfer', o.id);

            box.innerHTML = `
                <div class="p-8">
                    <div class="flex justify-between items-center mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                            <i class="ri-exchange-dollar-line"></i>
                        </div>
                        <button type="button" onclick="window.closeOrderModal()" class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                    <h2 class="text-[1.5rem] font-black text-slate-900 mb-1">Transfer ke Freelancer</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mb-6">Order ID: #ORD-${escapeHtml(o.id)}</p>
                    <div class="mb-6 p-4 ${payoutDone ? 'bg-emerald-50 border-emerald-100' : 'bg-amber-50 border-amber-100'} rounded-2xl border">
                        <p class="text-[12px] ${payoutDone ? 'text-emerald-700' : 'text-amber-700'} font-bold leading-relaxed">Karena semua rekening dikelola admin, tombol ini hanya mencatat transfer internal ke freelancer setelah order selesai. ${payoutDone ? 'Transfer untuk order ini sudah tercatat.' : 'Silakan catat transfer terlebih dahulu.'}</p>
                        ${payoutDone ? `<div class="mt-3 text-[11px] text-emerald-700 font-bold">Tanggal transfer: ${escapeHtml(payoutDate)}</div>` : ''}
                    </div>
                    <form action="${actionUrl}" method="POST">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <div class="space-y-5 mb-8">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Nominal Transfer</label>
                                <input type="text" name="amount" value="${amount}" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:border-emerald-500 focus:bg-white outline-none transition-all" />
                                <p class="text-[11px] text-slate-400 mt-2">Default diisi dari agreed price order.</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Catatan</label>
                                <textarea name="note" rows="3" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-medium focus:border-emerald-500 focus:bg-white outline-none transition-all" placeholder="Opsional"></textarea>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="window.closeOrderModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-2xl text-[13px] hover:bg-slate-200 transition-all">Batal</button>
                            <button type="submit" ${payoutDone ? 'disabled' : ''} class="flex-1 py-4 ${payoutDone ? 'bg-slate-300 text-slate-500 cursor-not-allowed' : 'bg-emerald-600 text-white hover:bg-emerald-700'} font-bold rounded-2xl text-[13px] transition-all shadow-lg shadow-emerald-200">${payoutDone ? 'Sudah Ditransfer' : 'Catat Transfer'}</button>
                        </div>
                    </form>
                </div>`;

            overlay.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
        };

        // 3. MODAL: DETAIL PESANAN
        window.openOrderDetail = function (id) {
            const o = window.__ORDERS_DATA__.find(x => Number(x.id) === Number(id));
            if (!o) return;

            const box = document.getElementById('modal-order-box');
            const overlay = document.getElementById('modal-order-overlay');
            if (!box || !overlay) return;

            const date = o.created_at
                ? new Date(o.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                : '-';

            box.innerHTML = `
                <div class="relative">
                    <div class="h-28 bg-gradient-to-r from-teal-600 to-emerald-600 flex items-center px-8">
                        <div class="flex-1">
                            <h2 class="text-white font-black text-xl tracking-tight">Detail Pesanan</h2>
                            <p class="text-white/70 text-[10px] font-bold uppercase tracking-[0.2em]">Transaksi #ORD-${escapeHtml(o.id)}</p>
                        </div>
                        <button onclick="window.closeOrderModal()" class="w-10 h-10 bg-white/20 text-white rounded-full flex items-center justify-center hover:bg-white/30 transition">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                    <div class="px-8 pb-8 -mt-8 relative z-10">
                        <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-50 mb-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-[13px] font-bold text-slate-400 uppercase tracking-widest mb-1">Layanan yang Dibeli</h3>
                                    <p class="text-[1.1rem] font-black text-slate-900 line-clamp-2 leading-tight">${escapeHtml(o.service?.title)}</p>
                                </div>
                                <span class="px-3 py-1 bg-teal-50 text-[#0f766e] text-[10px] font-black rounded-lg uppercase tracking-wider border border-teal-100">${escapeHtml(o.status)}</span>
                            </div>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                <span class="text-[13px] font-bold text-slate-500">Total Pesanan</span>
                                <span class="text-[1.3rem] font-black text-[#0f766e]">${window.DigitalanceUtils.formatRupiah(o.agreed_price || 0)}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pembeli</span>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-[11px]">C</div>
                                    <span class="text-[13px] font-bold text-slate-700 truncate">${escapeHtml(o.client?.name)}</span>
                                </div>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Penyedia</span>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-[11px]">F</div>
                                    <span class="text-[13px] font-bold text-slate-700 truncate">${escapeHtml(o.service?.freelancer?.skomda_student?.name)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-8">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <i class="ri-calendar-line text-slate-400"></i>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal Pesanan</span>
                                </div>
                                <span class="text-[13px] font-bold text-slate-700">${escapeHtml(date)}</span>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button onclick="window.openOrderModal(${o.id})" class="flex-1 py-4 bg-slate-900 text-white font-bold rounded-2xl text-[13px] hover:bg-slate-800 transition-all flex items-center justify-center gap-2 shadow-lg shadow-slate-200">
                                <i class="ri-edit-line"></i> Ubah Status
                            </button>
                        </div>
                    </div>
                </div>`;
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
        };

        // 4. MODAL: DELETE ORDER
        window.openDeleteOrder = function (id) {
            const o = window.__ORDERS_DATA__.find(x => Number(x.id) === Number(id));
            if (!o) return;

            const box = document.getElementById('modal-order-box');
            const overlay = document.getElementById('modal-order-overlay');
            if (!box || !overlay) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const actionUrl = getOrderRoute('delete', o.id);

            box.innerHTML = `
                <div class="p-8">
                    <div class="flex justify-between items-center mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-2xl animate-bounce">
                            <i class="ri-delete-bin-line"></i>
                        </div>
                        <button type="button" onclick="window.closeOrderModal()" class="w-10 h-10 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                    <h2 class="text-[1.5rem] font-black text-slate-900 mb-1">Hapus Pesanan</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mb-6">Order ID: #ORD-${escapeHtml(o.id)}</p>
                    <div class="mb-8 p-4 bg-red-50 border border-red-100 rounded-2xl">
                        <p class="text-[12px] text-red-700 font-bold leading-relaxed flex items-start gap-2">
                            <i class="ri-error-warning-line mt-0.5 text-lg flex-shrink-0"></i>
                            <span>Apakah Anda yakin ingin menghapus pesanan ini secara permanen? Seluruh transaksi dan riwayat progres pesanan ini akan terhapus selamanya.</span>
                        </p>
                    </div>
                    <form action="${actionUrl}" method="POST" id="form-delete-order">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <div class="flex gap-3">
                            <button type="button" onclick="window.closeOrderModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-bold rounded-2xl text-[13px] hover:bg-slate-200 transition-all">Batal</button>
                            <button type="submit" class="flex-1 py-4 bg-red-600 text-white font-bold rounded-2xl text-[13px] hover:bg-red-700 transition-all shadow-lg shadow-red-200">Ya, Hapus Permanen</button>
                        </div>
                    </form>
                </div>`;

            overlay.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
        };

        // Event Delegation (Single Handler Teroptimasi dengan Mapping)
        const handlers = {
            '.btn-open-order-detail': window.openOrderDetail,
            '.btn-open-order-modal': window.openOrderModal,
            '.btn-open-transfer-order': window.openTransferOrder,
            '.btn-open-transfer-detail': window.openTransferOrder,
            '.btn-open-delete-order': window.openDeleteOrder
        };

        document.addEventListener('click', function (e) {
            for (const [selector, callback] of Object.entries(handlers)) {
                const btn = e.target.closest(selector);
                if (btn) {
                    const id = btn.getAttribute('data-order-id');
                    if (id) callback(id);
                    break;
                }
            }
        });
    </script>
@endsection