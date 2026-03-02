let hasUnreadMessages = true;

let adminsData = [
  { id: 1, name: 'Reva Anindya',   email: 'reva@digitalance.id',   avatar: 'https://picsum.photos/seed/reva/200/200',   status: 'Active',    joinDate: '1 Jan 2024',  phone: '+62 812-0001-0001', bio: 'System Manager yang bertanggung jawab atas keamanan dan stabilitas sistem Digitalance.' },
  { id: 2, name: 'Dimas Prasetyo', email: 'dimas@digitalance.id',  avatar: 'https://picsum.photos/seed/dimas/200/200',  status: 'Active',    joinDate: '15 Mar 2024', phone: '+62 813-0002-0002', bio: 'Admin operasional yang menangani verifikasi freelancer dan review konten platform.' },
  { id: 3, name: 'Sari Wulandari', email: 'sari@digitalance.id',   avatar: 'https://picsum.photos/seed/sari/200/200',   status: 'Active',    joinDate: '2 Jun 2024',  phone: '+62 857-0003-0003', bio: 'Admin komunitas yang bertugas menjaga kualitas konten dan menangani laporan pengguna.' },
  { id: 4, name: 'Fajar Nugraha',  email: 'fajar@digitalance.id',  avatar: 'https://picsum.photos/seed/fajar/200/200',  status: 'Inactive',  joinDate: '20 Aug 2024', phone: '+62 878-0004-0004', bio: 'Admin yang mengelola laporan transaksi dan rekonsiliasi pembayaran.' },
  { id: 5, name: 'Putri Maharani', email: 'putri@digitalance.id',  avatar: 'https://picsum.photos/seed/putri/200/200',  status: 'Suspended', joinDate: '10 Sep 2024', phone: '+62 851-0005-0005', bio: 'Admin yang ditangguhkan sementara akibat pelanggaran kebijakan internal.' },
];

const STATUS_DOT_MAP  = { 'Active': 'online', 'Inactive': 'offline', 'Suspended': 'suspended' };
const STATUS_PILL_MAP = { 'Active': 'active',  'Inactive': 'inactive', 'Suspended': 'suspended' };

function renderStats() {
  const row = document.getElementById('stats-row');
  if (!row) return;
  const total     = adminsData.length;
  const active    = adminsData.filter(a => a.status === 'Active').length;
  const inactive  = adminsData.filter(a => a.status === 'Inactive').length;
  const suspended = adminsData.filter(a => a.status === 'Suspended').length;
  row.innerHTML = `
    <div class="stat-card"><div class="stat-icon teal"><i class="ri-shield-user-line"></i></div><div class="stat-text"><span class="stat-value">${total}</span><span class="stat-label">Total Admin</span></div></div>
    <div class="stat-card"><div class="stat-icon blue"><i class="ri-checkbox-circle-line"></i></div><div class="stat-text"><span class="stat-value">${active}</span><span class="stat-label">Admin Aktif</span></div></div>
    <div class="stat-card"><div class="stat-icon amber"><i class="ri-moon-line"></i></div><div class="stat-text"><span class="stat-value">${inactive}</span><span class="stat-label">Nonaktif</span></div></div>
    <div class="stat-card"><div class="stat-icon red"><i class="ri-forbid-line"></i></div><div class="stat-text"><span class="stat-value">${suspended}</span><span class="stat-label">Suspended</span></div></div>
  `;
}

function renderCards(data = adminsData) {
  const grid = document.getElementById('admin-card-grid');
  if (!grid) return;
  if (!data || data.length === 0) {
    grid.innerHTML = `<div class="empty-state"><div class="empty-icon"><i class="ri-shield-user-line"></i></div><h3>Tidak ada admin ditemukan</h3><p>Coba ubah filter atau kata kunci pencarian.</p></div>`;
    return;
  }
  grid.innerHTML = data.map(a => `
    <div class="user-card-item">
      <div class="user-card-top">
        <div class="user-card-avatar-wrap">
          <img class="user-card-avatar" src="${a.avatar}" alt="${a.name}" />
          <span class="user-online-dot ${STATUS_DOT_MAP[a.status] ?? 'offline'}"></span>
        </div>
        <span class="status-pill ${STATUS_PILL_MAP[a.status] ?? 'inactive'}">
          <i class="ri-circle-fill" style="font-size:7px;"></i> ${a.status}
        </span>
      </div>
      <h3 class="user-card-name">${a.name}</h3>
      <span class="user-card-role"><i class="ri-shield-user-line"></i> Admin</span>
      <span class="user-card-location"><i class="ri-mail-line"></i> ${a.email}</span>
      <button class="btn-detail" style="margin-top:auto;" onclick="openAdminModal(${a.id})">
        <i class="ri-eye-line"></i> Lihat Profil
      </button>
    </div>
  `).join('');
}

function openAdminModal(id) {
  const a = adminsData.find(a => a.id === id);
  if (!a) return;
  const overlay = document.getElementById('admin-modal-overlay');
  const box     = document.getElementById('admin-modal-box');
  const isSuspended  = a.status === 'Suspended';
  const suspendLabel = isSuspended ? 'Unsuspend' : 'Suspend';
  const suspendIcon  = isSuspended ? 'ri-checkbox-circle-line' : 'ri-forbid-line';
  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closeAdminModal()"><i class="ri-close-line"></i></button>
      <div class="modal-avatar-wrap"><img class="modal-avatar" src="${a.avatar}" alt="${a.name}" /></div>
    </div>
    <div class="modal-body">
      <h2 class="modal-name">${a.name}</h2>
      <div class="modal-role-row">
        <span class="role-badge admin"><i class="ri-shield-user-line"></i> Admin</span>
        <span class="status-pill ${STATUS_PILL_MAP[a.status] ?? 'inactive'}"><i class="ri-circle-fill" style="font-size:7px;"></i> ${a.status}</span>
      </div>
      <p class="modal-bio">${a.bio}</p>
      <div class="modal-info-list">
        <div class="modal-info-row"><i class="ri-mail-line"></i><span>${a.email}</span></div>
        <div class="modal-info-row"><i class="ri-phone-line"></i><span>${a.phone}</span></div>
        <div class="modal-info-row"><i class="ri-calendar-line"></i><span>Bergabung ${a.joinDate}</span></div>
      </div>
      <div class="modal-action-group">
        <div class="modal-action-row">
          <button class="modal-btn-primary" onclick="closeAdminModal()"><i class="ri-mail-send-line"></i> Kirim Pesan</button>
          <button class="modal-btn-edit" onclick="openEditAdminModal(${a.id}); closeAdminModal();"><i class="ri-edit-line"></i> Edit</button>
        </div>
        <div class="modal-action-row">
          <button class="modal-btn-warning" onclick="toggleSuspendAdmin(${a.id}); closeAdminModal();"><i class="${suspendIcon}"></i> ${suspendLabel}</button>
          <button class="modal-btn-delete" onclick="confirmDeleteAdmin(${a.id}); closeAdminModal();"><i class="ri-delete-bin-line"></i> Hapus Admin</button>
        </div>
      </div>
    </div>
  `;
  overlay.classList.add('open');
}

function closeAdminModal() {
  document.getElementById('admin-modal-overlay').classList.remove('open');
}

function toggleSuspendAdmin(id) {
  const a = adminsData.find(a => a.id === id);
  if (!a) return;
  a.status = a.status === 'Suspended' ? 'Active' : 'Suspended';
  refreshAfterChange();
}

function confirmDeleteAdmin(id) {
  const a = adminsData.find(a => a.id === id);
  if (!a) return;
  const el = document.createElement('div');
  el.className = 'confirm-overlay'; el.id = 'confirm-delete-overlay';
  el.innerHTML = `
    <div class="confirm-box">
      <div class="confirm-icon"><i class="ri-error-warning-line"></i></div>
      <h3>Hapus Admin?</h3>
      <p>Akun admin <strong>${a.name}</strong> akan dihapus secara permanen.</p>
      <div class="confirm-actions">
        <button class="btn-secondary" onclick="closeConfirmDelete()">Batal</button>
        <button class="modal-btn-delete" onclick="executeDeleteAdmin(${id})"><i class="ri-delete-bin-line"></i> Ya, Hapus</button>
      </div>
    </div>`;
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));
}

function closeConfirmDelete() {
  const el = document.getElementById('confirm-delete-overlay');
  if (el) { el.classList.remove('open'); setTimeout(() => el.remove(), 250); }
}

function executeDeleteAdmin(id) {
  const idx = adminsData.findIndex(a => a.id === id);
  if (idx !== -1) adminsData.splice(idx, 1);
  closeConfirmDelete(); closeAdminModal(); refreshAfterChange();
}

function openAddAdminModal() {
  const existing = document.getElementById('add-admin-overlay');
  if (existing) existing.remove();
  const el = document.createElement('div');
  el.className = 'modal-overlay'; el.id = 'add-admin-overlay';
  el.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">
        <h2>Tambah Admin Baru</h2>
        <button class="close-modal" id="btn-close-add-admin"><i class="ri-close-line"></i></button>
      </div>
      <form id="form-add-admin">
        <div class="form-row">
          <div class="form-group"><label>Nama Lengkap</label><input type="text" id="add-admin-name" required placeholder="e.g. Budi Santoso" /></div>
          <div class="form-group"><label>Email</label><input type="email" id="add-admin-email" required placeholder="budi@digitalance.id" /></div>
        </div>
        <div class="form-group"><label>No. Telepon</label><input type="text" id="add-admin-phone" placeholder="+62 812-xxxx-xxxx" /></div>
        <div class="form-group"><label>Bio</label><textarea id="add-admin-bio" rows="2" placeholder="Deskripsi singkat..."></textarea></div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" id="btn-cancel-add-admin">Batal</button>
          <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Simpan Admin</button>
        </div>
      </form>
    </div>`;
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));
  el.querySelector('#btn-close-add-admin').addEventListener('click', closeAddAdminModal);
  el.querySelector('#btn-cancel-add-admin').addEventListener('click', closeAddAdminModal);
  el.addEventListener('click', (e) => { if (e.target === el) closeAddAdminModal(); });
  el.querySelector('#form-add-admin').addEventListener('submit', (e) => {
    e.preventDefault();
    const name = el.querySelector('#add-admin-name').value.trim();
    adminsData.push({
      id: Date.now(), name,
      email:    el.querySelector('#add-admin-email').value.trim(),
      avatar:   `https://picsum.photos/seed/${name.split(' ')[0].toLowerCase()}/200/200`,
      status:   'Active',
      joinDate: new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
      phone:    el.querySelector('#add-admin-phone').value.trim() || '-',
      bio:      el.querySelector('#add-admin-bio').value.trim() || 'Belum ada bio.',
    });
    closeAddAdminModal(); refreshAfterChange();
  });
}

function closeAddAdminModal() {
  const el = document.getElementById('add-admin-overlay');
  if (el) { el.classList.remove('open'); setTimeout(() => el.remove(), 280); }
}

function openEditAdminModal(id) {
  const a = adminsData.find(a => a.id === id);
  if (!a) return;
  const existing = document.getElementById('edit-admin-overlay');
  if (existing) existing.remove();
  const el = document.createElement('div');
  el.className = 'modal-overlay'; el.id = 'edit-admin-overlay';
  el.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">
        <h2>Edit Admin</h2>
        <button class="close-modal" id="btn-close-edit-admin"><i class="ri-close-line"></i></button>
      </div>
      <form id="form-edit-admin">
        <div class="form-row">
          <div class="form-group"><label>Nama Lengkap</label><input type="text" id="edit-admin-name" required value="${a.name}" /></div>
          <div class="form-group"><label>Email</label><input type="email" id="edit-admin-email" required value="${a.email}" /></div>
        </div>
        <div class="form-group"><label>No. Telepon</label><input type="text" id="edit-admin-phone" value="${a.phone}" /></div>
        <div class="form-group"><label>Bio</label><textarea id="edit-admin-bio" rows="2">${a.bio}</textarea></div>
        <div class="form-group">
          <label>Status</label>
          <select id="edit-admin-status">
            <option value="Active"    ${a.status === 'Active'    ? 'selected' : ''}>Active</option>
            <option value="Inactive"  ${a.status === 'Inactive'  ? 'selected' : ''}>Inactive</option>
            <option value="Suspended" ${a.status === 'Suspended' ? 'selected' : ''}>Suspended</option>
          </select>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" id="btn-cancel-edit-admin">Batal</button>
          <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Simpan Perubahan</button>
        </div>
      </form>
    </div>`;
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));
  el.querySelector('#btn-close-edit-admin').addEventListener('click', closeEditAdminModal);
  el.querySelector('#btn-cancel-edit-admin').addEventListener('click', closeEditAdminModal);
  el.addEventListener('click', (e) => { if (e.target === el) closeEditAdminModal(); });
  el.querySelector('#form-edit-admin').addEventListener('submit', (e) => {
    e.preventDefault();
    a.name   = el.querySelector('#edit-admin-name').value.trim();
    a.email  = el.querySelector('#edit-admin-email').value.trim();
    a.phone  = el.querySelector('#edit-admin-phone').value.trim();
    a.bio    = el.querySelector('#edit-admin-bio').value.trim();
    a.status = el.querySelector('#edit-admin-status').value;
    closeEditAdminModal(); refreshAfterChange();
  });
}

function closeEditAdminModal() {
  const el = document.getElementById('edit-admin-overlay');
  if (el) { el.classList.remove('open'); setTimeout(() => el.remove(), 280); }
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
  const input = document.getElementById('admin-search-input');
  if (!input) return;
  input.addEventListener('input', applyFilterAndSearch);
}

function applyFilterAndSearch() {
  const filter = document.querySelector('.filter-tab.active')?.dataset.filter ?? 'all';
  const q      = (document.getElementById('admin-search-input')?.value || '').toLowerCase();
  let filtered = adminsData;
  if (filter !== 'all') filtered = adminsData.filter(a => a.status === filter);
  if (q) filtered = filtered.filter(a => a.name.toLowerCase().includes(q) || a.email.toLowerCase().includes(q));
  renderCards(filtered);
}

function refreshAfterChange() {
  renderStats();
  applyFilterAndSearch();
}

function initPage() {
  renderStats();
  renderCards();
  initFilters();
  initSearch();
  const btnAdd = document.getElementById('btn-add-admin');
  if (btnAdd) btnAdd.addEventListener('click', openAddAdminModal);
  const overlay = document.getElementById('admin-modal-overlay');
  if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) closeAdminModal(); });
  const notifBtn = document.getElementById('notif-btn');
  if (notifBtn) {
    notifBtn.classList.toggle('has-unread', hasUnreadMessages);
    notifBtn.addEventListener('click', () => notifBtn.classList.remove('has-unread'));
  }
}

document.addEventListener('DOMContentLoaded', initPage);