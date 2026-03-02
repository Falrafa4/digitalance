let hasUnreadMessages = true;

let trxData = [
  {
    id: 'TRX-001',
    order_id: 'ORD-001',
    amount: 3500000,
    type: 'full',
    status: 'paid',
    created_at: '2026-02-20'
  },
  {
    id: 'TRX-002',
    order_id: 'ORD-002',
    amount: 750000,
    type: 'dp',
    status: 'pending',
    created_at: '2026-02-25'
  },
  {
    id: 'TRX-003',
    order_id: 'ORD-005',
    amount: 2000000,
    type: 'refund',
    status: 'failed',
    created_at: '2026-02-26'
  },
  {
    id: 'TRX-004',
    order_id: 'ORD-008',
    amount: 500000,
    type: 'dp',
    status: 'paid',
    created_at: '2026-02-27'
  }
];

function formatRupiah(number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
}

function renderStats() {
  const row = document.getElementById('stats-row');
  if (!row) return;

  const total = trxData.length;
  const totalRevenue = trxData
    .filter(t => t.status === 'paid' && t.type !== 'refund')
    .reduce((sum, t) => sum + t.amount, 0);
  const pendingCount = trxData.filter(t => t.status === 'pending').length;
  const totalRefund = trxData
    .filter(t => t.type === 'refund' && t.status === 'paid')
    .reduce((sum, t) => sum + t.amount, 0);

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

  tbody.innerHTML = data.map(t => `
    <tr>
      <td><span class="id-badge">${t.id}</span></td>
      <td><span class="ref-badge">${t.order_id}</span></td>
      <td><span class="price-text">${formatRupiah(t.amount)}</span></td>
      <td><span class="type-pill type-${t.type}">${t.type}</span></td>
      <td><span class="status-pill status-${t.status}">${t.status}</span></td>
      <td><span class="date-text">${t.created_at}</span></td>
      <td>
        <div class="action-btns">
          <button class="btn-action" title="Detail" onclick="openTrxModal('${t.id}')"><i class="ri-eye-line"></i></button>
          <button class="btn-action" title="Edit" onclick="openEditTrxModal('${t.id}')"><i class="ri-edit-line"></i></button>
          <button class="btn-action danger" title="Hapus" onclick="deleteTrx('${t.id}')"><i class="ri-delete-bin-line"></i></button>
        </div>
      </td>
    </tr>
  `).join('');
}

function openTrxModal(id) {
  const t = trxData.find(x => x.id === id);
  if (!t) return;

  const overlay = document.getElementById('detail-modal-overlay');
  const box = document.getElementById('detail-modal-box');

  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closeTrxModal()"><i class="ri-close-line"></i></button>
    </div>
    <div class="modal-body">
      <h2 class="modal-name">Detail Transaksi</h2>
      
      <div class="modal-info-list">
        <div class="modal-info-row">
          <i class="ri-hashtag"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">ID Transaksi</span>${t.id}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-file-list-3-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Order ID</span>${t.order_id}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-money-dollar-circle-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Nominal</span>${formatRupiah(t.amount)}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-exchange-dollar-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Tipe Pembayaran</span><span class="type-pill type-${t.type}" style="margin-top:4px;">${t.type}</span></div>
        </div>
        <div class="modal-info-row">
          <i class="ri-checkbox-circle-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Status</span><span class="status-pill status-${t.status}" style="margin-top:4px;">${t.status}</span></div>
        </div>
        <div class="modal-info-row">
          <i class="ri-calendar-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Tanggal Dibuat</span>${t.created_at}</div>
        </div>
      </div>

      <div class="modal-action-group">
        <button class="btn-primary" style="flex:1" onclick="openEditTrxModal('${t.id}'); closeTrxModal();"><i class="ri-edit-line"></i> Edit Transaksi</button>
        <button class="modal-btn-delete" onclick="deleteTrx('${t.id}'); closeTrxModal();"><i class="ri-delete-bin-line"></i> Hapus</button>
      </div>
    </div>
  `;
  overlay.classList.add('open');
}

function closeTrxModal() {
  document.getElementById('detail-modal-overlay').classList.remove('open');
}

function openAddTrxModal() {
  const existing = document.getElementById('add-trx-overlay');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.className = 'modal-overlay';
  el.id = 'add-trx-overlay';

  el.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">
        <h2>Tambah Transaksi</h2>
        <button class="close-modal" id="btn-close-add"><i class="ri-close-line"></i></button>
      </div>
      <form id="form-add-trx">
        <div class="form-group">
          <label>Order ID</label>
          <input type="text" id="add-order-id" required placeholder="Contoh: ORD-001" />
        </div>
        <div class="form-group">
          <label>Nominal (Rp)</label>
          <input type="number" id="add-amount" required placeholder="500000" min="1000" />
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Tipe Pembayaran</label>
            <select id="add-type" required>
              <option value="dp">DP</option>
              <option value="full">Full</option>
              <option value="refund">Refund</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select id="add-status" required>
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
              <option value="failed">Failed</option>
            </select>
          </div>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" id="btn-cancel-add">Batal</button>
          <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Simpan Transaksi</button>
        </div>
      </form>
    </div>
  `;
  
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));

  const closeFn = () => { el.classList.remove('open'); setTimeout(() => el.remove(), 280); };
  el.querySelector('#btn-close-add').addEventListener('click', closeFn);
  el.querySelector('#btn-cancel-add').addEventListener('click', closeFn);
  
  el.querySelector('#form-add-trx').addEventListener('submit', (e) => {
    e.preventDefault();
    const newTrx = {
      id: 'TRX-' + Math.floor(Math.random() * 1000).toString().padStart(3, '0'),
      order_id: el.querySelector('#add-order-id').value,
      amount: parseInt(el.querySelector('#add-amount').value),
      type: el.querySelector('#add-type').value,
      status: el.querySelector('#add-status').value,
      created_at: new Date().toISOString().split('T')[0]
    };
    trxData.unshift(newTrx);
    closeFn();
    refreshUI();
  });
}

function openEditTrxModal(id) {
  const t = trxData.find(x => x.id === id);
  if (!t) return;

  const existing = document.getElementById('edit-trx-overlay');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.className = 'modal-overlay';
  el.id = 'edit-trx-overlay';

  el.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">
        <h2>Edit Transaksi ${t.id}</h2>
        <button class="close-modal" id="btn-close-edit"><i class="ri-close-line"></i></button>
      </div>
      <form id="form-edit-trx">
        <div class="form-group">
          <label>Order ID</label>
          <input type="text" id="edit-order-id" value="${t.order_id}" required />
        </div>
        <div class="form-group">
          <label>Nominal (Rp)</label>
          <input type="number" id="edit-amount" value="${t.amount}" required min="1000" />
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Tipe Pembayaran</label>
            <select id="edit-type" required>
              <option value="dp" ${t.type === 'dp' ? 'selected' : ''}>DP</option>
              <option value="full" ${t.type === 'full' ? 'selected' : ''}>Full</option>
              <option value="refund" ${t.type === 'refund' ? 'selected' : ''}>Refund</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select id="edit-status" required>
              <option value="pending" ${t.status === 'pending' ? 'selected' : ''}>Pending</option>
              <option value="paid" ${t.status === 'paid' ? 'selected' : ''}>Paid</option>
              <option value="failed" ${t.status === 'failed' ? 'selected' : ''}>Failed</option>
            </select>
          </div>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" id="btn-cancel-edit">Batal</button>
          <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Update Transaksi</button>
        </div>
      </form>
    </div>
  `;
  
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));

  const closeFn = () => { el.classList.remove('open'); setTimeout(() => el.remove(), 280); };
  el.querySelector('#btn-close-edit').addEventListener('click', closeFn);
  el.querySelector('#btn-cancel-edit').addEventListener('click', closeFn);

  el.querySelector('#form-edit-trx').addEventListener('submit', (e) => {
    e.preventDefault();
    t.order_id = el.querySelector('#edit-order-id').value;
    t.amount = parseInt(el.querySelector('#edit-amount').value);
    t.type = el.querySelector('#edit-type').value;
    t.status = el.querySelector('#edit-status').value;
    
    closeFn();
    refreshUI();
  });
}

function deleteTrx(id) {
  if (confirm(`Yakin ingin menghapus transaksi ${id}?`)) {
    trxData = trxData.filter(t => t.id !== id);
    refreshUI();
  }
}

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

function initSearch() {
  const input = document.getElementById('trx-search-input');
  if (input) input.addEventListener('input', applyFilterAndSearch);
}

function applyFilterAndSearch() {
  const activeTab = document.querySelector('.filter-tab.active');
  const filter = activeTab ? activeTab.dataset.filter : 'all';
  const q = (document.getElementById('trx-search-input')?.value || '').toLowerCase();

  let filtered = trxData;
  
  if (filter !== 'all') {
    if (filter === 'type-refund') {
      filtered = trxData.filter(t => t.type === 'refund');
    } else {
      filtered = trxData.filter(t => t.status === filter);
    }
  }

  if (q) {
    filtered = filtered.filter(t => 
      t.id.toLowerCase().includes(q) || 
      t.order_id.toLowerCase().includes(q)
    );
  }
  
  renderTable(filtered);
}

function refreshUI() {
  renderStats();
  applyFilterAndSearch();
}

function initPage() {
  renderStats();
  renderTable();
  initFilters();
  initSearch();

  const btnAdd = document.getElementById('btn-add-trx');
  if (btnAdd) btnAdd.addEventListener('click', openAddTrxModal);

  const overlay = document.getElementById('detail-modal-overlay');
  if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) closeTrxModal(); });

  const notifBtn = document.getElementById('notif-btn');
  if (notifBtn) {
    notifBtn.classList.toggle('has-unread', hasUnreadMessages);
    notifBtn.addEventListener('click', () => {
      hasUnreadMessages = false;
      notifBtn.classList.remove('has-unread');
    });
  }
}

document.addEventListener('DOMContentLoaded', initPage);