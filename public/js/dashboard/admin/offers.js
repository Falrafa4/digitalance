const $ = (id) => document.getElementById(id);
const $$ = (sel) => Array.from(document.querySelectorAll(sel));

let offersState = {
    data: [],
    filteredData: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: '',
    filterStatus: 'all'
};

let negoState = {
    data: [],
    filteredData: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: ''
};

document.addEventListener('DOMContentLoaded', function() {
    const pageData = window.__OFFERS_PAGE__ || {};
    offersState.data = Array.isArray(pageData.offers) ? pageData.offers : [];
    negoState.data = Array.isArray(pageData.negotiations) ? pageData.negotiations : [];

    offersState.filteredData = [...offersState.data];
    negoState.filteredData = [...negoState.data];

    initPage();
});

function initPage() {
    renderStats();
    renderOffers();
    renderNego();
    initTabEvents();
    initFilterEvents();
    initPaginationEvents();
    initSearchEvents();

    const overlay = $('modal-offers-overlay');
    if (overlay) {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
}

// State update functions
function updateOffersState(updates) {
    Object.assign(offersState, updates);
    if ('searchQuery' in updates || 'filterStatus' in updates) {
        filterOffersData();
        offersState.currentPage = 1;
    }
    renderOffers();
}

function updateNegoState(updates) {
    Object.assign(negoState, updates);
    if ('searchQuery' in updates) {
        filterNegoData();
        negoState.currentPage = 1;
    }
    renderNego();
}

function filterOffersData() {
    let filtered = [...offersState.data];
    if (offersState.searchQuery) {
        const q = offersState.searchQuery.toLowerCase();
        filtered = filtered.filter(o => 
            String(o.id).includes(q) || 
            (o.order?.client?.name || '').toLowerCase().includes(q) ||
            (o.order?.service?.title || '').toLowerCase().includes(q)
        );
    }
    if (offersState.filterStatus !== 'all') {
        filtered = filtered.filter(o => String(o.status).toLowerCase() === offersState.filterStatus.toLowerCase());
    }
    offersState.filteredData = filtered;
}

function filterNegoData() {
    let filtered = [...negoState.data];
    if (negoState.searchQuery) {
        const q = negoState.searchQuery.toLowerCase();
        filtered = filtered.filter(n => 
            String(n.id).includes(q) || 
            String(n.order_id).includes(q) ||
            (n.message || '').toLowerCase().includes(q) ||
            (n.sender || '').toLowerCase().includes(q)
        );
    }
    negoState.filteredData = filtered;
}

// ==========================================
// RENDERER FUNCTIONS
// ==========================================

function renderStats() {
    const row = $('stats-row');
    if (!row) return;

    const totalOffers = offersState.data.length;
    const pendingOffers = offersState.data.filter(o => String(o.status).toLowerCase() === 'sent').length;
    const totalNego = negoState.data.length;

    row.innerHTML = `
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 text-2xl"><i class="ri-price-tag-3-line"></i></div>
            <div>
                <div class="text-2xl font-bold text-gray-800">${totalOffers}</div>
                <div class="text-[10px] text-gray-400 uppercase font-bold">Total Tawaran</div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 text-2xl"><i class="ri-time-line"></i></div>
            <div>
                <div class="text-2xl font-bold text-gray-800">${pendingOffers}</div>
                <div class="text-[10px] text-gray-400 uppercase font-bold">Tertunda</div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-2xl"><i class="ri-discuss-line"></i></div>
            <div>
                <div class="text-2xl font-bold text-gray-800">${totalNego}</div>
                <div class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Negosiasi</div>
            </div>
        </div>
    `;
}

function renderOffers() {
    const tbody = $('offers-tbody');
    const emptyState = $('offers-empty');
    const pagination = $('offers-pagination');
    if (!tbody) return;

    const start = (offersState.currentPage - 1) * offersState.pageSize;
    const end = start + offersState.pageSize;
    const paginatedData = offersState.filteredData.slice(start, end);

    tbody.innerHTML = '';
    if (paginatedData.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (pagination) pagination.classList.add('hidden');
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    if (pagination) pagination.classList.remove('hidden');

    paginatedData.forEach(offer => {
        tbody.insertAdjacentHTML('beforeend', `
            <tr class="hover:bg-gray-50 transition border-b border-gray-50">
                <td class="px-6 py-4 text-sm font-bold text-gray-700">#OFF-${offer.id}</td>
                <td class="px-6 py-4">
                    <div class="text-sm font-semibold text-gray-800">${offer.order?.client?.name || 'Pengguna'}</div>
                    <div class="text-[10px] text-gray-400 uppercase font-medium">Kepada: ${offer.order?.service?.freelancer?.skomda_student?.name || 'Freelancer'}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">${offer.order?.service?.title || 'Tidak tersedia'}</td>
                <td class="px-6 py-4 text-sm font-bold text-teal-600">Rp ${Number(offer.offered_price || 0).toLocaleString('id-ID')}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full ${getStatusColor(offer.status)}">${offer.status}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <button onclick="openOfferModal(${offer.id})" class="p-2 text-teal-600 hover:bg-teal-50 rounded-lg transition group">
                        <i class="ri-eye-line text-lg group-hover:scale-110"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    const total = offersState.filteredData.length;
    updatePaginationInfo('offers', start, end, total, offersState.currentPage, offersState.pageSize);
}

function updatePaginationInfo(prefix, start, end, total, currentPage, pageSize) {
    const showingStartEl = $(prefix + '-showing-start');
    const showingEndEl = $(prefix + '-showing-end');
    const totalEl = $(prefix + '-total');
    const prevBtn = $(prefix + '-prev-btn');
    const nextBtn = $(prefix + '-next-btn');

    if (showingStartEl) showingStartEl.textContent = total === 0 ? 0 : start + 1;
    if (showingEndEl) showingEndEl.textContent = Math.min(end, total);
    if (totalEl) totalEl.textContent = total;
    if (prevBtn) prevBtn.disabled = currentPage === 1;
    if (nextBtn) nextBtn.disabled = end >= total;
}

function renderNego() {
    const tbody = $('nego-tbody');
    const emptyState = $('nego-empty');
    const pagination = $('nego-pagination');
    if (!tbody) return;

    const start = (negoState.currentPage - 1) * negoState.pageSize;
    const end = start + negoState.pageSize;
    const paginatedData = negoState.filteredData.slice(start, end);

    tbody.innerHTML = '';
    if (paginatedData.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (pagination) pagination.classList.add('hidden');
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    if (pagination) pagination.classList.remove('hidden');

    paginatedData.forEach(n => {
        const negoId = (n && (n.id || n.nego_id)) ? (n.id || n.nego_id) : '';
        tbody.insertAdjacentHTML('beforeend', `
            <tr class="hover:bg-gray-50 transition border-b border-gray-50">
                <td class="px-6 py-4 text-sm font-bold text-gray-700">#NG-${negoId}</td>
                <td class="px-6 py-4 text-sm text-gray-500">#ORD-${n.order_id || ''}</td>
                <td class="px-6 py-4">
                    <div class="text-sm font-semibold text-gray-800 uppercase">${n.sender || ''}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-[200px]">${n.message || ''}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${n.created_at ? new Date(n.created_at).toLocaleString('id-ID') : ''}</td>
                <td class="px-6 py-4 text-center">
                    <button onclick="openNegoModal('${negoId}')" class="p-2 text-teal-600 hover:bg-teal-50 rounded-lg transition group" aria-label="Lihat Negosiasi">
                        <i class="ri-eye-line text-lg group-hover:scale-110"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    const total = negoState.filteredData.length;
    updatePaginationInfo('nego', start, end, total, negoState.currentPage, negoState.pageSize);
}

// ==========================================
// CORE LOGIC
// ==========================================

function initTabEvents() {
    $$('.section-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            $$('.section-tab').forEach(t => {
                t.classList.remove('active', 'text-teal-600', 'border-teal-600');
                t.classList.add('text-gray-500', 'border-transparent');
            });
            $$('.tab-content').forEach(c => c.classList.remove('active'));

            this.classList.add('active', 'text-teal-600', 'border-teal-600');
            this.classList.remove('text-gray-500', 'border-transparent');

            const target = this.getAttribute('data-target');
            const targetEl = document.getElementById(target);
            if (targetEl) targetEl.classList.add('active');
        });
    });
}

function initFilterEvents() {
    $$('.filter-tab').forEach(f => {
        f.addEventListener('click', function() {
            $$('.filter-tab').forEach(t => {
                t.classList.remove('active', 'bg-teal-600', 'text-white');
                t.classList.add('bg-white', 'text-gray-600');
            });
            this.classList.add('active', 'bg-teal-600', 'text-white');
            updateOffersState({ filterStatus: this.getAttribute('data-filter') });
        });
    });
}

function initPaginationEvents() {
    const offersPrev = document.getElementById('offers-prev-btn');
    const offersNext = document.getElementById('offers-next-btn');
    const negoPrev = document.getElementById('nego-prev-btn');
    const negoNext = document.getElementById('nego-next-btn');

    if (offersPrev) offersPrev.addEventListener('click', () => {
        if (offersState.currentPage > 1) updateOffersState({ currentPage: offersState.currentPage - 1 });
    });
    if (offersNext) offersNext.addEventListener('click', () => {
        if (offersState.currentPage * offersState.pageSize < offersState.filteredData.length) {
            updateOffersState({ currentPage: offersState.currentPage + 1 });
        }
    });
    if (negoPrev) negoPrev.addEventListener('click', () => {
        if (negoState.currentPage > 1) updateNegoState({ currentPage: negoState.currentPage - 1 });
    });
    if (negoNext) negoNext.addEventListener('click', () => {
        if (negoState.currentPage * negoState.pageSize < negoState.filteredData.length) {
            updateNegoState({ currentPage: negoState.currentPage + 1 });
        }
    });
}

function initSearchEvents() {
    const searchInput = document.getElementById('global-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value;
            updateOffersState({ searchQuery: q });
            updateNegoState({ searchQuery: q });
        });
    }
}

function openModal() {
    const overlay = $('modal-offers-overlay');
    if (overlay) {
        overlay.classList.remove('hidden');
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.style.opacity = '1';
        overlay.style.pointerEvents = 'all';
    }
}

function closeModal() {
    const overlay = $('modal-offers-overlay');
    if (overlay) {
        overlay.classList.add('hidden');
    }
}

function openOfferModal(id) {
    const o = offersState.data.find(x => x.id == id);
    if (!o) return;

    const box = $('modal-offers-box');
    if (!box) return;
    box.innerHTML = `
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Detail Tawaran #${o.id}</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="ri-close-line text-2xl"></i></button>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 bg-gray-50 rounded-2xl">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Harga Ditawarkan</span>
                    <div class="text-lg font-bold text-teal-600">Rp ${Number(o.offered_price || 0).toLocaleString('id-ID')}</div>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Status</span>
                    <div><span class="px-2 py-0.5 text-[10px] font-bold rounded-full ${getStatusColor(o.status)}">${o.status}</span></div>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold">Klien ⮕ Freelancer</span>
                    <p class="font-semibold text-gray-800">${o.order?.client?.name || 'Tidak tersedia'} ⮕ ${o.order?.service?.freelancer?.skomda_student?.name || 'Tidak tersedia'}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold">Pesan Tambahan</span>
                    <div class="p-4 bg-teal-50/50 border border-teal-100 rounded-xl text-gray-600 italic mt-1">
                        "${o.message || 'Tidak ada pesan khusus.'}"
                    </div>
                </div>
            </div>
            <button onclick="closeModal()" class="mt-8 w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition">Tutup Detail</button>
        </div>
    `;
    openModal();
}

function openNegoModal(id) {
    const n = negoState.data.find(x => x.id == id);
    if (!n) return;

    const box = $('modal-offers-box');
    if (!box) return;
    box.innerHTML = `
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Detail Negosiasi #${n.id}</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="ri-close-line text-2xl"></i></button>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 bg-gray-50 rounded-2xl">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Pengirim</span>
                    <div class="text-lg font-bold text-teal-600 uppercase">${n.sender}</div>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Order ID</span>
                    <div class="text-lg font-bold text-gray-800">#ORD-${n.order_id}</div>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold">Isi Pesan Negosiasi</span>
                    <div class="p-4 bg-teal-50/50 border border-teal-100 rounded-xl text-gray-600 italic mt-1">
                        "${n.message}"
                    </div>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold">Waktu Kirim</span>
                    <p class="font-semibold text-gray-800">${new Date(n.created_at).toLocaleString('id-ID')}</p>
                </div>
            </div>
            <button onclick="closeModal()" class="mt-8 w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition">Tutup Detail</button>
        </div>
    `;
    openModal();
}

function getStatusColor(status) {
    const s = String(status).toLowerCase();
    if (s === 'accepted') return 'bg-green-100 text-green-700';
    if (s === 'rejected') return 'bg-red-100 text-red-700';
    if (s === 'sent') return 'bg-amber-100 text-amber-700';
    return 'bg-gray-100 text-gray-600';
}