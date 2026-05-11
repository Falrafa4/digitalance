@extends('layouts.dashboard')
@section('title', 'Order Management | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/orders.css') }}">
    <style>
        .status-badge { px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider; }
        .status-Pending { bg: #fef3c7; color: #92400e; }
        .status-Negotiated { bg: #e0f2fe; color: #075985; }
        .status-Paid { bg: #dcfce7; color: #166534; }
        .status-InProgress { bg: #eef2ff; color: #3730a3; }
        .status-Revision { bg: #ffedd5; color: #9a3412; }
        .status-Completed { bg: #dcfce7; color: #15803d; }
        .status-Cancelled { bg: #fee2e2; color: #991b1b; }
    </style>
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Order Management</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Kelola dan pantau seluruh transaksi pesanan di platform.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="bg-white px-5 py-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-sm">
                    <i class="ri-file-list-3-line"></i>
                </div>
                <div>
                    <div class="text-[1.2rem] font-black text-slate-900 leading-none">{{ $orders->count() }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Orders</div>
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
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari order ID atau client..." 
                   class="pl-10 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] transition-all" />
        </form>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-200 overflow-hidden animate-fadeUp-3">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Order Info</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Client & Freelancer</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Amount</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-[13px] font-black text-slate-900">#ORD-{{ $order->id }}</span>
                                    <span class="text-[11px] text-slate-400 font-bold uppercase mt-0.5 truncate max-w-[150px]">{{ $order->service->title ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold">C</div>
                                        <span class="text-[12px] font-bold text-slate-700">{{ $order->client->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-bold">F</div>
                                        <span class="text-[12px] font-medium text-slate-500">{{ $order->service->freelancer->skomda_student->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[13px] font-black text-slate-900">Rp{{ number_format($order->agreed_price ?? 0, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="status-badge status-{{ str_replace(' ', '', $order->status) }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[12px] text-slate-500 font-medium">{{ $order->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="window.openOrderModal({{ $order->id }})" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-[#0f766e] hover:text-white transition-all">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus order ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-2xl">
                                    <i class="ri-file-list-3-line"></i>
                                </div>
                                <h3 class="text-slate-900 font-bold">No Orders Found</h3>
                                <p class="text-slate-400 text-sm">Belum ada pesanan yang sesuai dengan filter ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 pagination-container">
        {{ $orders instanceof \Illuminate\Pagination\LengthAwarePaginator ? $orders->links() : '' }}
    </div>
@endsection

@section('modals')
    <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-order-overlay">
        <div class="bg-white rounded-[28px] w-full max-w-[500px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-order-box">
             <!-- Content via JS -->
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.__ORDERS_DATA__ = @json($orders instanceof \Illuminate\Pagination\LengthAwarePaginator ? $orders->items() : $orders);
        
        window.openOrderModal = function(id) {
            const o = window.__ORDERS_DATA__.find(x => x.id === id);
            if (!o) return;

            const box = document.getElementById('modal-order-box');
            const overlay = document.getElementById('modal-order-overlay');

            box.innerHTML = `
                <form action="/admin/orders/${o.id}/status" method="POST" class="p-8">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                             <i class="ri-file-list-3-line"></i>
                        </div>
                        <button type="button" onclick="window.closeOrderModal()" class="w-9 h-9 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>

                    <h2 class="text-[1.4rem] font-black text-slate-900 mb-1">Update Order Status</h2>
                    <p class="text-slate-400 text-sm mb-8 font-mono">#ORD-${o.id}</p>

                    <div class="space-y-6 mb-8">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pilih Status Baru</label>
                            <select name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-700 font-bold focus:border-[#0f766e] focus:bg-white outline-none transition-all">
                                ${['Pending', 'Negotiated', 'Paid', 'In Progress', 'Revision', 'Completed', 'Cancelled'].map(s => `
                                    <option value="${s}" ${o.status === s ? 'selected' : ''}>${s}</option>
                                `).join('')}
                            </select>
                        </div>

                        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
                             <p class="text-[11px] text-amber-700 font-bold leading-relaxed">
                                <i class="ri-information-line mr-1"></i> Perubahan status akan memberitahukan client dan freelancer terkait melalui sistem notifikasi.
                             </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="window.closeOrderModal()" class="flex-1 py-3.5 bg-slate-100 text-slate-500 font-bold rounded-xl text-sm hover:bg-slate-200 transition-all">Batal</button>
                        <button type="submit" class="flex-1 py-3.5 bg-[#0f766e] text-white font-bold rounded-xl text-sm hover:bg-[#0a5e58] transition-all shadow-lg shadow-teal-sm">Update Status</button>
                    </div>
                </form>
            `;

            overlay.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
        };

        window.closeOrderModal = function() {
            const overlay = document.getElementById('modal-order-overlay');
            const box = document.getElementById('modal-order-box');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            box.classList.add('scale-95');
        };
    </script>
@endsection