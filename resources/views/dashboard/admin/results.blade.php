@extends('layouts.dashboard')
@section('title', 'Results | Digitalance')

@section('content')
<div class="content-scroll flex-1 px-4 sm:px-8 py-7 overflow-y-auto">

    {{-- Page Header --}}
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Results</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Pantau hasil pekerjaan yang dikirimkan oleh freelancer.</p>
        </div>
        <span class="inline-flex items-center gap-2 px-[18px] py-[10px] bg-blue-50 text-blue-600 font-bold text-[13px] rounded-[12px] border border-blue-200">
            <i class="ri-eye-line"></i> Read Only
        </span>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[14px] mb-6 animate-fadeUp-1">
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Total Hasil</span>
            <span class="font-display text-[2rem] font-extrabold text-slate-900" id="stat-total">—</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Diterima</span>
            <span class="font-display text-[2rem] font-extrabold text-emerald-600" id="stat-accepted">—</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Revisi</span>
            <span class="font-display text-[2rem] font-extrabold text-amber-500" id="stat-revision">—</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Pending</span>
            <span class="font-display text-[2rem] font-extrabold text-slate-400" id="stat-pending">—</span>
        </div>
    </div>

    {{-- Filter + Search --}}
    <div class="flex items-center justify-between gap-4 mb-6 flex-wrap animate-fadeUp-2">
        <div class="flex gap-2 flex-wrap" id="filter-tabs">
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150" data-filter="all">Semua</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="pending">Pending</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="accepted">Diterima</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="revision">Revisi</button>
        </div>
        <div class="relative">
            <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
            <input type="text" id="result-search" placeholder="Cari order ID atau freelancer…"
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
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Freelancer</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">File Hasil</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Catatan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Dikirim</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Aksi</th>
                    </tr>
                </thead>
                <tbody id="result-tbody"></tbody>
            </table>
        </div>
        <div id="result-empty" class="hidden text-center py-16 px-5">
            <i class="ri-folder-check-line text-[44px] text-slate-300 mb-3 block"></i>
            <h3 class="font-display text-[1.15rem] text-slate-900 mb-1.5 font-bold">Tidak ada hasil ditemukan</h3>
            <p class="text-slate-400 text-[13.5px]">Coba ubah filter atau kata kunci pencarian.</p>
        </div>
    </div>
</div>
@endsection

@section('modals')
{{-- Modal Detail --}}
<div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200" id="modal-detail">
    <div class="modal-box bg-white rounded-3xl w-full max-w-[540px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
            <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Detail Hasil</span>
            <button onclick="closeModal('modal-detail')" class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all duration-150"><i class="ri-close-line"></i></button>
        </div>
        <div class="px-[26px] py-[22px] overflow-y-auto flex-1" id="modal-detail-content"></div>
        <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50 flex-shrink-0">
            <button onclick="closeModal('modal-detail')" class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all duration-150">Tutup</button>
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

let allResults = [];
let activeFilter = 'all';

const STATUS_MAP = {
    pending:  { label: 'Pending',  cls: 'bg-slate-100 text-slate-500' },
    accepted: { label: 'Diterima', cls: 'bg-emerald-100 text-emerald-700' },
    revision: { label: 'Revisi',   cls: 'bg-amber-100 text-amber-700' },
};

async function loadResults() {
    try {
        const res  = await fetch(`${BASE}/api/admin/results`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const json = await res.json();
        allResults = json.data ?? json ?? [];
    } catch { allResults = []; }
    renderStats();
    renderTable(allResults);
}

function renderStats() {
    document.getElementById('stat-total').textContent    = allResults.length;
    document.getElementById('stat-accepted').textContent = allResults.filter(r => r.status === 'accepted').length;
    document.getElementById('stat-revision').textContent = allResults.filter(r => r.status === 'revision').length;
    document.getElementById('stat-pending').textContent  = allResults.filter(r => r.status === 'pending').length;
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

document.getElementById('result-search').addEventListener('input', applyFilter);

function applyFilter() {
    const q = document.getElementById('result-search').value.toLowerCase();
    let filtered = allResults;
    if (activeFilter !== 'all') filtered = filtered.filter(r => r.status === activeFilter);
    if (q) filtered = filtered.filter(r =>
        String(r.order_id ?? '').includes(q) ||
        (r.freelancer?.name ?? '').toLowerCase().includes(q)
    );
    renderTable(filtered);
}

function renderTable(list) {
    const tbody = document.getElementById('result-tbody');
    const empty = document.getElementById('result-empty');
    if (!list.length) { tbody.innerHTML = ''; empty.classList.remove('hidden'); return; }
    empty.classList.add('hidden');
    tbody.innerHTML = list.map(r => {
        const st   = STATUS_MAP[r.status] ?? { label: r.status, cls: 'bg-slate-100 text-slate-500' };
        const date = r.created_at ? new Date(r.created_at).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' }) : '-';
        const note = (r.notes ?? '-').slice(0, 50) + ((r.notes ?? '').length > 50 ? '…' : '');
        const ext  = (r.file_url ?? '').split('.').pop().toLowerCase();
        const fileIcon = ['jpg','jpeg','png','gif','webp'].includes(ext) ? 'ri-image-line' :
                         ['pdf'].includes(ext) ? 'ri-file-pdf-line' :
                         ['zip','rar'].includes(ext) ? 'ri-folder-zip-line' : 'ri-file-line';
        return `
        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors duration-150">
            <td class="px-6 py-4 text-[12px] font-mono text-slate-400">#${r.id}</td>
            <td class="px-6 py-4 text-[13px] font-semibold text-[#0f766e]">#${r.order_id ?? '-'}</td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(r.freelancer?.name ?? 'F')}&background=0f766e&color=fff&size=64"
                        class="w-8 h-8 rounded-xl object-cover flex-shrink-0" />
                    <p class="font-semibold text-[13px] text-slate-900">${r.freelancer?.name ?? '-'}</p>
                </div>
            </td>
            <td class="px-6 py-4">
                ${r.file_url ? `<a href="${BASE}/storage/${r.file_url}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#f0fdf9] text-[#0f766e] rounded-[9px] text-[12px] font-bold hover:bg-[#d1fae5] transition-all duration-150">
                    <i class="${fileIcon}"></i>Lihat File
                </a>` : '<span class="text-slate-300 text-[12px]">—</span>'}
            </td>
            <td class="px-6 py-4 text-[13px] text-slate-600 max-w-[180px]">${note}</td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold ${st.cls}">
                    <i class="ri-circle-fill text-[6px]"></i>${st.label}
                </span>
            </td>
            <td class="px-6 py-4 text-[12px] text-slate-400">${date}</td>
            <td class="px-6 py-4">
                <button onclick="openDetail(${r.id})" class="w-8 h-8 rounded-[9px] bg-slate-100 text-slate-500 flex items-center justify-center text-[14px] hover:bg-[#f0fdf9] hover:text-[#0f766e] transition-all duration-150 border-none cursor-pointer" title="Detail">
                    <i class="ri-eye-line"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

function openDetail(id) {
    const r = allResults.find(x => x.id === id);
    if (!r) return;
    const st   = STATUS_MAP[r.status] ?? { label: r.status, cls: 'bg-slate-100 text-slate-500' };
    const date = r.created_at ? new Date(r.created_at).toLocaleString('id-ID') : '-';
    document.getElementById('modal-detail-content').innerHTML = `
        <div class="grid grid-cols-2 gap-3 mb-4 text-[13px]">
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">ID Hasil</p><p class="font-mono font-semibold text-slate-700">#${r.id}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Order</p><p class="font-semibold text-[#0f766e]">#${r.order_id ?? '-'}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Freelancer</p><p class="font-semibold text-slate-700">${r.freelancer?.name ?? '-'}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Status</p>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold ${st.cls}"><i class="ri-circle-fill text-[6px]"></i>${st.label}</span>
            </div>
            <div class="bg-slate-50 rounded-2xl p-3 col-span-2"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Dikirim</p><p class="font-semibold text-slate-700">${date}</p></div>
        </div>
        ${r.notes ? `<div class="bg-slate-50 rounded-2xl p-3 mb-4"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Catatan Freelancer</p><p class="text-[13px] text-slate-700 leading-relaxed">${r.notes}</p></div>` : ''}
        ${r.file_url ? `<a href="${BASE}/storage/${r.file_url}" target="_blank"
            class="flex items-center gap-3 px-4 py-3 bg-[#f0fdf9] border border-[#10B981]/30 rounded-2xl hover:bg-[#d1fae5] transition-all duration-150">
            <i class="ri-download-line text-[#0f766e] text-[18px]"></i>
            <span class="text-[13px] font-bold text-[#0f766e]">Unduh File Hasil</span>
        </a>` : ''}
    `;
    openModal('modal-detail');
}

function openModal(id)  { const m = document.getElementById(id); m.classList.remove('opacity-0','pointer-events-none'); m.classList.add('opacity-100'); }
function closeModal(id) { const m = document.getElementById(id); m.classList.add('opacity-0','pointer-events-none'); m.classList.remove('opacity-100'); }

document.addEventListener('DOMContentLoaded', loadResults);
</script>
@endsection