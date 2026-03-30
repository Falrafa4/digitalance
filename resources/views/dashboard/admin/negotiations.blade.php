@extends('layouts.dashboard')
@section('title', 'Negotiations | Digitalance')

@section('content')
<div class="content-scroll flex-1 px-4 sm:px-8 py-7 overflow-y-auto">

    {{-- Page Header --}}
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Negotiations</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Pantau log pesan negosiasi antara client dan freelancer.</p>
        </div>
        <span class="inline-flex items-center gap-2 px-[18px] py-[10px] bg-blue-50 text-blue-600 font-bold text-[13px] rounded-[12px] border border-blue-200">
            <i class="ri-eye-line"></i> Read Only
        </span>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[14px] mb-6 animate-fadeUp-1">
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Total Negosiasi</span>
            <span class="font-display text-[2rem] font-extrabold text-slate-900" id="stat-total">—</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Berlangsung</span>
            <span class="font-display text-[2rem] font-extrabold text-amber-500" id="stat-ongoing">—</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Selesai</span>
            <span class="font-display text-[2rem] font-extrabold text-emerald-600" id="stat-done">—</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Dibatalkan</span>
            <span class="font-display text-[2rem] font-extrabold text-red-500" id="stat-cancelled">—</span>
        </div>
    </div>

    {{-- Filter + Search --}}
    <div class="flex items-center justify-between gap-4 mb-6 flex-wrap animate-fadeUp-2">
        <div class="flex gap-2 flex-wrap" id="filter-tabs">
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150" data-filter="all">Semua</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="ongoing">Berlangsung</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="completed">Selesai</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="cancelled">Dibatalkan</button>
        </div>
        <div class="relative">
            <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
            <input type="text" id="nego-search" placeholder="Cari order ID, client, freelancer…"
                class="pl-9 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden animate-fadeUp-3">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">ID</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Order</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Pengirim</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Pesan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Tanggal</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Aksi</th>
                    </tr>
                </thead>
                <tbody id="nego-tbody"></tbody>
            </table>
        </div>
        <div id="nego-empty" class="hidden text-center py-16 px-5">
            <i class="ri-discuss-line text-[44px] text-slate-300 mb-3 block"></i>
            <h3 class="font-display text-[1.15rem] text-slate-900 mb-1.5 font-bold">Tidak ada negosiasi ditemukan</h3>
            <p class="text-slate-400 text-[13.5px]">Coba ubah filter atau kata kunci pencarian.</p>
        </div>
    </div>
</div>
@endsection

@section('modals')
{{-- Modal Detail Thread --}}
<div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200" id="modal-thread">
    <div class="modal-box bg-white rounded-3xl w-full max-w-[580px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
            <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Thread Negosiasi</span>
            <button onclick="closeModal('modal-thread')" class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all duration-150"><i class="ri-close-line"></i></button>
        </div>
        <div class="px-[26px] py-[22px] overflow-y-auto flex-1 flex flex-col gap-3" id="thread-content"></div>
        <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50 flex-shrink-0">
            <button onclick="closeModal('modal-thread')" class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all duration-150">Tutup</button>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-2.5 pointer-events-none">
    <style>
        .toast { pointer-events: all; animation: slideInRight .25s ease both; }
        @keyframes slideInRight { from { opacity:0; transform: translateX(40px); } to { opacity:1; transform: translateX(0); } }
    </style>
</div>
@endsection

@section('scripts')
<script>
const BASE = '{{ url('') }}';
const META = document.querySelector('meta[name="csrf-token"]');
const CSRF = META ? META.getAttribute('content') : '';

let allNegos = [];
let activeFilter = 'all';

async function loadNegos() {
    try {
        const res  = await fetch(`${BASE}/api/admin/negotiations`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const json = await res.json();
        allNegos = json.data ?? json ?? [];
    } catch { allNegos = []; }
    renderStats();
    renderTable(allNegos);
}

function renderStats() {
    const total     = allNegos.length;
    const ongoing   = allNegos.filter(n => n.status === 'ongoing').length;
    const done      = allNegos.filter(n => n.status === 'completed').length;
    const cancelled = allNegos.filter(n => n.status === 'cancelled').length;
    document.getElementById('stat-total').textContent     = total;
    document.getElementById('stat-ongoing').textContent   = ongoing;
    document.getElementById('stat-done').textContent      = done;
    document.getElementById('stat-cancelled').textContent = cancelled;
}

document.querySelectorAll('#filter-tabs .filter-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('#filter-tabs .filter-tab').forEach(b => {
            b.className = 'filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]';
        });
        btn.className = 'filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150';
        activeFilter = btn.dataset.filter;
        applyFilter();
    });
});

document.getElementById('nego-search').addEventListener('input', applyFilter);

function applyFilter() {
    const q = document.getElementById('nego-search').value.toLowerCase();
    let filtered = allNegos;
    if (activeFilter !== 'all') filtered = filtered.filter(n => n.status === activeFilter);
    if (q) filtered = filtered.filter(n =>
        String(n.order_id ?? '').includes(q) ||
        (n.sender?.name ?? '').toLowerCase().includes(q)
    );
    renderTable(filtered);
}

const STATUS_MAP = {
    ongoing:   { label: 'Berlangsung', cls: 'bg-amber-100 text-amber-700' },
    completed: { label: 'Selesai',     cls: 'bg-emerald-100 text-emerald-700' },
    cancelled: { label: 'Dibatalkan',  cls: 'bg-red-100 text-red-700' },
};

function renderTable(list) {
    const tbody = document.getElementById('nego-tbody');
    const empty = document.getElementById('nego-empty');
    if (!list.length) { tbody.innerHTML = ''; empty.classList.remove('hidden'); return; }
    empty.classList.add('hidden');
    tbody.innerHTML = list.map(n => {
        const st   = STATUS_MAP[n.status] ?? { label: n.status, cls: 'bg-slate-100 text-slate-500' };
        const date = n.created_at ? new Date(n.created_at).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' }) : '-';
        const msg  = (n.message ?? '-').slice(0, 60) + ((n.message ?? '').length > 60 ? '…' : '');
        return `
        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors duration-150">
            <td class="px-6 py-4 text-[12px] font-mono text-slate-400">#${n.id}</td>
            <td class="px-6 py-4 text-[13px] font-semibold text-[#0f766e]">#${n.order_id ?? '-'}</td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(n.sender?.name ?? 'U')}&background=0f766e&color=fff&size=64"
                        class="w-8 h-8 rounded-xl object-cover flex-shrink-0" />
                    <div>
                        <p class="font-semibold text-[13px] text-slate-900">${n.sender?.name ?? '-'}</p>
                        <p class="text-[11px] text-slate-400">${n.sender?.role ?? '-'}</p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-[13px] text-slate-600 max-w-[200px]">${msg}</td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold ${st.cls}">
                    <i class="ri-circle-fill text-[6px]"></i>${st.label}
                </span>
            </td>
            <td class="px-6 py-4 text-[12px] text-slate-400">${date}</td>
            <td class="px-6 py-4">
                <button onclick="openThread(${n.order_id})" class="w-8 h-8 rounded-[9px] bg-slate-100 text-slate-500 flex items-center justify-center text-[14px] hover:bg-[#f0fdf9] hover:text-[#0f766e] transition-all duration-150 border-none cursor-pointer" title="Lihat Thread">
                    <i class="ri-chat-3-line"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

async function openThread(orderId) {
    const messages = allNegos.filter(n => n.order_id === orderId).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    document.getElementById('thread-content').innerHTML = messages.length
        ? messages.map(m => {
            const date = m.created_at ? new Date(m.created_at).toLocaleString('id-ID') : '-';
            const isClient = (m.sender?.role ?? '').toLowerCase() === 'client';
            return `
            <div class="flex ${isClient ? 'justify-start' : 'justify-end'} gap-2">
                <div class="max-w-[80%]">
                    <p class="text-[11px] font-bold mb-1 ${isClient ? 'text-slate-400' : 'text-[#0f766e]'}">${m.sender?.name ?? '-'} · ${m.sender?.role ?? '-'}</p>
                    <div class="px-4 py-3 rounded-2xl text-[13px] leading-relaxed ${isClient ? 'bg-slate-100 text-slate-700' : 'bg-[#f0fdf9] text-[#0f766e] border border-[#10B981]/20'}">
                        ${m.message ?? '-'}
                    </div>
                    <p class="text-[11px] text-slate-300 mt-1 ${isClient ? 'text-left' : 'text-right'}">${date}</p>
                </div>
            </div>`;
        }).join('')
        : '<p class="text-slate-400 text-center text-[13px] py-6">Tidak ada pesan dalam thread ini.</p>';
    openModal('modal-thread');
}

function openModal(id)  { const m = document.getElementById(id); m.classList.remove('opacity-0','pointer-events-none'); m.classList.add('opacity-100'); }
function closeModal(id) { const m = document.getElementById(id); m.classList.add('opacity-0','pointer-events-none'); m.classList.remove('opacity-100'); }

function showToast(msg, type = 'success') {
    const colors = { success: 'border-[#10b981] text-emerald-800', danger: 'border-red-400 text-red-800', warn: 'border-orange-400 text-orange-800' };
    const icons  = { success: 'ri-check-double-line', danger: 'ri-close-circle-line', warn: 'ri-alert-line' };
    const el = document.createElement('div');
    el.className = `toast flex items-center gap-2.5 px-[18px] py-[13px] border-[1.5px] bg-white rounded-[13px] text-[13px] font-semibold max-w-[300px] shadow-lg ${colors[type]||colors.success}`;
    el.innerHTML = `<i class="text-[17px] flex-shrink-0 ${icons[type]||icons.success}"></i><span>${msg}</span><button onclick="this.closest('.toast').remove()" class="ml-auto bg-transparent border-none cursor-pointer opacity-50 hover:opacity-100 text-[15px] p-0"><i class="ri-close-line"></i></button>`;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

document.addEventListener('DOMContentLoaded', loadNegos);
</script>
@endsection