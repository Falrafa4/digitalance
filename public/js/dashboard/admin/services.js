let hasUnreadMessages = true;

// DATA
const rawServices = window.__SERVICES_PAGE__?.data;
let servicesData = Array.isArray(rawServices) ? rawServices : (rawServices?.data || []);

// HELPERS
function formatRupiah(number) {
  if (!number) return '-';
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
}

function formatPriceRange(min, max) {
  if (max && max > min) return `${formatRupiah(min)} - ${formatRupiah(max)}`;
  return formatRupiah(min);
}

// STATS
function renderStats() {
  const row = document.getElementById('stats-row');
  if (!row) return;

  const total = servicesData.length;
  const approved = servicesData.filter(s => String(s.status).toLowerCase() === 'approved').length;
  const pending = servicesData.filter(s => String(s.status).toLowerCase() === 'pending').length;
  const draft = servicesData.filter(s => String(s.status).toLowerCase() === 'draft').length;

  row.innerHTML = `
    <div class="stat-card">
      <div class="stat-icon blue"><i class="ri-tools-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${total}</span>
        <span class="stat-label">Total Services</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon teal"><i class="ri-checkbox-circle-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${approved}</span>
        <span class="stat-label">Approved</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon amber"><i class="ri-time-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${pending}</span>
        <span class="stat-label">Pending Approval</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon slate"><i class="ri-draft-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${draft}</span>
        <span class="stat-label">Draft</span>
      </div>
    </div>
  `;
}

// CARDS
function renderCards(data = servicesData) {
  const wrap = document.getElementById('service-cards-wrap');
  const emptyEl = document.getElementById('service-empty');
  if (!wrap) return;

  if (!data || data.length === 0) {
    wrap.style.display = 'none';
    emptyEl.style.display = 'block';
    return;
  }
  
  wrap.style.display = 'grid';
  emptyEl.style.display = 'none';

  wrap.innerHTML = data.map(s => {
    const fName = s.freelancer?.skomda_student?.name ?? s.freelancer?.name ?? 'Freelancer';
    const catName = s.service_category?.name ?? s.category?.name ?? 'Kategori';
    const rawStatus = String(s.status || 'Draft').toLowerCase();

    let actionButtons = `<button class="btn-action" title="Detail" onclick="openServiceModal('${s.id}')"><i class="ri-eye-line"></i></button>`;

    return `
      <div class="service-card">
        <div class="card-header">
          <span class="service-id">#${s.id}</span>
          <span class="status-pill status-${rawStatus}">${s.status}</span>
        </div>
        <div class="card-body">
          <span class="cat-badge">${catName}</span>
          <h3 class="service-title" title="${s.title}">${s.title}</h3>
          
          <div style="margin-top: 4px; display: flex; flex-direction: column; gap: 6px;">
            <div class="card-info-row">
              <i class="ri-user-line"></i> ${fName}
            </div>
            <div class="card-info-row">
              <i class="ri-money-dollar-circle-line"></i> ${formatPriceRange(s.price_min, s.price_max)}
            </div>
            <div class="card-info-row">
              <i class="ri-timer-line"></i> ${s.delivery_time ? s.delivery_time + ' Hari' : '-'}
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="action-btns">${actionButtons}</div>
        </div>
      </div>
    `;
  }).join('');
}

// MODAL
function openServiceModal(id) {
  const s = servicesData.find(x => String(x.id) === String(id));
  if (!s) return;

  const overlay = document.getElementById('detail-modal-overlay');
  const box = document.getElementById('detail-modal-box');

  const fName = s.freelancer?.skomda_student?.name ?? s.freelancer?.name ?? 'Freelancer';
  const catName = s.service_category?.name ?? s.category?.name ?? 'Kategori';
  const rawStatus = String(s.status || 'Draft').toLowerCase();

  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closeServiceModal()"><i class="ri-close-line"></i></button>
    </div>
    <div class="modal-body">
      <span class="service-id" style="margin-bottom:8px; display:inline-block;">#${s.id}</span>
      <h2 class="modal-name">${s.title}</h2>
      
      <div class="modal-role-row">
        <span class="status-pill status-${rawStatus}">${s.status}</span>
        <span class="cat-badge">${catName}</span>
      </div>

      <div class="modal-info-grid">
        <div class="modal-info-card">
          <div class="modal-info-label">Freelancer</div>
          <div class="modal-info-value"><i class="ri-user-line"></i> ${fName}</div>
        </div>
        <div class="modal-info-card">
          <div class="modal-info-label">Waktu Pengerjaan</div>
          <div class="modal-info-value"><i class="ri-timer-line"></i> ${s.delivery_time ? s.delivery_time + ' Hari' : '-'}</div>
        </div>
        <div class="modal-info-card">
          <div class="modal-info-label">Harga Minimum</div>
          <div class="modal-info-value">${formatRupiah(s.price_min)}</div>
        </div>
        <div class="modal-info-card">
          <div class="modal-info-label">Harga Maksimum</div>
          <div class="modal-info-value">${s.price_max ? formatRupiah(s.price_max) : '-'}</div>
        </div>
      </div>

      <p class="modal-section-title">Deskripsi Layanan</p>
      <div class="desc-box">${s.description || '-'}</div>
    </div>
  `;
  overlay.classList.add('open');
}

function closeServiceModal() {
  document.getElementById('detail-modal-overlay').classList.remove('open');
}

// FILTERS
function initFilters() {
  const tabs = document.querySelectorAll('.filter-tab');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      applyFilterAndSearch();
    });
  });
}

// API
const META_CSRF = document.querySelector('meta[name="csrf-token"]');
const CSRF_TOKEN = META_CSRF ? META_CSRF.getAttribute('content') : '';
const BASE_URL = window.location.origin;

async function approveService(id) {
  if (!confirm('Yakin ingin menyetujui layanan ini?')) return;

  try {
    const response = await fetch(`${BASE_URL}/admin/services/${id}/status`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ status: 'Approved' })
    });

    const result = await response.json();
    
    if (result.success) {
      const s = servicesData.find(x => String(x.id) === String(id));
      if (s) {
        s.status = 'Approved';
        s.reject_reason = null;
        applyFilterAndSearch();
        renderStats();
      }
      alert('Layanan berhasil disetujui!');
    } else {
      alert('Gagal menyetujui layanan.');
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Terjadi kesalahan jaringan.');
  }
}

function openRejectModal(id) {
  const s = servicesData.find(x => String(x.id) === String(id));
  if (!s) return;

  const existing = document.getElementById('reject-service-overlay');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.className = 'modal-overlay';
  el.id = 'reject-service-overlay';

  el.innerHTML = `
    <div class="modal-content" style="max-width: 450px;">
      <div class="modal-header">
        <h2>Tolak Layanan</h2>
        <button type="button" class="close-modal" id="btn-close-reject"><i class="ri-close-line"></i></button>
      </div>
      <form id="form-reject-service">
        <div class="form-group">
          <label>Alasan Penolakan untuk #${s.id}</label>
          <textarea id="reject-reason-input" rows="4" required placeholder="Tuliskan mengapa layanan ini ditolak..."></textarea>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" id="btn-cancel-reject">Batal</button>
          <button type="submit" class="btn-reject-confirm"><i class="ri-close-circle-line"></i> Tolak Layanan</button>
        </div>
      </form>
    </div>
  `;
  
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));

  const closeFn = () => { el.classList.remove('open'); setTimeout(() => el.remove(), 280); };
  el.querySelector('#btn-close-reject').addEventListener('click', closeFn);
  el.querySelector('#btn-cancel-reject').addEventListener('click', closeFn);
  
  el.querySelector('#form-reject-service').addEventListener('submit', async (e) => {
    e.preventDefault();
    const reason = el.querySelector('#reject-reason-input').value;
    
    try {
      const response = await fetch(`${BASE_URL}/admin/services/${id}/status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ status: 'Rejected', reject_reason: reason })
      });

      const result = await response.json();
      
      if (result.success) {
        s.status = 'Rejected';
        s.reject_reason = reason;
        closeFn();
        closeServiceModal();
        applyFilterAndSearch();
        renderStats();
        alert('Layanan berhasil ditolak.');
      } else {
        alert('Gagal menolak layanan.');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Terjadi kesalahan jaringan.');
    }
  });
}

// SEARCH
function initSearch() {
  const input = document.getElementById('service-search-input');
  if (input) input.addEventListener('input', applyFilterAndSearch);
}

function applyFilterAndSearch() {
  const activeTab = document.querySelector('.filter-tab.active');
  const filter = activeTab ? activeTab.dataset.filter.toLowerCase() : 'all';
  const q = (document.getElementById('service-search-input')?.value || '').toLowerCase();

  let filtered = [...servicesData];
  
  if (filter !== 'all') {
    filtered = filtered.filter(s => String(s.status).toLowerCase() === filter);
  }
  
  if (q) {
    filtered = filtered.filter(s => {
      const fName = String(s.freelancer?.user?.name ?? s.freelancer?.name ?? '').toLowerCase();
      const catName = String(s.category?.name ?? '').toLowerCase();
      const title = String(s.title ?? '').toLowerCase();
      
      return title.includes(q) || catName.includes(q) || fName.includes(q);
    });
  }
  renderCards(filtered);
}

// INIT
function initPage() {
  renderStats();
  renderCards();
  initFilters();
  initSearch();

  const overlay = document.getElementById('detail-modal-overlay');
  if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) closeServiceModal(); });
}

document.addEventListener('DOMContentLoaded', initPage);
