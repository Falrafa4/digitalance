let offersData = [];
let negoData = [];

document.addEventListener('DOMContentLoaded', function() {
    // 1. Ambil data dari Bridge yang ada di Blade
    const pageData = window.OFFERS_PAGE || {};
    offersData = Array.isArray(pageData.offers) ? pageData.offers : [];
    negoData = Array.isArray(pageData.negotiations) ? pageData.negotiations : [];
    
    // 2. Jalankan Inisialisasi
    initPage();
});

function initPage() {
    renderStats();
    renderOffersTable();
    renderNegoTable();
    initTabEvents();
    initFilterEvents();
    
    // Event listener untuk menutup modal jika klik di luar box (overlay)
    const overlay = document.getElementById('detail-modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });
    }

    // Support tutup modal pakai tombol ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
}

// ==========================================
// RENDERER FUNCTIONS
// ==========================================

function renderStats() {
    const row = document.getElementById('stats-row');
    if (!row) return;

    const totalOffers = offersData.length;
    const pendingOffers = offersData.filter(o => String(o.status).toLowerCase() === 'sent').length;
    const totalNego = negoData.length;

    row.innerHTML = `
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 text-2xl">
                <i class="ri-price-tag-3-line"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">${totalOffers}</div>
                <div class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Total Offers</div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 text-2xl">
                <i class="ri-time-line"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">${pendingOffers}</div>
                <div class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Pending</div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-2xl">
                <i class="ri-discuss-line"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">${totalNego}</div>
                <div class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Negotiations</div>
            </div>
        </div>
    `;
}

function renderOffersTable(data = offersData) {
    const tbody = document.getElementById('offers-tbody');
    const emptyState = document.getElementById('offers-empty');
    if (!tbody) return;

    tbody.innerHTML = '';
    if (data.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    data.forEach(offer => {
        const row = `
            <tr class="hover:bg-gray-50 transition border-b border-gray-50">
                <td class="px-6 py-4 text-sm font-bold text-gray-700">#OFF-${offer.id}</td>
                <td class="px-6 py-4">
                    <div class="text-sm font-semibold text-gray-800">${offer.order?.client?.name || 'User'}</div>
                    <div class="text-[10px] text-gray-400 uppercase font-medium">To: ${offer.order?.service?.freelancer?.name || 'Freelancer'}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">${offer.order?.service?.title || 'N/A'}</td>
                <td class="px-6 py-4 text-sm font-bold text-teal-600">Rp ${Number(offer.offered_price || offer.amount || 0).toLocaleString('id-ID')}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full ${getStatusColor(offer.status)}">
                        ${offer.status}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <button onclick="openOfferModal(${offer.id})" class="p-2 text-teal-600 hover:bg-teal-50 rounded-lg transition group">
                        <i class="ri-eye-line text-lg group-hover:scale-110"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

function renderNegoTable(data = negoData) {
    const tbody = document.getElementById('nego-tbody');
    const emptyState = document.getElementById('nego-empty');
    if (!tbody) return;

    tbody.innerHTML = '';
    if (data.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    data.forEach(n => {
        const row = `
            <tr class="hover:bg-gray-50 transition border-b border-gray-50">
                <td class="px-6 py-4 text-sm font-bold text-gray-700">#NG-${n.id}</td>
                <td class="px-6 py-4 text-sm text-gray-500">#ORD-${n.order_id}</td>
                <td class="px-6 py-4">
                    <div class="text-sm font-semibold text-gray-800 uppercase">${n.sender_type}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-[200px]">${n.message}</td>
                <td class="px-6 py-4 text-center">
                    <button onclick="openNegoModal(${n.id})" class="p-2 text-teal-600 hover:bg-teal-50 rounded-lg transition">
                        <i class="ri-chat-search-line text-lg"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// ==========================================
// CORE LOGIC (TAB, MODAL, FILTER)
// ==========================================

function initTabEvents() {
    const tabs = document.querySelectorAll('.section-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active state
            tabs.forEach(t => t.classList.remove('active', 'text-teal-600', 'border-teal-600'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            // Add active state to clicked tab
            this.classList.add('active', 'text-teal-600', 'border-teal-600');
            const target = this.getAttribute('data-target');
            const targetEl = document.getElementById(target);
            if (targetEl) targetEl.classList.add('active');
        });
    });
}

function initFilterEvents() {
    const filters = document.querySelectorAll('.filter-tab');
    filters.forEach(f => {
        f.addEventListener('click', function() {
            filters.forEach(t => {
                t.classList.remove('active', 'bg-teal-600', 'text-white');
                t.classList.add('bg-white', 'text-gray-600');
            });
            this.classList.add('active', 'bg-teal-600', 'text-white');
            
            const filterValue = this.getAttribute('data-filter');
            const filtered = filterValue === 'all' 
                ? offersData 
                : offersData.filter(o => String(o.status).toLowerCase() === filterValue.toLowerCase());
            renderOffersTable(filtered);
        });
    });
}

function handleSearch() {
    const q = document.getElementById('global-search-input').value.toLowerCase();
    
    const filteredOffers = offersData.filter(o => 
        String(o.id).includes(q) || 
        (o.order?.client?.name || '').toLowerCase().includes(q) ||
        (o.order?.service?.title || '').toLowerCase().includes(q)
    );
    renderOffersTable(filteredOffers);

    const filteredNego = negoData.filter(n => 
        String(n.id).includes(q) || 
        n.message.toLowerCase().includes(q)
    );
    renderNegoTable(filteredNego);
}

function openOfferModal(id) {
    const o = offersData.find(x => x.id == id);
    if (!o) return;

    const box = document.getElementById('detail-modal-box');
    const overlay = document.getElementById('detail-modal-overlay');

    box.innerHTML = `
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Detail Tawaran #${o.id}</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="ri-close-line text-2xl"></i></button>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 bg-gray-50 rounded-2xl">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Harga Ditawarkan</span>
                    <div class="text-lg font-bold text-teal-600">Rp ${Number(o.offered_price || o.amount || 0).toLocaleString('id-ID')}</div>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Status</span>
                    <div><span class="px-2 py-0.5 text-[10px] font-bold rounded-full ${getStatusColor(o.status)}">${o.status}</span></div>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold">Client / Freelancer</span>
                    <p class="font-semibold text-gray-800">${o.order?.client?.name} ⮕ ${o.order?.service?.freelancer?.name}</p>
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
    overlay.classList.remove('hidden');
}

function openNegoModal(id) {
    const n = negoData.find(x => x.id == id);
    if (!n) return;

    const box = document.getElementById('detail-modal-box');
    const overlay = document.getElementById('detail-modal-overlay');

    box.innerHTML = `
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="ri-discuss-line"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Pesan Negosiasi</h2>
            <p class="text-xs text-gray-400 uppercase font-bold mb-6">Dari: ${n.sender_type} | Order #${n.order_id}</p>
            <div class="bg-gray-50 p-6 rounded-2xl text-gray-700 leading-relaxed font-medium italic">
                "${n.message}"
            </div>
            <button onclick="closeModal()" class="mt-8 w-full py-3 bg-teal-600 text-white font-bold rounded-xl hover:bg-teal-700 transition">Selesai Membaca</button>
        </div>
    `;
    overlay.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('detail-modal-overlay').classList.add('hidden');
}

function getStatusColor(status) {
    const s = String(status).toLowerCase();
    if (s === 'accepted') return 'bg-green-100 text-green-700';
    if (s === 'rejected') return 'bg-red-100 text-red-700';
    if (s === 'sent') return 'bg-amber-100 text-amber-700';
    return 'bg-gray-100 text-gray-600';
}