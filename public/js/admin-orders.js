let hasUnreadMessages = true;

let ordersData = [
  {
    id: 'ORD-001',
    client_id: 'C-101',
    client_name: 'Budi Santoso',
    freelancer_id: 'F-201',
    freelancer_name: 'Reva Anindya',
    service_id: 'SRV-005',
    service_name: 'UI/UX Design',
    brief: 'Membuat desain aplikasi mobile untuk e-commerce sayuran organik.',
    status: 'in_progress',
    agreed_price: 3500000,
    created_at: '2026-02-20'
  },
  {
    id: 'ORD-002',
    client_id: 'C-102',
    client_name: 'PT Maju Bersama',
    freelancer_id: 'F-202',
    freelancer_name: 'Dimas Prasetyo',
    service_id: 'SRV-012',
    service_name: 'Video Editing',
    brief: 'Editing video company profile durasi 5 menit.',
    status: 'pending',
    agreed_price: 1500000,
    created_at: '2026-02-25'
  },
  {
    id: 'ORD-003',
    client_id: 'C-103',
    client_name: 'Ayu Lestari',
    freelancer_id: 'F-203',
    freelancer_name: 'Sari Wulandari',
    service_id: 'SRV-008',
    service_name: 'Social Media Management',
    brief: 'Mengelola konten instagram selama 1 bulan penuh.',
    status: 'completed',
    agreed_price: 2000000,
    created_at: '2026-01-10'
  }
];

const STATUS_OPTIONS = ['pending', 'negotiated', 'paid', 'in_progress', 'revision', 'completed', 'cancelled'];

function formatRupiah(number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
}

function renderStats() {
  const row = document.getElementById('stats-row');
  if (!row) return;
  const total = ordersData.length;
  const inProgress = ordersData.filter(o => o.status === 'in_progress').length;
  const completed = ordersData.filter(o => o.status === 'completed').length;
  const pending = ordersData.filter(o => o.status === 'pending').length;

  row.innerHTML = `
    <div class="stat-card">
      <div class="stat-icon blue"><i class="ri-file-list-3-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${total}</span>
        <span class="stat-label">Total Orders</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon teal"><i class="ri-loader-4-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${inProgress}</span>
        <span class="stat-label">In Progress</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon purple"><i class="ri-check-double-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${completed}</span>
        <span class="stat-label">Completed</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon amber"><i class="ri-time-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${pending}</span>
        <span class="stat-label">Pending</span>
      </div>
    </div>
  `;
}

function renderTable(data = ordersData) {
  const tbody = document.getElementById('order-tbody');
  const emptyEl = document.getElementById('order-empty');
  const tableEl = document.getElementById('order-table');
  if (!tbody) return;

  if (!data || data.length === 0) {
    tableEl.style.display = 'none';
    emptyEl.style.display = 'block';
    return;
  }
  tableEl.style.display = 'table';
  emptyEl.style.display = 'none';

  tbody.innerHTML = data.map(o => `
    <tr>
      <td><span class="order-id-badge">${o.id}</span></td>
      <td>
        <div class="user-pair">
          <span class="user-name"><i class="ri-user-line"></i> ${o.client_name} <span class="user-label">(${o.client_id})</span></span>
          <span class="user-name"><i class="ri-briefcase-line"></i> ${o.freelancer_name} <span class="user-label">(${o.freelancer_id})</span></span>
        </div>
      </td>
      <td>
        <div class="user-pair">
          <span class="user-name">${o.service_name}</span>
          <span class="user-label">${o.service_id}</span>
        </div>
      </td>
      <td><span class="price-text">${formatRupiah(o.agreed_price)}</span></td>
      <td><span class="status-pill status-${o.status}">${o.status.replace('_', ' ')}</span></td>
      <td>${o.created_at}</td>
      <td>
        <div class="action-btns">
          <button class="btn-action" title="Detail" onclick="openOrderModal('${o.id}')"><i class="ri-eye-line"></i></button>
          <button class="btn-action" title="Edit" onclick="openEditOrderModal('${o.id}')"><i class="ri-edit-line"></i></button>
          <button class="btn-action danger" title="Hapus" onclick="deleteOrder('${o.id}')"><i class="ri-delete-bin-line"></i></button>
        </div>
      </td>
    </tr>
  `).join('');
}

function openOrderModal(id) {
  const o = ordersData.find(x => x.id === id);
  if (!o) return;
  const overlay = document.getElementById('detail-modal-overlay');
  const box = document.getElementById('detail-modal-box');

  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closeOrderModal()"><i class="ri-close-line"></i></button>
    </div>
    <div class="modal-body">
      <h2 class="modal-name">Order ${o.id}</h2>
      <div class="modal-role-row">
        <span class="status-pill status-${o.status}">${o.status.replace('_', ' ')}</span>
      </div>
      
      <div class="modal-info-list">
        <div class="modal-info-row">
          <i class="ri-user-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Client</span>${o.client_name} (${o.client_id})</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-briefcase-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Freelancer</span>${o.freelancer_name} (${o.freelancer_id})</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-tools-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Service</span>${o.service_name} (${o.service_id})</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-money-dollar-circle-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Agreed Price</span>${formatRupiah(o.agreed_price)}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-calendar-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Created At</span>${o.created_at}</div>
        </div>
      </div>

      <p class="modal-section-title">Brief</p>
      <div class="brief-box">${o.brief}</div>

      <div class="modal-action-group">
        <button class="btn-primary" style="flex:1" onclick="openEditOrderModal('${o.id}'); closeOrderModal();"><i class="ri-edit-line"></i> Edit Order</button>
        <button class="modal-btn-delete" onclick="deleteOrder('${o.id}'); closeOrderModal();"><i class="ri-delete-bin-line"></i> Hapus</button>
      </div>
    </div>
  `;
  overlay.classList.add('open');
}

function closeOrderModal() {
  document.getElementById('detail-modal-overlay').classList.remove('open');
}

function openAddOrderModal() {
  const existing = document.getElementById('add-order-overlay');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.className = 'modal-overlay';
  el.id = 'add-order-overlay';
  
  const statusOptionsHtml = STATUS_OPTIONS.map(s => `<option value="${s}">${s.replace('_', ' ')}</option>`).join('');

  el.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">
        <h2>Tambah Order</h2>
        <button class="close-modal" id="btn-close-add"><i class="ri-close-line"></i></button>
      </div>
      <form id="form-add-order">
        <div class="form-row">
          <div class="form-group"><label>Client ID</label><input type="text" id="add-client-id" required /></div>
          <div class="form-group"><label>Client Name</label><input type="text" id="add-client-name" required /></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Freelancer ID</label><input type="text" id="add-free-id" required /></div>
          <div class="form-group"><label>Freelancer Name</label><input type="text" id="add-free-name" required /></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Service ID</label><input type="text" id="add-srv-id" required /></div>
          <div class="form-group"><label>Service Name</label><input type="text" id="add-srv-name" required /></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Agreed Price</label><input type="number" id="add-price" required /></div>
          <div class="form-group"><label>Status</label><select id="add-status">${statusOptionsHtml}</select></div>
        </div>
        <div class="form-group">
          <label>Brief</label>
          <textarea id="add-brief" rows="3" required></textarea>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" id="btn-cancel-add">Batal</button>
          <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Simpan</button>
        </div>
      </form>
    </div>
  `;
  
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));

  const closeFn = () => { el.classList.remove('open'); setTimeout(() => el.remove(), 280); };
  el.querySelector('#btn-close-add').addEventListener('click', closeFn);
  el.querySelector('#btn-cancel-add').addEventListener('click', closeFn);
  
  el.querySelector('#form-add-order').addEventListener('submit', (e) => {
    e.preventDefault();
    const newOrder = {
      id: 'ORD-' + Math.floor(Math.random() * 1000).toString().padStart(3, '0'),
      client_id: el.querySelector('#add-client-id').value,
      client_name: el.querySelector('#add-client-name').value,
      freelancer_id: el.querySelector('#add-free-id').value,
      freelancer_name: el.querySelector('#add-free-name').value,
      service_id: el.querySelector('#add-srv-id').value,
      service_name: el.querySelector('#add-srv-name').value,
      brief: el.querySelector('#add-brief').value,
      status: el.querySelector('#add-status').value,
      agreed_price: parseInt(el.querySelector('#add-price').value),
      created_at: new Date().toISOString().split('T')[0]
    };
    ordersData.unshift(newOrder);
    closeFn();
    refreshUI();
  });
}

function openEditOrderModal(id) {
  const o = ordersData.find(x => x.id === id);
  if (!o) return;

  const existing = document.getElementById('edit-order-overlay');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.className = 'modal-overlay';
  el.id = 'edit-order-overlay';

  const statusOptionsHtml = STATUS_OPTIONS.map(s => `<option value="${s}" ${o.status === s ? 'selected' : ''}>${s.replace('_', ' ')}</option>`).join('');

  el.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">
        <h2>Edit Order ${o.id}</h2>
        <button class="close-modal" id="btn-close-edit"><i class="ri-close-line"></i></button>
      </div>
      <form id="form-edit-order">
        <div class="form-row">
          <div class="form-group"><label>Client ID</label><input type="text" id="edit-client-id" value="${o.client_id}" required /></div>
          <div class="form-group"><label>Client Name</label><input type="text" id="edit-client-name" value="${o.client_name}" required /></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Freelancer ID</label><input type="text" id="edit-free-id" value="${o.freelancer_id}" required /></div>
          <div class="form-group"><label>Freelancer Name</label><input type="text" id="edit-free-name" value="${o.freelancer_name}" required /></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Service ID</label><input type="text" id="edit-srv-id" value="${o.service_id}" required /></div>
          <div class="form-group"><label>Service Name</label><input type="text" id="edit-srv-name" value="${o.service_name}" required /></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Agreed Price</label><input type="number" id="edit-price" value="${o.agreed_price}" required /></div>
          <div class="form-group"><label>Status</label><select id="edit-status">${statusOptionsHtml}</select></div>
        </div>
        <div class="form-group">
          <label>Brief</label>
          <textarea id="edit-brief" rows="3" required>${o.brief}</textarea>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" id="btn-cancel-edit">Batal</button>
          <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Update</button>
        </div>
      </form>
    </div>
  `;
  
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));

  const closeFn = () => { el.classList.remove('open'); setTimeout(() => el.remove(), 280); };
  el.querySelector('#btn-close-edit').addEventListener('click', closeFn);
  el.querySelector('#btn-cancel-edit').addEventListener('click', closeFn);

  el.querySelector('#form-edit-order').addEventListener('submit', (e) => {
    e.preventDefault();
    o.client_id = el.querySelector('#edit-client-id').value;
    o.client_name = el.querySelector('#edit-client-name').value;
    o.freelancer_id = el.querySelector('#edit-free-id').value;
    o.freelancer_name = el.querySelector('#edit-free-name').value;
    o.service_id = el.querySelector('#edit-srv-id').value;
    o.service_name = el.querySelector('#edit-srv-name').value;
    o.agreed_price = parseInt(el.querySelector('#edit-price').value);
    o.status = el.querySelector('#edit-status').value;
    o.brief = el.querySelector('#edit-brief').value;
    
    closeFn();
    refreshUI();
  });
}

function deleteOrder(id) {
  if (confirm(`Yakin ingin menghapus order ${id}?`)) {
    ordersData = ordersData.filter(o => o.id !== id);
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
  const input = document.getElementById('order-search-input');
  if (input) input.addEventListener('input', applyFilterAndSearch);
}

function applyFilterAndSearch() {
  const activeTab = document.querySelector('.filter-tab.active');
  const filter = activeTab ? activeTab.dataset.filter : 'all';
  const q = (document.getElementById('order-search-input')?.value || '').toLowerCase();

  let filtered = ordersData;
  if (filter !== 'all') {
    filtered = ordersData.filter(o => o.status === filter);
  }
  if (q) {
    filtered = filtered.filter(o => 
      o.id.toLowerCase().includes(q) || 
      o.client_name.toLowerCase().includes(q) || 
      o.freelancer_name.toLowerCase().includes(q)
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

  const btnAdd = document.getElementById('btn-add-order');
  if (btnAdd) btnAdd.addEventListener('click', openAddOrderModal);

  const overlay = document.getElementById('detail-modal-overlay');
  if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) closeOrderModal(); });

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