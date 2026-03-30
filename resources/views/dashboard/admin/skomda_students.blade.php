@extends('layouts.dashboard')
@section('title', 'Skomda Students | Digitalance')

@section('content')
<div class="content-scroll flex-1 px-4 sm:px-8 py-7 overflow-y-auto">

    {{-- Page Header --}}
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Skomda Students</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Data siswa/i SMK yang terverifikasi sebagai freelancer di Digitalance.</p>
        </div>
        <span class="inline-flex items-center gap-2 px-[18px] py-[10px] bg-[#f0fdf9] text-[#0f766e] font-bold text-[13px] rounded-[12px] border border-[#10B981]">
            <i class="ri-shield-check-line"></i> Data Tervalidasi
        </span>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[14px] mb-6 animate-fadeUp-1">
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Total Siswa</span>
            <span class="font-display text-[2rem] font-extrabold text-slate-900" id="stat-total">—</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Aktif Freelancer</span>
            <span class="font-display text-[2rem] font-extrabold text-emerald-600" id="stat-active">—</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">SMK Terdaftar</span>
            <span class="font-display text-[2rem] font-extrabold text-blue-600" id="stat-schools">—</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Suspended</span>
            <span class="font-display text-[2rem] font-extrabold text-red-500" id="stat-suspended">—</span>
        </div>
    </div>

    {{-- Filter + Search --}}
    <div class="flex items-center justify-between gap-4 mb-6 flex-wrap animate-fadeUp-2">
        <div class="flex gap-2 flex-wrap" id="filter-tabs">
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150" data-filter="all">Semua</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="active">Aktif</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="suspended">Suspended</button>
        </div>
        <div class="relative">
            <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
            <input type="text" id="student-search" placeholder="Cari nama, email, atau sekolah…"
                class="pl-9 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden animate-fadeUp-3">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Siswa</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Sekolah</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Jurusan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Kelas</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Terdaftar</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Aksi</th>
                    </tr>
                </thead>
                <tbody id="student-tbody"></tbody>
            </table>
        </div>
        <div id="student-empty" class="hidden text-center py-16 px-5">
            <i class="ri-graduation-cap-line text-[44px] text-slate-300 mb-3 block"></i>
            <h3 class="font-display text-[1.15rem] text-slate-900 mb-1.5 font-bold">Tidak ada siswa ditemukan</h3>
            <p class="text-slate-400 text-[13.5px]">Coba ubah filter atau kata kunci pencarian.</p>
        </div>
    </div>
</div>
@endsection

@section('modals')
{{-- Modal Detail --}}
<div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200" id="modal-detail">
    <div class="modal-box bg-white rounded-3xl w-full max-w-[520px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
            <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Detail Siswa</span>
            <button onclick="closeModal('modal-detail')" class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all duration-150"><i class="ri-close-line"></i></button>
        </div>
        <div class="px-[26px] py-[22px] overflow-y-auto flex-1" id="modal-detail-content"></div>
        <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50 flex-shrink-0" id="modal-detail-actions"></div>
    </div>
</div>

{{-- Toast --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-2.5 pointer-events-none">
    <style>
        .toast { pointer-events: all; animation: slideInRight .25s ease both; }
        .toast.hide { animation: slideOutRight .25s ease both; }
        @keyframes slideInRight { from { opacity:0; transform: translateX(40px); } to { opacity:1; transform: translateX(0); } }
        @keyframes slideOutRight { from { opacity:1; transform: translateX(0); } to { opacity:0; transform: translateX(40px); } }
    </style>
</div>
@endsection

@section('scripts')
<script>
const BASE = '{{ url('') }}';
const META = document.querySelector('meta[name="csrf-token"]');
const CSRF = META ? META.getAttribute('content') : '';

let allStudents = [];
let activeFilter = 'all';

async function loadStudents() {
    try {
        const res  = await fetch(`${BASE}/api/admin/skomda-students`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const json = await res.json();
        allStudents = json.data ?? json ?? [];
    } catch { allStudents = []; }
    renderStats();
    renderTable(allStudents);
}

function renderStats() {
    const total     = allStudents.length;
    const active    = allStudents.filter(s => s.status !== 'Suspended').length;
    const schools   = [...new Set(allStudents.map(s => s.school_name).filter(Boolean))].length;
    const suspended = allStudents.filter(s => s.status === 'Suspended').length;
    document.getElementById('stat-total').textContent     = total;
    document.getElementById('stat-active').textContent    = active;
    document.getElementById('stat-schools').textContent   = schools;
    document.getElementById('stat-suspended').textContent = suspended;
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

document.getElementById('student-search').addEventListener('input', applyFilter);

function applyFilter() {
    const q = document.getElementById('student-search').value.toLowerCase();
    let filtered = allStudents;
    if (activeFilter === 'active')    filtered = filtered.filter(s => s.status !== 'Suspended');
    if (activeFilter === 'suspended') filtered = filtered.filter(s => s.status === 'Suspended');
    if (q) filtered = filtered.filter(s =>
        (s.name ?? '').toLowerCase().includes(q) ||
        (s.email ?? '').toLowerCase().includes(q) ||
        (s.school_name ?? '').toLowerCase().includes(q)
    );
    renderTable(filtered);
}

function renderTable(list) {
    const tbody = document.getElementById('student-tbody');
    const empty = document.getElementById('student-empty');
    if (!list.length) { tbody.innerHTML = ''; empty.classList.remove('hidden'); return; }
    empty.classList.add('hidden');
    tbody.innerHTML = list.map(s => {
        const isSuspended = s.status === 'Suspended';
        const statusCls   = isSuspended ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700';
        const statusLbl   = isSuspended ? 'Suspended' : 'Aktif';
        const date        = s.created_at ? new Date(s.created_at).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' }) : '-';
        return `
        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors duration-150">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(s.name ?? 'S')}&background=0f766e&color=fff"
                        class="w-9 h-9 rounded-xl object-cover border border-slate-100 flex-shrink-0" />
                    <div>
                        <p class="font-semibold text-[13.5px] text-slate-900">${s.name ?? '-'}</p>
                        <p class="text-[11px] text-slate-400">${s.email ?? '-'}</p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-[13px] text-slate-700">${s.school_name ?? '-'}</td>
            <td class="px-6 py-4 text-[13px] text-slate-700">${s.major ?? '-'}</td>
            <td class="px-6 py-4 text-[13px] text-slate-700">${s.grade ?? '-'}</td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold ${statusCls}">
                    <i class="ri-circle-fill text-[6px]"></i>${statusLbl}
                </span>
            </td>
            <td class="px-6 py-4 text-[12px] text-slate-400">${date}</td>
            <td class="px-6 py-4">
                <div class="flex gap-1.5">
                    <button onclick="openDetail(${s.id})" class="w-8 h-8 rounded-[9px] bg-slate-100 text-slate-500 flex items-center justify-center text-[14px] hover:bg-[#f0fdf9] hover:text-[#0f766e] transition-all duration-150 border-none cursor-pointer" title="Detail">
                        <i class="ri-eye-line"></i>
                    </button>
                    ${!isSuspended ? `
                    <button onclick="handleSuspend(${s.id})" class="w-8 h-8 rounded-[9px] bg-slate-100 text-slate-500 flex items-center justify-center text-[14px] hover:bg-red-50 hover:text-red-500 transition-all duration-150 border-none cursor-pointer" title="Suspend">
                        <i class="ri-forbid-line"></i>
                    </button>` : `
                    <button onclick="handleUnsuspend(${s.id})" class="w-8 h-8 rounded-[9px] bg-slate-100 text-slate-500 flex items-center justify-center text-[14px] hover:bg-emerald-50 hover:text-emerald-600 transition-all duration-150 border-none cursor-pointer" title="Aktifkan">
                        <i class="ri-refresh-line"></i>
                    </button>`}
                </div>
            </td>
        </tr>`;
    }).join('');
}

function openDetail(id) {
    const s = allStudents.find(x => x.id === id);
    if (!s) return;
    const isSuspended = s.status === 'Suspended';
    document.getElementById('modal-detail-content').innerHTML = `
        <div class="flex items-center gap-4 mb-5">
            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(s.name ?? 'S')}&background=0f766e&color=fff&size=128"
                class="w-16 h-16 rounded-2xl object-cover border" />
            <div>
                <h3 class="font-display font-extrabold text-[1.2rem] text-slate-900">${s.name}</h3>
                <p class="text-[13px] text-slate-400">${s.email}</p>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold mt-1 ${isSuspended ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'}">
                    <i class="ri-circle-fill text-[6px]"></i>${isSuspended ? 'Suspended' : 'Aktif'}
                </span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 text-[13px]">
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Sekolah</p><p class="font-semibold text-slate-700">${s.school_name ?? '-'}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">NISN</p><p class="font-semibold text-slate-700">${s.nisn ?? '-'}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Jurusan</p><p class="font-semibold text-slate-700">${s.major ?? '-'}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Kelas</p><p class="font-semibold text-slate-700">${s.grade ?? '-'}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3 col-span-2"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Telepon</p><p class="font-semibold text-slate-700">${s.phone ?? '-'}</p></div>
        </div>
    `;
    document.getElementById('modal-detail-actions').innerHTML = `
        <button onclick="closeModal('modal-detail')" class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all duration-150">Tutup</button>
        ${!isSuspended
            ? `<button onclick="handleSuspend(${s.id}); closeModal('modal-detail')" class="flex-1 py-[11px] rounded-[11px] bg-red-500 text-white font-bold text-[13px] cursor-pointer border-none hover:bg-red-600 transition-all duration-150"><i class="ri-forbid-line mr-1"></i>Suspend</button>`
            : `<button onclick="handleUnsuspend(${s.id}); closeModal('modal-detail')" class="flex-1 py-[11px] rounded-[11px] bg-emerald-600 text-white font-bold text-[13px] cursor-pointer border-none hover:bg-emerald-700 transition-all duration-150"><i class="ri-refresh-line mr-1"></i>Aktifkan</button>`}
    `;
    openModal('modal-detail');
}

async function handleSuspend(id) {
    if (!confirm('Suspend siswa ini?')) return;
    try {
        const res = await fetch(`${BASE}/api/admin/skomda-students/${id}/suspend`, {
            method: 'PATCH', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        showToast('Siswa di-suspend.', 'warn'); loadStudents();
    } catch { showToast('Gagal suspend.', 'danger'); }
}

async function handleUnsuspend(id) {
    if (!confirm('Aktifkan kembali siswa ini?')) return;
    try {
        const res = await fetch(`${BASE}/api/admin/skomda-students/${id}/unsuspend`, {
            method: 'PATCH', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        showToast('Siswa diaktifkan kembali!'); loadStudents();
    } catch { showToast('Gagal mengaktifkan.', 'danger'); }
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

document.addEventListener('DOMContentLoaded', loadStudents);
</script>
@endsection