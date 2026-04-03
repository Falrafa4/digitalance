let hasUnreadMessages = true;

// TANGKAP DATA DARI BLADE
const rawTrx = window.__TRANSACTIONS_PAGE__?.data;
let trxData = Array.isArray(rawTrx) ? rawTrx : (rawTrx?.data || []);

function formatRupiah(number) {
  if (!number) return 'Rp 0';
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
}

// RENDER STATS
function renderStats() {
  const row = document.getElementById('stats-row');
  if (!row) return;

  const total = trxData.length;
  const totalRevenue = trxData
    .filter(t => String(t.status).toLowerCase() === 'paid' && String(t.type).toLowerCase() !== 'refund')
    .reduce((sum, t) => sum + (Number(t.amount) || 0), 0);
  const pendingCount = trxData.filter(t => String(t.status).toLowerCase() === 'pending').length;
  const totalRefund = trxData
    .filter(t => String(t.type).toLowerCase() === 'refund' && String(t.status).toLowerCase() === 'paid')
    .reduce((sum, t) => sum + (Number(t.amount) || 0), 0);

  row.innerHTML = `
    <div class="stat-card">
      <div class="stat-icon blue"><i class="ri-bank-card-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${total}</span>
        <span class="stat-label">Total Transaksi</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon teal"><i class="ri-wallet-3-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${formatRupiah(totalRevenue).replace(',00', '')}</span>
        <span class="stat-label">Pemasukan (Paid)</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon amber"><i class="ri-time-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${pendingCount}</span>
        <span class="stat-label">Transaksi Pending</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon red"><i class="ri-refund-2-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${formatRupiah(totalRefund).replace(',00', '')}</span>
        <span class="stat-label">Total Refund (Paid)</span>
      </div>
    </div>
  `;
}

// RENDER TABLE
function renderTable(data = trxData) {
  const tbody = document.getElementById('trx-tbody');
  const emptyEl = document.getElementById('trx-empty');
  const tableEl = document.getElementById('trx-table');
  if (!tbody) return;

  if (!data || data.length === 0) {
    tableEl.style.display = 'none';
    emptyEl.style.display = 'block';
    return;
  }
  tableEl.style.display = 'table';
  emptyEl.style.display = 'none';

  tbody.innerHTML = data.map(t => {
    const rawStatus = String(t.status || 'pending').toLowerCase();
    const rawType = String(t.type || 'full').toLowerCase();
    const date = t.created_at ? new Date(t.created_at).toLocaleDateString('id-ID') : '-';

    return `
    <tr>
      <td><span class="id-badge">#${t.id}</span></td>
      <td><span class="ref-badge">#${t.order_id ?? '-'}</span></td>
      <td><span class="price-text">${formatRupiah(t.amount || 0)}</span></td>
      <td><span class="type-pill type-${rawType}">${t.type || '-'}</span></td>
      <td><span class="status-pill status-${rawStatus}">${t.status || '-'}</span></td>
      <td><span class="date-text">${date}</span></td>
      <td>
        <div class="action-btns">
          <button class="btn-action" title="Detail" onclick="openTrxModal('${t.id}')"><i class="ri-eye-line"></i></button>
        </div>
      </td>
    </tr>
  `}).join('');
}

// OPEN TRX MODAL
function openTrxModal(id) {
  const t = trxData.find(x => String(x.id) === String(id));
  if (!t) return;

  const overlay = document.getElementById('detail-modal-overlay');
  const box = document.getElementById('detail-modal-box');

  const rawStatus = String(t.status || 'pending').toLowerCase();
  const rawType = String(t.type || 'full').toLowerCase();
  const date = t.created_at ? new Date(t.created_at).toLocaleString('id-ID') : '-';

  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closeTrxModal()"><i class="ri-close-line"></i></button>
    </div>
    <div class="modal-body">
      <h2 class="modal-name">Detail Transaksi</h2>
      
      <div class="modal-info-list">
        <div class="modal-info-row">
          <i class="ri-hashtag"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">ID Transaksi</span>#${t.id}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-file-list-3-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Order ID</span>#${t.order_id ?? '-'}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-money-dollar-circle-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Nominal</span>${formatRupiah(t.amount || 0)}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-exchange-dollar-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Tipe Pembayaran</span><span class="type-pill type-${rawType}" style="margin-top:4px;">${t.type || '-'}</span></div>
        </div>
        <div class="modal-info-row">
          <i class="ri-checkbox-circle-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Status</span><span class="status-pill status-${rawStatus}" style="margin-top:4px;">${t.status || '-'}</span></div>
        </div>
        <div class="modal-info-row">
          <i class="ri-calendar-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Waktu Dibuat</span>${date}</div>
        </div>
      </div>
    </div>
  `;
  overlay.classList.add('open');
}

// CLOSE TRX MODAL
function closeTrxModal() {
  document.getElementById('detail-modal-overlay').classList.remove('open');
}

// INIT FILTERS
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

// INIT SEARCH
function initSearch() {
  const input = document.getElementById('trx-search-input');
  if (input) input.addEventListener('input', applyFilterAndSearch);
}

// APPLY FILTER AND SEARCH
function applyFilterAndSearch() {
  const activeTab = document.querySelector('.filter-tab.active');
  const filter = activeTab ? activeTab.dataset.filter.toLowerCase() : 'all';
  const q = (document.getElementById('trx-search-input')?.value || '').toLowerCase();

  let filtered = [...trxData];
  
  if (filter !== 'all') {
    filtered = filtered.filter(t => String(t.status).toLowerCase() === filter);
  }

  if (q) {
    filtered = filtered.filter(t => 
      String(t.id).toLowerCase().includes(q) || 
      String(t.order_id).toLowerCase().includes(q) ||
      String(t.type).toLowerCase().includes(q)
    );
  }
  
  renderTable(filtered);
}

// INIT PAGE
function initPage() {
  renderStats();
  renderTable();
  initFilters();
  initSearch();

  const overlay = document.getElementById('detail-modal-overlay');
  if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) closeTrxModal(); });
}

document.addEventListener('DOMContentLoaded', initPage);