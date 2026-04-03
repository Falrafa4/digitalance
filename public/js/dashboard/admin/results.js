const META = document.querySelector('meta[name="csrf-token"]');
const CSRF = META ? META.getAttribute('content') : '';
const BASE = window.location.origin;

let allResults = [];
let activeFilter = 'all';

const STATUS_MAP = {
    pending:  { label: 'Pending',  cls: 'bg-slate-100 text-slate-500' },
    accepted: { label: 'Diterima', cls: 'bg-emerald-100 text-emerald-700' },
    revision: { label: 'Revisi',   cls: 'bg-amber-100 text-amber-700' },
};

// LOAD RESULTS
async function loadResults() {
    try {
        const res  = await fetch(`${BASE}/api/admin/results`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const json = await res.json();
        console.log("Data API Results:", json);
        allResults = Array.isArray(json) ? json : (json?.data || []);
    } catch (e) { 
        console.error("Gagal mengambil data:", e);
        allResults = []; 
    }
    renderStats();
    renderTable(allResults);
}

// RENDER STATS
function renderStats() {
    document.getElementById('stat-total').textContent    = allResults.length;
    document.getElementById('stat-accepted').textContent = allResults.filter(r => String(r.order?.status || 'pending').toLowerCase() === 'accepted').length;
    document.getElementById('stat-revision').textContent = allResults.filter(r => String(r.order?.status || 'pending').toLowerCase() === 'revision').length;
    document.getElementById('stat-pending').textContent  = allResults.filter(r => String(r.order?.status || 'pending').toLowerCase() === 'pending').length;
}

// INIT FILTERS
function initFilters() {
    const tabs = document.querySelectorAll('#filter-tabs .filter-tab');
    tabs.forEach(btn => {
        btn.addEventListener('click', () => {
            tabs.forEach(b => {
                b.className = 'filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]';
            });
            btn.className = 'filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150';
            activeFilter = btn.dataset.filter.toLowerCase();
            applyFilter();
        });
    });
}

// APPLY FILTER
function applyFilter() {
    const searchInput = document.getElementById('result-search');
    const q = searchInput ? searchInput.value.toLowerCase() : '';
    
    let filtered = [...allResults];

    if (activeFilter !== 'all') {
        filtered = filtered.filter(r => String(r.order?.status || 'pending').toLowerCase() === activeFilter);
    }

    if (q) {
        filtered = filtered.filter(r => {
            const orderId = String(r.order_id ?? '').toLowerCase();
            const fName = String(r.order?.service?.freelancer?.name ?? r.order?.service?.freelancer?.user?.name ?? '').toLowerCase();
            const version = String(r.version ?? '').toLowerCase();
            
            return orderId.includes(q) || fName.includes(q) || version.includes(q);
        });
    }
    
    renderTable(filtered);
}

document.getElementById('result-search')?.addEventListener('input', applyFilter);

// RENDER TABLE
function renderTable(list) {
    const tbody = document.getElementById('result-tbody');
    const empty = document.getElementById('result-empty');
    
    if (!list.length) { 
        tbody.innerHTML = ''; 
        empty.classList.remove('hidden'); 
        return; 
    }
    
    empty.classList.add('hidden');
    
    tbody.innerHTML = list.map(r => {
        const statusKey = String(r.order?.status || 'pending').toLowerCase();
        const st   = STATUS_MAP[statusKey] ?? { label: statusKey, cls: 'bg-slate-100 text-slate-500' };
        const date = r.created_at ? new Date(r.created_at).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' }) : '-';
        const note = (r.note ?? '-').slice(0, 50) + ((r.note ?? '').length > 50 ? '…' : '');
        const ext  = (r.file_url ?? '').split('.').pop().toLowerCase();
        const fileIcon = ['jpg','jpeg','png','gif','webp'].includes(ext) ? 'ri-image-line' :
                         ['pdf'].includes(ext) ? 'ri-file-pdf-line' :
                         ['zip','rar'].includes(ext) ? 'ri-folder-zip-line' : 'ri-file-line';
        const fName = r.order?.service?.freelancer?.name ?? r.order?.service?.freelancer?.user?.name ?? 'Freelancer';

        return `
        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors duration-150">
            <td class="px-6 py-4 text-[12px] font-mono text-slate-400">#${r.id}</td>
            <td class="px-6 py-4 text-[13px] font-semibold text-[#0f766e]">#${r.order_id ?? '-'}</td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(fName)}&background=0f766e&color=fff&size=64"
                        class="w-8 h-8 rounded-xl object-cover flex-shrink-0" />
                    <div>
                        <p class="font-semibold text-[13px] text-slate-900">${fName}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">${r.version ?? 'v1'}</p>
                    </div>
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
                <button onclick="openDetail('${r.id}')" class="w-8 h-8 rounded-[9px] bg-slate-100 text-slate-500 flex items-center justify-center text-[14px] hover:bg-[#f0fdf9] hover:text-[#0f766e] transition-all duration-150 border-none cursor-pointer" title="Detail">
                    <i class="ri-eye-line"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

// MODAL DETAIL
function openDetail(id) {
    const r = allResults.find(x => String(x.id) === String(id));
    if (!r) return;

    const statusKey = String(r.order?.status || 'pending').toLowerCase();
    const st   = STATUS_MAP[statusKey] ?? { label: statusKey, cls: 'bg-slate-100 text-slate-500' };
    const date = r.created_at ? new Date(r.created_at).toLocaleString('id-ID') : '-';
    const fName = r.order?.service?.freelancer?.name ?? r.order?.service?.freelancer?.user?.name ?? 'Freelancer';

    document.getElementById('modal-detail-content').innerHTML = `
        <div class="grid grid-cols-2 gap-3 mb-4 text-[13px]">
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">ID Hasil</p><p class="font-mono font-semibold text-slate-700">#${r.id}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Order</p><p class="font-semibold text-[#0f766e]">#${r.order_id ?? '-'}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Freelancer</p><p class="font-semibold text-slate-700">${fName}</p></div>
            <div class="bg-slate-50 rounded-2xl p-3"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Status / Versi</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold ${st.cls}"><i class="ri-circle-fill text-[6px]"></i>${st.label}</span>
                    <span class="text-xs font-bold text-slate-400">${r.version ?? '-'}</span>
                </div>
            </div>
            <div class="bg-slate-50 rounded-2xl p-3 col-span-2"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Dikirim</p><p class="font-semibold text-slate-700">${date}</p></div>
        </div>
        ${r.note ? `<div class="bg-slate-50 rounded-2xl p-3 mb-4"><p class="text-slate-400 text-[11px] font-bold uppercase mb-1">Catatan</p><p class="text-[13px] text-slate-700 leading-relaxed">${r.note}</p></div>` : ''}
        ${r.file_url ? `<a href="${BASE}/storage/${r.file_url}" target="_blank"
            class="flex items-center gap-3 px-4 py-3 bg-[#f0fdf9] border border-[#10B981]/30 rounded-2xl hover:bg-[#d1fae5] transition-all duration-150">
            <i class="ri-download-line text-[#0f766e] text-[18px]"></i>
            <span class="text-[13px] font-bold text-[#0f766e]">Unduh File ${r.version ?? ''}</span>
        </a>` : ''}
    `;
    openModal('modal-detail');
}

// OPEN MODAL
function openModal(id)  { 
    const m = document.getElementById(id); 
    if(m) { m.classList.remove('opacity-0','pointer-events-none'); m.classList.add('opacity-100'); }
}

// CLOSE MODAL
function closeModal(id) { 
    const m = document.getElementById(id); 
    if(m) { m.classList.add('opacity-0','pointer-events-none'); m.classList.remove('opacity-100'); }
}

// INIT
document.addEventListener('DOMContentLoaded', () => {
    initFilters();
    loadResults();
});
