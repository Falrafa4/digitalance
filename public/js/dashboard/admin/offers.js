// OFFERS.JS - WARAS VERSION (OFFERS & NEGOTIATIONS)
let hasUnreadMessages = true;

const rawOffers = window.__OFFERS_PAGE__?.offersData;
let offersData = Array.isArray(rawOffers) ? rawOffers : (rawOffers?.data || []);

const rawNego = window.__OFFERS_PAGE__?.negoData;
let negoData = Array.isArray(rawNego) ? rawNego : (rawNego?.data || []);

// HELPER FUNCTIONS
function formatRupiah(number) {
  if (!number) return 'Rp 0';
  return new Intl.NumberFormat('id-ID', { 
    style: 'currency', 
    currency: 'IDR', 
    minimumFractionDigits: 0 
  }).format(number);
}

// RENDER STATS
function renderStats() {
  const row = document.getElementById('stats-row');
  if (!row) return;

  const totalOffers = offersData.length;
  const pendingOffers = offersData.filter(o => 
    String(o.status).toLowerCase() === 'sent' || 
    String(o.status).toLowerCase() === 'pending'
  ).length;
  const totalNego = negoData.length;

  row.innerHTML = `
  <div class="stat-card">
    <div class="stat-icon blue"><i class="ri-price-tag-3-line"></i></div>
    <div class="stat-text">
    <span class="stat-value">${totalOffers}</span>
    <span class="stat-label">Total Tawaran</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber"><i class="ri-time-line"></i></div>
    <div class="stat-text">
    <span class="stat-value">${pendingOffers}</span>
    <span class="stat-label">Tawaran Pending</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon teal"><i class="ri-discuss-line"></i></div>
    <div class="stat-text">
    <span class="stat-value">${totalNego}</span>
    <span class="stat-label">Total Pesan Nego</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon purple"><i class="ri-message-3-line"></i></div>
    <div class="stat-text">
    <span class="stat-value">${totalNego > 0 ? 'Active' : 'Empty'}</span>
    <span class="stat-label">Status Negosiasi</span>
    </div>
  </div>
  `;
}

// RENDER OFFERS TABLE
function renderOffersTable(data = offersData) {
  const tbody = document.getElementById('offers-tbody');
  const emptyEl = document.getElementById('offers-empty');
  const tableEl = document.getElementById('offers-table');
  if (!tbody) return;

  if (!data || data.length === 0) {
    tableEl.style.display = 'none';
    emptyEl.style.display = 'block';
    return;
  }
  tableEl.style.display = 'table';
  emptyEl.style.display = 'none';

  tbody.innerHTML = data.map(o => {
    const clientName = o.order?.client?.skomda_student?.name ?? o.order?.client?.name ?? 'Client';
    const freeName = o.order?.service?.freelancer?.skomda_student?.name ?? 'Freelancer';
    const rawStatus = String(o.status || 'Sent').toLowerCase();

    return `
  <tr>
    <td><span class="id-badge">#${o.id}</span></td>
    <td>
    <div class="user-pair">
      <span class="user-name" title="Klien"><i class="ri-user-line"></i> ${clientName}</span>
      <span class="user-name" title="Freelancer"><i class="ri-briefcase-line"></i> ${freeName}</span>
    </div>
    </td>
    <td>
    <div class="offer-detail-cell">
      <span class="offer-title">${o.title}</span>
      <span class="offer-desc" title="${o.description ?? '-'}">${(o.description ?? '-').slice(0, 45)}...</span>
    </div>
    </td>
    <td>
    <div class="price-time-cell">
      <span class="price-val">${formatRupiah(o.offered_price)}</span>
      <span class="time-val"><i class="ri-calendar-line"></i> ${o.deadline ?? '-'}</span>
    </div>
    </td>
    <td><span class="status-pill status-${rawStatus}">${o.status}</span></td>
    <td>
    <div class="action-btns">
      <button class="btn-action" title="Detail" onclick="openOfferModal('${o.id}')"><i class="ri-eye-line"></i></button>
    </div>
    </td>
  </tr>
  `}).join('');
}

// RENDER NEGO TABLE
function renderNegoTable(data = negoData) {
  const tbody = document.getElementById('nego-tbody');
  const emptyEl = document.getElementById('nego-empty');
  const tableEl = document.getElementById('nego-table');
  if (!tbody) return;

  if (!data || data.length === 0) {
    tableEl.style.display = 'none';
    emptyEl.style.display = 'block';
    return;
  }
  tableEl.style.display = 'table';
  emptyEl.style.display = 'none';

  tbody.innerHTML = data.map(n => {
    const isClient = String(n.sender).toLowerCase() === 'client';
    const senderName = isClient 
      ? (n.order?.client?.skomda_student?.name ?? 'Client') 
      : (n.order?.service?.freelancer?.skomda_student?.name ?? 'Freelancer');
      
    const date = n.created_at ? new Date(n.created_at).toLocaleString('id-ID', {
      day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
    }) : '-';

    return `
  <tr>
    <td><span class="id-badge">#${n.id}</span></td>
    <td><span class="ref-badge"><i class="ri-file-list-3-line"></i> #${n.order_id}</span></td>
    <td>
    <div class="user-pair">
      <span class="user-name">${senderName}</span>
      <span style="font-size:10px; color:var(--slate-400); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
       ${n.sender}
      </span>
    </div>
    </td>
    <td><div class="msg-cell" title="${n.message}">${n.message.slice(0, 55)}...</div></td>
    <td style="font-size:12px; color:var(--slate-500); white-space:nowrap;">${date}</td>
    <td>
    <div class="action-btns">
      <button class="btn-action" title="Baca Pesan" onclick="openNegoModal('${n.id}')"><i class="ri-eye-line"></i></button>
    </div>
    </td>
  </tr>
  `}).join('');
}

// MODALS
function openOfferModal(id) {
  const o = offersData.find(x => String(x.id) === String(id));
  if (!o) return;
  const overlay = document.getElementById('detail-modal-overlay');
  const box = document.getElementById('detail-modal-box');

  box.innerHTML = `
  <div class="modal-hero">
    <button class="modal-close" onclick="closeModal()"><i class="ri-close-line"></i></button>
  </div>
  <div class="modal-body">
    <h2 class="modal-name">Detail Tawaran #${o.id}</h2>
    <div class="modal-info-list">
    <div class="modal-info-row">
      <i class="ri-money-dollar-circle-line"></i>
      <div class="modal-info-text"><strong>Harga</strong>${formatRupiah(o.offered_price)}</div>
    </div>
    <div class="modal-info-row">
      <i class="ri-calendar-event-line"></i>
      <div class="modal-info-text"><strong>Deadline</strong>${o.deadline ?? '-'}</div>
    </div>
    <div class="modal-info-row">
      <i class="ri-checkbox-circle-line"></i>
      <div class="modal-info-text"><strong>Status</strong><span class="status-pill status-${String(o.status).toLowerCase()}">${o.status}</span></div>
    </div>
    </div>
    <p class="modal-section-title">Isi Tawaran</p>
    <div class="msg-box"><strong>${o.title}</strong><br>${o.description ?? '-'}</div>
  </div>`;
  overlay.classList.add('open');
}

function openNegoModal(id) {
  const n = negoData.find(x => String(x.id) === String(id));
  if (!n) return;
  const overlay = document.getElementById('detail-modal-overlay');
  const box = document.getElementById('detail-modal-box');

  box.innerHTML = `
  <div class="modal-hero">
    <button class="modal-close" onclick="closeModal()"><i class="ri-close-line"></i></button>
  </div>
  <div class="modal-body">
    <h2 class="modal-name">Log Negosiasi #${n.id}</h2>
    <div class="modal-info-list">
    <div class="modal-info-row"><i class="ri-file-list-3-line"></i><div><strong>Order</strong>#${n.order_id}</div></div>
    <div class="modal-info-row"><i class="ri-user-smile-line"></i><div><strong>Pengirim</strong>${n.sender}</div></div>
    </div>
    <p class="modal-section-title">Pesan</p>
    <div class="msg-box">${n.message}</div>
  </div>`;
  overlay.classList.add('open');
}

function closeModal() {
  document.getElementById('detail-modal-overlay').classList.remove('open');
}

// TABS & SEARCH
function initTabs() {
  const tabs = document.querySelectorAll('.section-tab');
  const contents = document.querySelectorAll('.tab-content');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));
      tab.classList.add('active');
      document.getElementById(tab.dataset.target).classList.add('active');
      applySearchAndFilter();
    });
  });
}

function initFilters() {
  const tabs = document.querySelectorAll('#offers-filters .filter-tab');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      applySearchAndFilter();
    });
  });
}

function initSearch() {
  const input = document.getElementById('global-search-input');
  if (input) input.addEventListener('input', applySearchAndFilter);
}

function applySearchAndFilter() {
  const activeSection = document.querySelector('.section-tab.active').dataset.target;
  const q = (document.getElementById('global-search-input')?.value || '').toLowerCase();

  if (activeSection === 'offers-section') {
    const filter = document.querySelector('#offers-filters .filter-tab.active')?.dataset.filter.toLowerCase() || 'all';
    let filtered = offersData.filter(o => {
      const matchesFilter = filter === 'all' || String(o.status).toLowerCase() === filter;
      const matchesSearch = !q || o.title.toLowerCase().includes(q) || String(o.id).includes(q);
      return matchesFilter && matchesSearch;
    });
    renderOffersTable(filtered);
  } else {
    let filtered = negoData.filter(n => !q || n.message.toLowerCase().includes(q) || String(n.order_id).includes(q));
    renderNegoTable(filtered);
  }
}

// PAGE BOOTSTRAP
function initPage() {
  initTabs();
  initFilters();
  initSearch();
  renderStats();
  renderOffersTable();
  renderNegoTable();

  const overlay = document.getElementById('detail-modal-overlay');
  if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });
}

document.addEventListener('DOMContentLoaded', initPage);
