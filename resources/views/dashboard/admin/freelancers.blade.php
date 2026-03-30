@extends('layouts.dashboard')
@section('title', 'Freelancer Management | Digitalance')

@section('content')
<div class="content-scroll flex-1 px-4 sm:px-8 py-7 overflow-y-auto">

    {{-- Page Header --}}
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Freelancer Management</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Kelola data, verifikasi, dan status freelancer siswa SMK.</p>
        </div>
        <button id="btn-add-freelancer"
            class="inline-flex items-center gap-2 px-[22px] py-[11px] bg-[#0f766e] text-white font-display font-bold text-[13px] rounded-[12px] shadow-teal-md hover:bg-[#0a5e58] hover:shadow-teal-lg transition-all duration-200 hover:-translate-y-0.5 cursor-pointer border-none whitespace-nowrap">
            <i class="ri-user-star-line"></i> Tambah Freelancer
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[14px] mb-6 animate-fadeUp-1" id="stats-row">
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Total Freelancer</span>
            <span class="font-display text-[2rem] font-extrabold text-slate-900" id="stat-total">—</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Terverifikasi</span>
            <span class="font-display text-[2rem] font-extrabold text-emerald-600" id="stat-verified">—</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Pending Verifikasi</span>
            <span class="font-display text-[2rem] font-extrabold text-amber-500" id="stat-pending">—</span>
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
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="verified">Terverifikasi</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="pending">Pending</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="suspended">Suspended</button>
        </div>
        <div class="relative">
            <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
            <input type="text" id="freelancer-search" placeholder="Cari nama, email, keahlian…"
                class="pl-9 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
        </div>
    </div>

    {{-- Card Grid --}}
    <div class="grid gap-[18px] pb-6 animate-fadeUp-3"
        style="grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));" id="freelancer-grid"></div>

    {{-- Empty State --}}
    <div id="freelancer-empty" class="hidden text-center py-16 px-5 bg-white border-2 border-dashed border-slate-200 rounded-3xl">
        <i class="ri-user-star-line text-[44px] text-slate-300 mb-3 block"></i>
        <h3 class="font-display text-[1.15rem] text-slate-900 mb-1.5 font-bold">Tidak ada freelancer ditemukan</h3>
        <p class="text-slate-400 text-[13.5px]">Coba ubah filter atau kata kunci pencarian.</p>
    </div>
</div>
@endsection

@section('modals')
{{-- Modal: Lihat Profil Freelancer --}}
<div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200" id="modal-detail">
    <div class="modal-box bg-white rounded-3xl w-full max-w-[560px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
            <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Detail Freelancer</span>
            <button onclick="closeModal('modal-detail')" class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all duration-150"><i class="ri-close-line"></i></button>
        </div>
        <div class="px-[26px] py-[22px] overflow-y-auto flex-1" id="modal-detail-content"></div>
        <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50 flex-shrink-0" id="modal-detail-actions"></div>
    </div>
</div>

{{-- Modal: Tambah Freelancer --}}
<div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200" id="modal-add">
    <div class="modal-box bg-white rounded-3xl w-full max-w-[520px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
            <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Tambah Freelancer</span>
            <button onclick="closeModal('modal-add')" class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all duration-150"><i class="ri-close-line"></i></button>
        </div>
        <div class="px-[26px] py-[22px] overflow-y-auto flex-1">
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Nama Lengkap</label>
                    <input id="add-name" type="text" placeholder="Budi Santoso" class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Email</label>
                    <input id="add-email" type="email" placeholder="budi@smk.sch.id" class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">No. Telepon</label>
                    <input id="add-phone" type="text" placeholder="+62 812-xxxx-xxxx" class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Lokasi</label>
                    <input id="add-location" type="text" placeholder="Surabaya, Jawa Timur" class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                </div>
            </div>
            <div class="flex flex-col gap-1.5 mb-4">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Keahlian (pisah koma)</label>
                <input id="add-skills" type="text" placeholder="UI/UX Design, Figma, Web Dev" class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Bio</label>
                <textarea id="add-bio" rows="3" placeholder="Deskripsi singkat..." class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 resize-y focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]"></textarea>
            </div>
        </div>
        <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50 flex-shrink-0">
            <button onclick="closeModal('modal-add')" class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all duration-150">Batal</button>
            <button onclick="submitAddFreelancer()" class="flex-1 py-[11px] rounded-[11px] bg-[#0f766e] text-white font-bold text-[13px] cursor-pointer border-none shadow-teal-sm hover:bg-[#0a5e58] transition-all duration-150">Simpan</button>
        </div>
    </div>
</div>

{{-- Modal: Edit Freelancer --}}
<div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200" id="modal-edit">
    <div class="modal-box bg-white rounded-3xl w-full max-w-[520px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
            <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Edit Freelancer</span>
            <button onclick="closeModal('modal-edit')" class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all duration-150"><i class="ri-close-line"></i></button>
        </div>
        <div class="px-[26px] py-[22px] overflow-y-auto flex-1">
            <input type="hidden" id="edit-uid" />
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Nama Lengkap</label>
                    <input id="edit-name" type="text" class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Email</label>
                    <input id="edit-email" type="email" class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">No. Telepon</label>
                    <input id="edit-phone" type="text" class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Lokasi</label>
                    <input id="edit-location" type="text" class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                </div>
            </div>
            <div class="flex flex-col gap-1.5 mb-4">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Keahlian</label>
                <input id="edit-skills" type="text" class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Bio</label>
                <textarea id="edit-bio" rows="3" class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 resize-y focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]"></textarea>
            </div>
        </div>
        <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50 flex-shrink-0">
            <button onclick="closeModal('modal-edit')" class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all duration-150">Batal</button>
            <button onclick="submitEditFreelancer()" class="flex-1 py-[11px] rounded-[11px] bg-[#0f766e] text-white font-bold text-[13px] cursor-pointer border-none shadow-teal-sm hover:bg-[#0a5e58] transition-all duration-150">Simpan Perubahan</button>
        </div>
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

// ─── DATA ───────────────────────────────────────────
let allFreelancers = [];
let activeFilter = 'all';

async function loadFreelancers() {
    try {
        const res = await fetch(`${BASE}/api/admin/freelancers`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const json = await res.json();
        allFreelancers = json.data ?? json ?? [];
    } catch (e) {
        allFreelancers = [];
    }
    renderStats();
    renderCards(allFreelancers);
}

// ─── STATS ──────────────────────────────────────────
function renderStats() {
    const total     = allFreelancers.length;
    const verified  = allFreelancers.filter(f => f.is_verified == 1 || f.is_verified === true).length;
    const pending   = allFreelancers.filter(f => f.is_verified == 0 && f.status !== 'Suspended').length;
    const suspended = allFreelancers.filter(f => f.status === 'Suspended').length;
    document.getElementById('stat-total').textContent     = total;
    document.getElementById('stat-verified').textContent  = verified;
    document.getElementById('stat-pending').textContent   = pending;
    document.getElementById('stat-suspended').textContent = suspended;
}

// ─── FILTER TABS ────────────────────────────────────
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

document.getElementById('freelancer-search').addEventListener('input', applyFilter);

function applyFilter() {
    const q = document.getElementById('freelancer-search').value.toLowerCase();
    let filtered = allFreelancers;
    if (activeFilter === 'verified')  filtered = filtered.filter(f => f.is_verified == 1);
    if (activeFilter === 'pending')   filtered = filtered.filter(f => f.is_verified == 0 && f.status !== 'Suspended');
    if (activeFilter === 'suspended') filtered = filtered.filter(f => f.status === 'Suspended');
    if (q) filtered = filtered.filter(f =>
        (f.name ?? '').toLowerCase().includes(q) ||
        (f.email ?? '').toLowerCase().includes(q) ||
        (f.skills ?? '').toLowerCase().includes(q)
    );
    renderCards(filtered);
}

// ─── RENDER CARDS ───────────────────────────────────
const STATUS_MAP = {
    verified:  { label: 'Terverifikasi', cls: 'bg-emerald-100 text-emerald-700' },
    pending:   { label: 'Pending',       cls: 'bg-amber-100 text-amber-700' },
    Suspended: { label: 'Suspended',     cls: 'bg-red-100 text-red-700' },
};

function getStatus(f) {
    if (f.status === 'Suspended') return STATUS_MAP.Suspended;
    return f.is_verified ? STATUS_MAP.verified : STATUS_MAP.pending;
}

function renderCards(list) {
    const grid  = document.getElementById('freelancer-grid');
    const empty = document.getElementById('freelancer-empty');
    if (!list.length) { grid.innerHTML = ''; empty.classList.remove('hidden'); return; }
    empty.classList.add('hidden');
    grid.innerHTML = list.map(f => {
        const st = getStatus(f);
        const skills = (f.skills ?? '').split(',').filter(Boolean).slice(0, 3);
        const avatar = f.profile_photo
            ? `${BASE}/storage/${f.profile_photo}`
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(f.name ?? 'F')}&background=0f766e&color=fff`;
        return `
        <div class="bg-white border border-slate-200 rounded-3xl p-6 flex flex-col gap-4 hover:border-[#10B981] hover:shadow-teal-md transition-all duration-300 animate-fadeUp">
            <div class="flex items-start gap-3.5">
                <img src="${avatar}" alt="${f.name}" class="w-12 h-12 rounded-2xl object-cover border border-slate-100 flex-shrink-0" />
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-display font-bold text-[15px] text-slate-900 truncate">${f.name ?? '-'}</h3>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold ${st.cls}">
                            <i class="ri-circle-fill text-[6px]"></i>${st.label}
                        </span>
                    </div>
                    <p class="text-[12px] text-slate-400 truncate mt-0.5">${f.email ?? '-'}</p>
                    ${f.location ? `<p class="text-[11px] text-slate-400 mt-0.5"><i class="ri-map-pin-line mr-1"></i>${f.location}</p>` : ''}
                </div>
            </div>
            ${skills.length ? `
            <div class="flex flex-wrap gap-1.5">
                ${skills.map(s => `<span class="px-2.5 py-1 bg-[#f0fdf9] text-[#0f766e] text-[11px] font-semibold rounded-lg">${s.trim()}</span>`).join('')}
            </div>` : ''}
            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 text-center">
                <div>
                    <p class="font-display font-extrabold text-[1.2rem] text-slate-900">${f.total_services ?? 0}</p>
                    <p class="text-[11px] text-slate-400">Layanan</p>
                </div>
                <div>
                    <p class="font-display font-extrabold text-[1.2rem] text-slate-900">${f.avg_rating ? Number(f.avg_rating).toFixed(1) : '—'}</p>
                    <p class="text-[11px] text-slate-400">Rating</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="openDetail(${f.id})" class="flex-1 py-2 rounded-[10px] bg-slate-100 text-slate-600 text-[12px] font-bold hover:bg-slate-200 transition-all duration-150 border-none cursor-pointer">
                    <i class="ri-eye-line mr-1"></i>Detail
                </button>
                ${f.is_verified == 0 && f.status !== 'Suspended' ? `
                <button onclick="handleVerify(${f.id})" class="flex-1 py-2 rounded-[10px] bg-emerald-100 text-emerald-700 text-[12px] font-bold hover:bg-emerald-200 transition-all duration-150 border-none cursor-pointer">
                    <i class="ri-checkbox-circle-line mr-1"></i>Verifikasi
                </button>` : ''}
                ${f.status !== 'Suspended' ? `
                <button onclick="handleSuspend(${f.id})" class="py-2 px-3 rounded-[10px] bg-red-50 text-red-500 text-[12px] font-bold hover:bg-red-100 transition-all duration-150 border-none cursor-pointer">
                    <i class="ri-forbid-line"></i>
                </button>` : `
                <button onclick="handleUnsuspend(${f.id})" class="py-2 px-3 rounded-[10px] bg-emerald-50 text-emerald-600 text-[12px] font-bold hover:bg-emerald-100 transition-all duration-150 border-none cursor-pointer">
                    <i class="ri-refresh-line"></i>
                </button>`}
            </div>
        </div>`;
    }).join('');
}

// ─── ACTIONS ────────────────────────────────────────
async function openDetail(id) {
    const f = allFreelancers.find(x => x.id === id);
    if (!f) return;
    const st = getStatus(f);
    document.getElementById('modal-detail-content').innerHTML = `
        <div class="flex items-center gap-4 mb-5">
            <img src="${f.profile_photo ? BASE+'/storage/'+f.profile_photo : `https://ui-avatars.com/api/?name=${encodeURIComponent(f.name)}&background=0f766e&color=fff`}"
                class="w-16 h-16 rounded-2xl object-cover border" />
            <div>
                <h3 class="font-display font-extrabold text-[1.2rem] text-slate-900">${f.name}</h3>
                <p class="text-[13px] text-slate-400">${f.email}</p>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold mt-1 ${st.cls}">
                    <i class="ri-circle-fill text-[6px]"></i>${st.label}
                </span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 text-[13px]">
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Telepon</p><p class="font-semibold text-slate-700">${f.phone ?? '-'}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Lokasi</p><p class="font-semibold text-slate-700">${f.location ?? '-'}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Total Layanan</p><p class="font-display font-extrabold text-slate-900 text-[1.1rem]">${f.total_services ?? 0}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Rating</p><p class="font-display font-extrabold text-slate-900 text-[1.1rem]">${f.avg_rating ? Number(f.avg_rating).toFixed(1) : '—'}</p></div>
        </div>
        ${f.bio ? `<div class="mt-3 bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Bio</p><p class="text-[13px] text-slate-700 leading-relaxed">${f.bio}</p></div>` : ''}
        ${f.skills ? `<div class="mt-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-2">Keahlian</p><div class="flex flex-wrap gap-1.5">${f.skills.split(',').map(s => `<span class="px-2.5 py-1 bg-[#f0fdf9] text-[#0f766e] text-[11px] font-semibold rounded-lg">${s.trim()}</span>`).join('')}</div></div>` : ''}
    `;
    document.getElementById('modal-detail-actions').innerHTML = `
        <button onclick="closeModal('modal-detail')" class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all duration-150">Tutup</button>
        <button onclick="openEdit(${f.id})" class="flex-1 py-[11px] rounded-[11px] bg-[#0f766e] text-white font-bold text-[13px] cursor-pointer border-none hover:bg-[#0a5e58] transition-all duration-150"><i class="ri-edit-line mr-1"></i>Edit</button>
    `;
    openModal('modal-detail');
}

function openEdit(id) {
    const f = allFreelancers.find(x => x.id === id);
    if (!f) return;
    closeModal('modal-detail');
    document.getElementById('edit-uid').value      = f.id;
    document.getElementById('edit-name').value     = f.name ?? '';
    document.getElementById('edit-email').value    = f.email ?? '';
    document.getElementById('edit-phone').value    = f.phone ?? '';
    document.getElementById('edit-location').value = f.location ?? '';
    document.getElementById('edit-skills').value   = f.skills ?? '';
    document.getElementById('edit-bio').value      = f.bio ?? '';
    openModal('modal-edit');
}

async function submitAddFreelancer() {
    const body = {
        name:     document.getElementById('add-name').value,
        email:    document.getElementById('add-email').value,
        phone:    document.getElementById('add-phone').value,
        location: document.getElementById('add-location').value,
        skills:   document.getElementById('add-skills').value,
        bio:      document.getElementById('add-bio').value,
    };
    if (!body.name || !body.email) return showToast('Nama dan email wajib diisi!', 'warn');
    try {
        const res  = await fetch(`${BASE}/api/admin/freelancers`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(body)
        });
        if (!res.ok) throw new Error();
        closeModal('modal-add');
        showToast('Freelancer berhasil ditambahkan!');
        loadFreelancers();
    } catch { showToast('Gagal menambahkan freelancer.', 'danger'); }
}

async function submitEditFreelancer() {
    const id   = document.getElementById('edit-uid').value;
    const body = {
        name:     document.getElementById('edit-name').value,
        email:    document.getElementById('edit-email').value,
        phone:    document.getElementById('edit-phone').value,
        location: document.getElementById('edit-location').value,
        skills:   document.getElementById('edit-skills').value,
        bio:      document.getElementById('edit-bio').value,
    };
    try {
        const res = await fetch(`${BASE}/api/admin/freelancers/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(body)
        });
        if (!res.ok) throw new Error();
        closeModal('modal-edit');
        showToast('Freelancer berhasil diupdate!');
        loadFreelancers();
    } catch { showToast('Gagal mengupdate freelancer.', 'danger'); }
}

async function handleVerify(id) {
    if (!confirm('Verifikasi freelancer ini?')) return;
    try {
        const res = await fetch(`${BASE}/api/admin/freelancers/${id}/verify`, {
            method: 'PATCH',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        showToast('Freelancer berhasil diverifikasi!');
        loadFreelancers();
    } catch { showToast('Gagal verifikasi.', 'danger'); }
}

async function handleSuspend(id) {
    if (!confirm('Suspend freelancer ini?')) return;
    try {
        const res = await fetch(`${BASE}/api/admin/freelancers/${id}/suspend`, {
            method: 'PATCH',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        showToast('Freelancer di-suspend.', 'warn');
        loadFreelancers();
    } catch { showToast('Gagal suspend.', 'danger'); }
}

async function handleUnsuspend(id) {
    if (!confirm('Aktifkan kembali freelancer ini?')) return;
    try {
        const res = await fetch(`${BASE}/api/admin/freelancers/${id}/unsuspend`, {
            method: 'PATCH',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        if (!res.ok) throw new Error();
        showToast('Freelancer diaktifkan kembali!');
        loadFreelancers();
    } catch { showToast('Gagal mengaktifkan.', 'danger'); }
}

// ─── MODAL HELPERS ──────────────────────────────────
function openModal(id)  { const m = document.getElementById(id); m.classList.remove('opacity-0','pointer-events-none'); m.classList.add('opacity-100'); }
function closeModal(id) { const m = document.getElementById(id); m.classList.add('opacity-0','pointer-events-none'); m.classList.remove('opacity-100'); }

document.getElementById('btn-add-freelancer').addEventListener('click', () => openModal('modal-add'));

// ─── TOAST ──────────────────────────────────────────
function showToast(msg, type = 'success') {
    const colors = { success: 'border-[#10b981] text-emerald-800', danger: 'border-red-400 text-red-800', warn: 'border-orange-400 text-orange-800' };
    const icons  = { success: 'ri-check-double-line', danger: 'ri-close-circle-line', warn: 'ri-alert-line' };
    const el = document.createElement('div');
    el.className = `toast flex items-center gap-2.5 px-[18px] py-[13px] border-[1.5px] bg-white rounded-[13px] text-[13px] font-semibold max-w-[300px] shadow-lg ${colors[type]||colors.success}`;
    el.innerHTML = `<i class="text-[17px] flex-shrink-0 ${icons[type]||icons.success}"></i><span>${msg}</span><button onclick="this.closest('.toast').remove()" class="ml-auto bg-transparent border-none cursor-pointer opacity-50 hover:opacity-100 text-[15px] p-0"><i class="ri-close-line"></i></button>`;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// ─── INIT ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', loadFreelancers);
</script>
@endsection