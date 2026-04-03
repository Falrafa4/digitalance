let hasUnreadMessages = true;

// Data injection dari Blade
const rawPortfolios = window.__PORTOFOLIOS_PAGE__?.data;
let portfoliosData = Array.isArray(rawPortfolios) ? rawPortfolios : (rawPortfolios?.data || []);

// STATS RENDER
function renderStats() {
    const row = document.getElementById('stats-row');
    if (!row) return;

    const total = portfoliosData.length;

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
                <span class="stat-value">${total}</span>
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
        return;
    }
    
    wrap.style.display = 'grid';
    emptyEl.style.display = 'none';

    wrap.innerHTML = data.map(p => {
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
                         : 'https://via.placeholder.com/800x600?text=No+Image';

        return `
            <div class="port-card" onclick="openPortModal('${p.id}')">
                <div class="port-cover-wrap">
                    <img class="port-cover" src="${imageUrl}" alt="Cover" onerror="this.src='https://via.placeholder.com/800x600?text=Image+Not+Found'">
                    <div class="port-cover-overlay">
                        <span class="preview-hint"><i class="ri-eye-line"></i> Lihat Detail</span>
                    </div>
                </div>
                <div class="port-body">
                    <div class="port-id-row">
                        <span class="port-id">#${p.id}</span>
                        <span class="port-srv-id"><i class="ri-tools-line"></i> #${p.service_id}</span>
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
}

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
        <div class="modal-hero">
            <button class="modal-close" onclick="closePortModal()"><i class="ri-close-line"></i></button>
            ${imageUrl ? `<img src="${imageUrl}" alt="Header">` : ''}
        </div>
        <div class="modal-body">
            <div class="modal-role-row">
                <span class="port-id">#${p.id}</span>
                <span class="port-srv-id" style="background:var(--slate-100); padding:4px 10px; border-radius:8px; font-size:12px; font-weight:700;">
                    Service #${p.service_id}
                </span>
            </div>
            
            <h2 class="modal-name">${p.title}</h2>

            <div class="modal-info-grid">
                <div class="modal-info-card">
                    <div class="modal-info-label">Freelancer</div>
                    <div class="modal-info-value"><i class="ri-user-line"></i> ${fName}</div>
                </div>
                <div class="modal-info-card">
                    <div class="modal-info-label">ID Project</div>
                    <div class="modal-info-value"><i class="ri-hashtag"></i> ${p.service_id}</div>
                </div>
                <div class="modal-info-card" style="grid-column: span 2;">
                    <div class="modal-info-label">Tanggal Upload</div>
                    <div class="modal-info-value"><i class="ri-calendar-line"></i> ${p.created_at ? new Date(p.created_at).toLocaleString('id-ID') : '-'}</div>
                </div>
            </div>

            <p class="modal-section-title">Deskripsi Karya</p>
            <div class="desc-box">${p.description || 'Tidak ada deskripsi tersedia.'}</div>

            <div class="modal-action-group">
                <button class="modal-btn-delete" onclick="deletePort('${p.id}'); closePortModal()"><i class="ri-delete-bin-line"></i> Hapus Portofolio</button>
            </div>
        </div>
    `;

    overlay.classList.add('open');
}

function closePortModal() {
    document.getElementById('detail-modal-overlay').classList.remove('open');
}

// FILTERS & SEARCH
function initSearch() {
    const input = document.getElementById('port-search-input');
    if (!input) return;

    input.addEventListener('input', () => {
        const q = input.value.toLowerCase();
        const filtered = portfoliosData.filter(p => {
            const fName = (p.service?.freelancer?.skomda_student?.name || '').toLowerCase();
            return p.title.toLowerCase().includes(q) || 
                   fName.includes(q) || 
                   String(p.service_id).includes(q);
        });
        renderCards(filtered);
    });
}

function initFilters() {
    const tabs = document.querySelectorAll('.filter-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            renderCards(portfoliosData);
        });
    });
}

// PAGE BOOTSTRAP
function initPage() {
    renderStats();
    renderCards();
    initSearch();
    initFilters();

    const overlay = document.getElementById('detail-modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', (e) => { if (e.target === overlay) closePortModal(); });
    }
}

document.addEventListener('DOMContentLoaded', initPage);
