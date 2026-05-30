@extends('layouts.dashboard')
@php /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $orders */ @endphp
@section('title', 'Manajemen Pesanan | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/orders.css') }}">
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Manajemen Pesanan</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Kelola dan pantau seluruh transaksi pesanan di platform.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-white px-5 py-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-sm">
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
        </div>

        <form action="{{ route('admin.orders.index') }}" method="GET" class="relative">
            <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[15px]"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari ID order atau klien..."
                class="pl-10 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] transition-all" />
        </form>
    </div>

    {{-- PERBAIKAN TASK 2: Memastikan pembungkus memiliki kelas data-table-wrapper overflow-x-auto yang solid --}}
    <div class="bg-white rounded-[24px] border border-slate-200 overflow-hidden animate-fadeUp-3">
        <div class="overflow-x-auto w-full block scrollbar-thin">
            <table class="w-full text-left border-collapse min-w-[800px] data-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Info Pesanan
                        </th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Klien &
                            Freelancer</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Jumlah</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-[13px] font-black text-slate-900">#ORD-{{ $order->id }}</span>
                                    <span
                                        class="text-[11px] text-slate-400 font-bold uppercase mt-0.5 truncate max-w-[150px]">{{ $order->service->title ?? 'Tidak tersedia' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold">
                                            C</div>
                                        <span
                                            class="text-[12px] font-bold text-slate-700">{{ $order->client->name ?? 'Tidak tersedia' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-bold">
                                            F</div>
                                        <span
                                            class="text-[12px] font-medium text-slate-500">{{ $order->service->freelancer->skomda_student->name ?? 'Tidak tersedia' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-[13px] font-black text-slate-900">Rp{{ number_format($order->agreed_price ?? 0, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <x-ui.status-badge :status="$order->status" />
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-[12px] text-slate-500 font-medium">{{ $order->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button data-order-id="{{ $order->id }}"
                                        class="btn-open-order-detail w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-all">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button data-order-id="{{ $order->id }}"
                                        class="btn-open-order-modal w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-[#0f766e] hover:text-white transition-all">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <button data-order-id="{{ $order->id }}"
                                        class="btn-open-delete-order w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
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
    <script id="__ORDERS_DATA_JSON__"
        type="application/json">{{ base64_encode(json_encode($orders instanceof \Illuminate\Pagination\LengthAwarePaginator ? $orders->items() : $orders)) }}</script>
    <script>
        (function () {
            try {
                var rawBase = document.getElementById('__ORDERS_DATA_JSON__') && document.getElementById('__ORDERS_DATA_JSON__').textContent || '';
                var raw = rawBase ? atob(rawBase) : '[]';
                window.__ORDERS_DATA__ = JSON.parse(raw);
            } catch (e) {
                window.__ORDERS_DATA__ = [];
            }
        })();
    </script>
    <script>
        window.openOrderModal = function (id) {
            const o = window.__ORDERS_DATA__.find(x => x.id === id);
            if (!o) return;

            const box = document.getElementById('modal-order-box');
            const overlay = document.getElementById('modal-order-overlay');

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
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mb-8">Order ID: #ORD-${o.id}</p>
                    <form action="/admin/orders/${o.id}/status" method="POST">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
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

        document.addEventListener('click', function (e) {
            var detailBtn = e.target.closest && e.target.closest('.btn-open-order-detail');
            if (detailBtn) {
                var id = detailBtn.getAttribute('data-order-id');
                if (id) window.openOrderDetail(id);
                return;
            }
            var modalBtn = e.target.closest && e.target.closest('.btn-open-order-modal');
            if (modalBtn) {
                var id2 = modalBtn.getAttribute('data-order-id');
                if (id2) window.openOrderModal(id2);
                return;
            }
            var delBtn = e.target.closest && e.target.closest('.btn-open-delete-order');
            if (delBtn) {
                var id3 = delBtn.getAttribute('data-order-id');
                if (id3) window.openDeleteOrder(id3);
                return;
            }
        });

        window.openOrderDetail = function (id) {
            const o = window.__ORDERS_DATA__.find(x => x.id === id);
            if (!o) return;
            const box = document.getElementById('modal-order-box');
            const overlay = document.getElementById('modal-order-overlay');
            const date = new Date(o.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

            box.innerHTML = `
                <div class="relative">
                    <div class="h-28 bg-gradient-to-r from-teal-600 to-emerald-600 flex items-center px-8">
                        <div class="flex-1">
                            <h2 class="text-white font-black text-xl tracking-tight">Detail Pesanan</h2>
                            <p class="text-white/70 text-[10px] font-bold uppercase tracking-[0.2em]">Transaksi #ORD-${o.id}</p>
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
                                <p class="text-[1.1rem] font-black text-slate-900 line-clamp-2 leading-tight">${o.service?.title || 'N/A'}</p>
                            </div>
                            <span class="px-3 py-1 bg-teal-50 text-[#0f766e] text-[10px] font-black rounded-lg uppercase tracking-wider border border-teal-100">${o.status}</span>
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
                                    <span class="text-[13px] font-bold text-slate-700 truncate">${o.client?.name || 'Klien'}</span>
                                </div>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Penyedia</span>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-[11px]">F</div>
                                    <span class="text-[13px] font-bold text-slate-700 truncate">${o.service?.freelancer?.skomda_student?.name || 'Freelancer'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-8">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <i class="ri-calendar-line text-slate-400"></i>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal Pesanan</span>
                                </div>
                                <span class="text-[13px] font-bold text-slate-700">${date}</span>
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

        window.closeOrderModal = function () {
            const overlay = document.getElementById('modal-order-overlay');
            const box = document.getElementById('modal-order-box');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            box.classList.add('scale-95');
        };

        window.openDeleteOrder = async function (id) {
            if (await window.customConfirm(`Yakin ingin menghapus Order #ORD-${id} secara permanen?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/orders/' + id;
                form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                <input type="hidden" name="_method" value="DELETE">`;
                document.body.appendChild(form);
                form.submit();
            }
        };
    </script>
@endsection