let hasUnreadMessages = true;

// Data injection dari Blade
const rawPortfolios = window.__PORTOFOLIOS_PAGE__?.data;
let portfoliosData = Array.isArray(rawPortfolios) ? rawPortfolios : (rawPortfolios?.data || []);

let currentPage = 1;
const itemsPerPage = 8;

// STATS RENDER
function renderStats() {
    const row = document.getElementById('stats-row');
    if (!row) return;

    const total = portfoliosData.length;
    // Asumsikan status ada, jika tidak, kita hitung yang disetujui (contoh: status 'approved' atau fallback)
    const approvedCount = portfoliosData.filter(p => String(p.status || 'approved').toLowerCase() === 'approved').length;

    row.innerHTML = `
        <div class="stat-card">
            <div class="stat-icon blue"><i class="ri-folder-user-line"></i></div>
            <div class="stat-text">
                <span class="stat-value">${total}</span>
                <span class="stat-label">Total Portofolio</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal"><i class="ri-checkbox-circle-line"></i></div>
            <div class="stat-text">
                <span class="stat-value">${approvedCount}</span>
                <span class="stat-label">Karya Terverifikasi</span>
            </div>
        </div>
    `;
}

// RENDER CARDS
function renderCards(data = portfoliosData) {
    const wrap = document.getElementById('port-cards-wrap');
    const emptyEl = document.getElementById('port-empty');
    if (!wrap) return;

    if (!data || data.length === 0) {
        wrap.style.display = 'none';
        emptyEl.style.display = 'block';
        renderPagination(0);
        return;
    }
    
    wrap.style.display = 'grid';
    emptyEl.style.display = 'none';

    const totalPages = Math.ceil(data.length / itemsPerPage);
    if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
    const startIndex = (currentPage - 1) * itemsPerPage;
    const paginatedData = data.slice(startIndex, startIndex + itemsPerPage);

    wrap.innerHTML = paginatedData.map(p => {
        const fName = p.service?.freelancer?.skomda_student?.name 
                      ?? p.service?.freelancer?.name 
                      ?? 'Freelancer';

        const date = p.created_at ? new Date(p.created_at).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }) : '-';

        const imageUrl = p.media_url 
                         ? `${window.location.origin}/storage/${p.media_url}` 
                         : 'https://placehold.co/800x600?text=No+Image';

        return `
            <div class="port-card" onclick="openPortModal('${p.id}')">
                <div class="port-cover-wrap">
                    <img class="port-cover" src="${imageUrl}" alt="Cover" onerror="this.src='https://placehold.co/800x600?text=Image+Not+Found'" lazyload>
                    <div class="port-cover-overlay">
                        <span class="preview-hint"><i class="ri-eye-line"></i> Lihat Detail</span>
                    </div>
                </div>
                <div class="port-body">
                    <div class="port-id-row">
                        <span class="port-id">#${p.id}</span>
                        <span class="port-srv-id"><i class="ri-tools-line"></i> #${p.service?.title ?? p.service_id ?? '-'}</span>
                    </div>
                    <h3 class="port-title" title="${p.title}">${p.title}</h3>
                    <p class="port-desc">${p.description ? p.description.slice(0, 75) + '...' : '-'}</p>
                    <div class="port-meta-row">
                        <span class="port-freelancer"><i class="ri-user-line"></i> ${fName}</span>
                        <span class="port-date"><i class="ri-calendar-line"></i> ${date}</span>
                    </div>
                </div>
                <div class="port-footer">
                    <button class="card-btn-view" onclick="event.stopPropagation(); openPortModal('${p.id}')">
                        <i class="ri-eye-line"></i> Lihat Detail
                    </button>
                </div>
            </div>
        `;
    }).join('');

    renderPagination(totalPages);
}

function renderPagination(totalPages) {
    let wrap = document.getElementById('pagination-wrap');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'pagination-wrap';
        wrap.className = 'flex justify-center gap-2 mt-8';
        const container = document.getElementById('port-cards-wrap').parentNode;
        container.insertBefore(wrap, document.getElementById('port-cards-wrap').nextSibling);
    }
    
    if (totalPages <= 1) {
        wrap.innerHTML = '';
        return;
    }

    let html = '';
    html += `<button class="px-3 py-1 rounded border border-slate-200 bg-white text-sm hover:bg-slate-50 disabled:opacity-50" ${currentPage === 1 ? 'disabled' : ''} onclick="window.changePortPage(${currentPage - 1})">Prev</button>`;
    
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `<button class="px-3 py-1 rounded border ${i === currentPage ? 'bg-[#0f766e] text-white border-[#0f766e]' : 'border-slate-200 bg-white hover:bg-slate-50'} text-sm" onclick="window.changePortPage(${i})">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<span class="px-2 py-1 text-slate-400">...</span>`;
        }
    }

    html += `<button class="px-3 py-1 rounded border border-slate-200 bg-white text-sm hover:bg-slate-50 disabled:opacity-50" ${currentPage === totalPages ? 'disabled' : ''} onclick="window.changePortPage(${currentPage + 1})">Next</button>`;
    wrap.innerHTML = html;
}

window.changePortPage = function(page) {
    currentPage = page;
    refreshGrid();
};

// MODAL DETAIL
function openPortModal(id) {
    const p = portfoliosData.find(x => String(x.id) === String(id));
    if (!p) return;

    const overlay = document.getElementById('detail-modal-overlay');
    const box = document.getElementById('detail-modal-box');
    
    const fName = p.service?.freelancer?.skomda_student?.name 
                  ?? p.service?.freelancer?.name 
                  ?? 'Freelancer';

    const imageUrl = p.media_url ? `${window.location.origin}/storage/${p.media_url}` : '';

    box.innerHTML = `
        <div class="modal-hero relative">
            <button class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-black/50 text-white rounded-full hover:bg-black/70 transition" onclick="closePortModal()"><i class="ri-close-line"></i></button>
            ${imageUrl ? `<img src="${imageUrl}" alt="Header" class="w-full h-48 object-cover">` : ''}
        </div>
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="font-bold text-slate-800">#${p.id}</span>
                <span class="bg-slate-100 px-3 py-1 rounded-lg text-xs font-bold text-slate-600">
                    Service #${p.service_id}
                </span>
            </div>
            
            <h2 class="text-xl font-bold text-slate-900 mb-6">${p.title}</h2>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Freelancer</div>
                    <div class="text-sm font-semibold text-slate-800 truncate"><i class="ri-user-line text-slate-400 mr-1"></i> ${fName}</div>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Upload</div>
                    <div class="text-sm font-semibold text-slate-800"><i class="ri-calendar-line text-slate-400 mr-1"></i> ${p.created_at ? new Date(p.created_at).toLocaleDateString('id-ID') : '-'}</div>
                </div>
            </div>

            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Karya</p>
            <div class="text-sm text-slate-600 leading-relaxed mb-6 bg-slate-50 p-4 rounded-xl border border-slate-100">${p.description || 'Tidak ada deskripsi tersedia.'}</div>

            <div class="flex gap-2 border-t border-slate-100 pt-4">
                <button class="flex-1 bg-red-50 text-red-600 py-2.5 rounded-xl font-bold text-sm hover:bg-red-100 transition flex items-center justify-center gap-2" onclick="deletePort('${p.id}'); closePortModal()"><i class="ri-delete-bin-line"></i> Hapus</button>
            </div>
        </div>
    `;

    // Pastikan z-index modal portfolio tinggi agar tidak tertutup
    overlay.classList.add('open', 'z-[9999]');
    overlay.style.display = 'flex';
}

function closePortModal() {
    const overlay = document.getElementById('detail-modal-overlay');
    overlay.classList.remove('open');
    overlay.style.display = 'none';
}

function refreshGrid() {
    const activeTab = document.querySelector('.filter-tab.active');
    const f = activeTab ? activeTab.dataset.filter.toLowerCase() : 'all';
    const input = document.getElementById('port-search-input');
    const q = input ? input.value.toLowerCase() : '';

    let res = portfoliosData;
    
    if (f !== 'all') {
        res = res.filter(p => String(p.status || 'pending').toLowerCase() === f);
    }
    
    if (q) {
        res = res.filter(p => {
            const fName = (p.service?.freelancer?.skomda_student?.name || '').toLowerCase();
            return p.title.toLowerCase().includes(q) || 
                   fName.includes(q) || 
                   String(p.service_id).includes(q);
        });
    }
    
    renderCards(res);
}

// FILTERS & SEARCH
function initSearch() {
    const input = document.getElementById('port-search-input');
    if (!input) return;

    input.addEventListener('input', () => {
        currentPage = 1;
        refreshGrid();
    });
}

function initFilters() {
    const tabs = document.querySelectorAll('.filter-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            currentPage = 1;
            refreshGrid();
        });
    });
}

// PAGE BOOTSTRAP
function initPage() {
    renderStats();
    refreshGrid(); // Memanggil refreshGrid alih-alih renderCards langsung agar filter awal diterapkan
    initSearch();
    initFilters();

    const overlay = document.getElementById('detail-modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', (e) => { if (e.target === overlay) closePortModal(); });
    }
    
    // Add Portofolio Button (Dummy / Mock for now)
    const btnAddPort = document.getElementById('btn-add-port');
    if(btnAddPort) {
        btnAddPort.addEventListener('click', () => {
            if(window.showToast) {
                window.showToast('Info', 'Form tambah portofolio terbuka. (Fitur dalam pengembangan)', 'success');
            } else {
                alert('Form tambah portofolio');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', initPage);
