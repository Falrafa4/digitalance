let hasUnreadMessages = true;

const freelancers = [
  { id: 'F-201', name: 'Reva Anindya', email: 'reva@digitalance.id' },
  { id: 'F-205', name: 'Budi Santoso', email: 'budi@digitalance.id' },
  { id: 'F-210', name: 'Sari Wulandari', email: 'sari@digitalance.id' }
];

const serviceCategories = [
  { id: 'CAT-001', name: 'UI/UX Design', is_active: true },
  { id: 'CAT-002', name: 'Web Development', is_active: true },
  { id: 'CAT-003', name: 'Video Editing', is_active: true },
  { id: 'CAT-004', name: 'Copywriting', is_active: true },
  { id: 'CAT-005', name: 'SEO Optimization', is_active: true }
];

let servicesData = [
  {
    id: 'SRV-001',
    freelancer_id: 'F-201',
    freelancer_email: 'reva@digitalance.id',
    freelancer_name: 'Reva Anindya',
    category_id: 'CAT-001',
    title: 'Desain UI/UX Aplikasi Mobile (Figma)',
    description: 'Saya akan membuat desain UI/UX aplikasi mobile yang modern dan user-friendly menggunakan Figma. Termasuk prototyping dan wireframing.',
    price_min: 1500000,
    price_max: 5000000,
    delivery_min: 7,
    delivery_max: 14,
    status: 'approved',
    reject_reason: null,
    created_at: '2026-02-15'
  },
  {
    id: 'SRV-002',
    freelancer_id: 'F-205',
    freelancer_email: 'budi@digitalance.id',
    freelancer_name: 'Budi Santoso',
    category_id: 'CAT-002',
    title: 'Pembuatan Website Profile Perusahaan',
    description: 'Pembuatan website company profile menggunakan HTML, CSS, dan JS yang responsif di berbagai perangkat.',
    price_min: 2000000,
    price_max: null,
    delivery_min: 10,
    delivery_max: 10,
    status: 'pending',
    reject_reason: null,
    created_at: '2026-02-27'
  },
  {
    id: 'SRV-003',
    freelancer_id: 'F-210',
    freelancer_email: 'sari@digitalance.id',
    freelancer_name: 'Sari Wulandari',
    category_id: 'CAT-004',
    title: 'Jasa Penulisan Artikel Medis SEO',
    description: 'Saya menawarkan jasa penulisan artikel di bidang medis tanpa referensi yang jelas.',
    price_min: 50000,
    price_max: 150000,
    delivery_min: 1,
    delivery_max: 2,
    status: 'rejected',
    reject_reason: 'Deskripsi tidak sesuai dengan standar kredibilitas platform. Harap sertakan referensi medis yang valid untuk pembuatan artikel.',
    created_at: '2026-02-28'
  }
];

function getCategoryName(catId) {
  const cat = serviceCategories.find(c => c.id === catId);
  return cat ? cat.name : 'Unknown Category';
}

function formatRupiah(number) {
  if (!number) return '-';
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
}

function formatPriceRange(min, max) {
  if (max && max > min) return `${formatRupiah(min)} - ${formatRupiah(max)}`;
  return formatRupiah(min);
}

function formatDeliveryTime(min, max) {
  if (max && max > min) return `${min} - ${max} Hari`;
  return `${min} Hari`;
}

function renderStats() {
  const row = document.getElementById('stats-row');
  if (!row) return;

  const total = servicesData.length;
  const approved = servicesData.filter(s => s.status === 'approved').length;
  const pending = servicesData.filter(s => s.status === 'pending').length;
  const rejected = servicesData.filter(s => s.status === 'rejected').length;

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
      <div class="stat-icon red"><i class="ri-close-circle-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${rejected}</span>
        <span class="stat-label">Rejected</span>
      </div>
    </div>
  `;
}

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
    let actionButtons = `<button class="btn-action" title="Detail" onclick="openServiceModal('${s.id}')"><i class="ri-eye-line"></i></button>`;

    if (s.status === 'pending') {
      actionButtons += `
        <button class="btn-action success" title="Approve" onclick="approveService('${s.id}')"><i class="ri-check-line"></i></button>
        <button class="btn-action warn" title="Tolak" onclick="openRejectModal('${s.id}')"><i class="ri-close-line"></i></button>
      `;
    }

    actionButtons += `
      <button class="btn-action" title="Edit" onclick="openEditServiceModal('${s.id}')"><i class="ri-edit-line"></i></button>
      <button class="btn-action danger" title="Hapus" onclick="deleteService('${s.id}')"><i class="ri-delete-bin-line"></i></button>
    `;

    return `
      <div class="service-card">
        <div class="card-header">
          <span class="service-id">${s.id}</span>
          <span class="status-pill status-${s.status}">${s.status}</span>
        </div>
        <div class="card-body">
          <span class="cat-badge">${getCategoryName(s.category_id)}</span>
          <h3 class="service-title" title="${s.title}">${s.title}</h3>
          
          <div style="margin-top: 4px; display: flex; flex-direction: column; gap: 6px;">
            <div class="card-info-row">
              <i class="ri-user-line"></i> ${s.freelancer_name}
            </div>
            <div class="card-info-row">
              <i class="ri-money-dollar-circle-line"></i> ${formatPriceRange(s.price_min, s.price_max)}
            </div>
            <div class="card-info-row">
              <i class="ri-timer-line"></i> ${formatDeliveryTime(s.delivery_min, s.delivery_max)}
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

function openServiceModal(id) {
  const s = servicesData.find(x => x.id === id);
  if (!s) return;

  const overlay = document.getElementById('detail-modal-overlay');
  const box = document.getElementById('detail-modal-box');

  let actionHtml = `<button class="btn-primary" style="flex:1" onclick="openEditServiceModal('${s.id}'); closeServiceModal();"><i class="ri-edit-line"></i> Edit</button>`;
  
  if (s.status === 'pending') {
    actionHtml = `
      <button class="modal-btn-approve" onclick="approveService('${s.id}'); closeServiceModal();"><i class="ri-check-line"></i> Approve</button>
      <button class="modal-btn-reject" onclick="openRejectModal('${s.id}'); closeServiceModal();"><i class="ri-close-line"></i> Reject</button>
      ${actionHtml}
    `;
  }
  
  actionHtml += `<button class="modal-btn-delete" onclick="deleteService('${s.id}'); closeServiceModal();"><i class="ri-delete-bin-line"></i> Hapus</button>`;

  let rejectHtml = '';
  if (s.status === 'rejected' && s.reject_reason) {
    rejectHtml = `
      <div class="reject-reason-box">
        <strong>Alasan Penolakan:</strong>
        ${s.reject_reason}
      </div>
    `;
  }

  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closeServiceModal()"><i class="ri-close-line"></i></button>
    </div>
    <div class="modal-body">
      <span class="service-id" style="margin-bottom:8px; display:inline-block;">${s.id}</span>
      <h2 class="modal-name">${s.title}</h2>
      
      <div class="modal-role-row">
        <span class="status-pill status-${s.status}">${s.status}</span>
        <span class="cat-badge">${getCategoryName(s.category_id)}</span>
      </div>

      ${rejectHtml}

      <div class="modal-info-grid">
        <div class="modal-info-card">
          <div class="modal-info-label">Freelancer</div>
          <div class="modal-info-value"><i class="ri-user-line"></i> ${s.freelancer_name} (${s.freelancer_id})</div>
        </div>
        <div class="modal-info-card">
          <div class="modal-info-label">Waktu Pengerjaan</div>
          <div class="modal-info-value"><i class="ri-timer-line"></i> ${formatDeliveryTime(s.delivery_min, s.delivery_max)}</div>
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
      <div class="desc-box">${s.description}</div>

      <div class="modal-action-group">
        ${actionHtml}
      </div>
    </div>
  `;
  overlay.classList.add('open');
}

function closeServiceModal() {
  document.getElementById('detail-modal-overlay').classList.remove('open');
}

function approveService(id) {
  const s = servicesData.find(x => x.id === id);
  if (s) {
    s.status = 'approved';
    s.reject_reason = null;
    refreshUI();
  }
}

function openRejectModal(id) {
  const s = servicesData.find(x => x.id === id);
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
        <button class="close-modal" id="btn-close-reject"><i class="ri-close-line"></i></button>
      </div>
      <form id="form-reject-service">
        <div class="form-group">
          <label>Alasan Penolakan untuk ${s.id}</label>
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
  
  el.querySelector('#form-reject-service').addEventListener('submit', (e) => {
    e.preventDefault();
    s.status = 'rejected';
    s.reject_reason = el.querySelector('#reject-reason-input').value;
    closeFn();
    refreshUI();
  });
}

function deleteService(id) {
  if (confirm(`Yakin ingin menghapus layanan ${id}?`)) {
    servicesData = servicesData.filter(s => s.id !== id);
    refreshUI();
  }
}

function getCategoryOptions(selectedId = '') {
  return serviceCategories.map(c => 
    `<option value="${c.id}" ${c.id === selectedId ? 'selected' : ''}>${c.name}</option>`
  ).join('');
}

function bindFreelancerEmailLookup(el) {
  const emailInput = el.querySelector('.free-email-input');
  const infoDiv = el.querySelector('.free-info');
  const idInput = el.querySelector('.free-id-input');
  const nameInput = el.querySelector('.free-name-input');

  const checkEmail = (email) => {
    const f = freelancers.find(x => x.email === email);
    if (f) {
      infoDiv.innerHTML = `<span style="color:#10B981"><i class="ri-check-line"></i> Ditemukan: ${f.name} (${f.id})</span>`;
      idInput.value = f.id;
      nameInput.value = f.name;
    } else {
      infoDiv.innerHTML = `<span style="color:#ef4444"><i class="ri-error-warning-line"></i> Freelancer tidak ditemukan</span>`;
      idInput.value = '';
      nameInput.value = '';
    }
  };

  emailInput.addEventListener('input', (e) => checkEmail(e.target.value));
  if (emailInput.value) checkEmail(emailInput.value);
}

function openAddServiceModal() {
  const existing = document.getElementById('add-service-overlay');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.className = 'modal-overlay';
  el.id = 'add-service-overlay';

  el.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">
        <h2>Tambah Layanan</h2>
        <button class="close-modal" id="btn-close-add"><i class="ri-close-line"></i></button>
      </div>
      <form id="form-add-service">
        <div class="form-group">
          <label>Judul Layanan</label>
          <input type="text" id="add-title" required />
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label>Kategori</label>
            <select id="add-cat-id" required>
              <option value="" disabled selected>Pilih Kategori...</option>
              ${getCategoryOptions()}
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select id="add-status">
              <option value="draft">Draft</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Email Freelancer</label>
          <input type="email" id="add-free-email" class="free-email-input" required placeholder="Cari berdasarkan email..." />
          <div class="free-info" style="font-size:12px; margin-top:6px; font-weight:600;"></div>
          <input type="hidden" id="add-free-id" class="free-id-input" required />
          <input type="hidden" id="add-free-name" class="free-name-input" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Harga Min</label>
            <div class="input-group">
              <span class="input-prefix">Rp</span>
              <input type="number" id="add-price-min" required min="1000" />
            </div>
          </div>
          <div class="form-group">
            <label>Harga Max (Opsional)</label>
            <div class="input-group">
              <span class="input-prefix">Rp</span>
              <input type="number" id="add-price-max" min="1000" />
            </div>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group"><label>Estimasi Min (Hari)</label><input type="number" id="add-del-min" required min="1" /></div>
          <div class="form-group"><label>Estimasi Max (Hari)</label><input type="number" id="add-del-max" required min="1" /></div>
        </div>

        <div class="form-group">
          <label>Deskripsi</label>
          <textarea id="add-desc" rows="4" required></textarea>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-secondary" id="btn-cancel-add">Batal</button>
          <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Simpan Layanan</button>
        </div>
      </form>
    </div>
  `;
  
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));

  bindFreelancerEmailLookup(el);

  const closeFn = () => { el.classList.remove('open'); setTimeout(() => el.remove(), 280); };
  el.querySelector('#btn-close-add').addEventListener('click', closeFn);
  el.querySelector('#btn-cancel-add').addEventListener('click', closeFn);
  
  el.querySelector('#form-add-service').addEventListener('submit', (e) => {
    e.preventDefault();
    if (!el.querySelector('#add-free-id').value) {
      alert("Email Freelancer tidak valid atau tidak ditemukan.");
      return;
    }

    const pMax = el.querySelector('#add-price-max').value;
    const newService = {
      id: 'SRV-' + Math.floor(Math.random() * 1000).toString().padStart(3, '0'),
      title: el.querySelector('#add-title').value,
      category_id: el.querySelector('#add-cat-id').value,
      freelancer_id: el.querySelector('#add-free-id').value,
      freelancer_email: el.querySelector('#add-free-email').value,
      freelancer_name: el.querySelector('#add-free-name').value,
      price_min: parseInt(el.querySelector('#add-price-min').value),
      price_max: pMax ? parseInt(pMax) : null,
      delivery_min: parseInt(el.querySelector('#add-del-min').value),
      delivery_max: parseInt(el.querySelector('#add-del-max').value),
      status: el.querySelector('#add-status').value,
      reject_reason: null,
      description: el.querySelector('#add-desc').value,
      created_at: new Date().toISOString().split('T')[0]
    };
    servicesData.unshift(newService);
    closeFn();
    refreshUI();
  });
}

function openEditServiceModal(id) {
  const s = servicesData.find(x => x.id === id);
  if (!s) return;

  const existing = document.getElementById('edit-service-overlay');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.className = 'modal-overlay';
  el.id = 'edit-service-overlay';

  el.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">
        <h2>Edit Layanan</h2>
        <button class="close-modal" id="btn-close-edit"><i class="ri-close-line"></i></button>
      </div>
      <form id="form-edit-service">
        <div class="form-group">
          <label>Judul Layanan</label>
          <input type="text" id="edit-title" value="${s.title}" required />
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label>Kategori</label>
            <select id="edit-cat-id" required>
              ${getCategoryOptions(s.category_id)}
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select id="edit-status">
              <option value="draft" ${s.status === 'draft' ? 'selected' : ''}>Draft</option>
              <option value="pending" ${s.status === 'pending' ? 'selected' : ''}>Pending</option>
              <option value="approved" ${s.status === 'approved' ? 'selected' : ''}>Approved</option>
              <option value="rejected" ${s.status === 'rejected' ? 'selected' : ''}>Rejected</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Email Freelancer</label>
          <input type="email" id="edit-free-email" class="free-email-input" value="${s.freelancer_email}" required />
          <div class="free-info" style="font-size:12px; margin-top:6px; font-weight:600;"></div>
          <input type="hidden" id="edit-free-id" class="free-id-input" value="${s.freelancer_id}" required />
          <input type="hidden" id="edit-free-name" class="free-name-input" value="${s.freelancer_name}" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Harga Min</label>
            <div class="input-group">
              <span class="input-prefix">Rp</span>
              <input type="number" id="edit-price-min" value="${s.price_min}" required min="1000" />
            </div>
          </div>
          <div class="form-group">
            <label>Harga Max (Opsional)</label>
            <div class="input-group">
              <span class="input-prefix">Rp</span>
              <input type="number" id="edit-price-max" value="${s.price_max || ''}" min="1000" />
            </div>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group"><label>Estimasi Min (Hari)</label><input type="number" id="edit-del-min" value="${s.delivery_min}" required min="1" /></div>
          <div class="form-group"><label>Estimasi Max (Hari)</label><input type="number" id="edit-del-max" value="${s.delivery_max}" required min="1" /></div>
        </div>

        <div class="form-group">
          <label>Deskripsi</label>
          <textarea id="edit-desc" rows="4" required>${s.description}</textarea>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-secondary" id="btn-cancel-edit">Batal</button>
          <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Update Layanan</button>
        </div>
      </form>
    </div>
  `;
  
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));

  bindFreelancerEmailLookup(el);

  const closeFn = () => { el.classList.remove('open'); setTimeout(() => el.remove(), 280); };
  el.querySelector('#btn-close-edit').addEventListener('click', closeFn);
  el.querySelector('#btn-cancel-edit').addEventListener('click', closeFn);

  el.querySelector('#form-edit-service').addEventListener('submit', (e) => {
    e.preventDefault();
    if (!el.querySelector('#edit-free-id').value) {
      alert("Email Freelancer tidak valid atau tidak ditemukan.");
      return;
    }

    const pMax = el.querySelector('#edit-price-max').value;
    s.title = el.querySelector('#edit-title').value;
    s.category_id = el.querySelector('#edit-cat-id').value;
    s.freelancer_id = el.querySelector('#edit-free-id').value;
    s.freelancer_email = el.querySelector('#edit-free-email').value;
    s.freelancer_name = el.querySelector('#edit-free-name').value;
    s.price_min = parseInt(el.querySelector('#edit-price-min').value);
    s.price_max = pMax ? parseInt(pMax) : null;
    s.delivery_min = parseInt(el.querySelector('#edit-del-min').value);
    s.delivery_max = parseInt(el.querySelector('#edit-del-max').value);
    s.status = el.querySelector('#edit-status').value;
    s.description = el.querySelector('#edit-desc').value;
    
    if (s.status !== 'rejected') {
      s.reject_reason = null;
    }
    
    closeFn();
    refreshUI();
  });
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
  const input = document.getElementById('service-search-input');
  if (input) input.addEventListener('input', applyFilterAndSearch);
}

function applyFilterAndSearch() {
  const activeTab = document.querySelector('.filter-tab.active');
  const filter = activeTab ? activeTab.dataset.filter : 'all';
  const q = (document.getElementById('service-search-input')?.value || '').toLowerCase();

  let filtered = servicesData;
  if (filter !== 'all') {
    filtered = servicesData.filter(s => s.status === filter);
  }
  if (q) {
    filtered = filtered.filter(s => 
      s.title.toLowerCase().includes(q) || 
      getCategoryName(s.category_id).toLowerCase().includes(q) || 
      s.freelancer_name.toLowerCase().includes(q)
    );
  }
  renderCards(filtered);
}

function refreshUI() {
  renderStats();
  applyFilterAndSearch();
}

function initPage() {
  renderStats();
  renderCards();
  initFilters();
  initSearch();

  const btnAdd = document.getElementById('btn-add-service');
  if (btnAdd) btnAdd.addEventListener('click', openAddServiceModal);

  const overlay = document.getElementById('detail-modal-overlay');
  if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) closeServiceModal(); });

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