let hasUnreadMessages = true;

const freelancers = [
  { id: 'F-201', name: 'Reva Anindya', email: 'reva@digitalance.id' },
  { id: 'F-205', name: 'Budi Santoso', email: 'budi@digitalance.id' },
  { id: 'F-210', name: 'Sari Wulandari', email: 'sari@digitalance.id' }
];

let portfoliosData = [
  {
    id: 'PRT-001',
    freelancer_id: 'F-201',
    freelancer_email: 'reva@digitalance.id',
    freelancer_name: 'Reva Anindya',
    service_id: 'SRV-001',
    title: 'Desain Aplikasi E-Commerce Grocery',
    description: 'Studi kasus lengkap desain UI/UX untuk aplikasi grocery. Termasuk research, wireframing, dan high-fidelity prototype yang berfokus pada kemudahan pengguna.',
    media: {
      image: 'https://images.unsplash.com/photo-1607004468138-c7e4f1e208ae?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
      pdf: 'E-Commerce_Grocery_CaseStudy.pdf'
    },
    status: 'approved',
    reject_reason: null,
    created_at: '2026-02-20'
  },
  {
    id: 'PRT-002',
    freelancer_id: 'F-205',
    freelancer_email: 'budi@digitalance.id',
    freelancer_name: 'Budi Santoso',
    service_id: 'SRV-002',
    title: 'Company Profile PT. Maju Jaya',
    description: 'Pengembangan website responsif untuk perusahaan manufaktur dengan animasi modern menggunakan GSAP dan Tailwind CSS.',
    media: {
      image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
      pdf: 'Tech_Spec_MajuJaya.pdf'
    },
    status: 'pending',
    reject_reason: null,
    created_at: '2026-02-27'
  },
  {
    id: 'PRT-003',
    freelancer_id: 'F-210',
    freelancer_email: 'sari@digitalance.id',
    freelancer_name: 'Sari Wulandari',
    service_id: 'SRV-004',
    title: 'Kumpulan Artikel Kesehatan Kulit',
    description: 'Draft kasar artikel tentang rutinitas skincare malam.',
    media: {
      image: 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
      pdf: 'Draft_Artikel.pdf'
    },
    status: 'rejected',
    reject_reason: 'Kualitas file PDF lampiran buram dan tidak dapat terbaca. Mohon perbaiki dan sertakan sumber terpercaya di dalam dokumen.',
    created_at: '2026-02-28'
  }
];

function renderStats() {
  const row = document.getElementById('stats-row');
  if (!row) return;

  const total = portfoliosData.length;
  const approved = portfoliosData.filter(p => p.status === 'approved').length;
  const pending = portfoliosData.filter(p => p.status === 'pending').length;
  const rejected = portfoliosData.filter(p => p.status === 'rejected').length;

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
    const statusLabel = { approved: 'Approved', pending: 'Pending', rejected: 'Rejected', draft: 'Draft' }[p.status] || p.status;

    let pendingActions = '';
    if (p.status === 'pending') {
      pendingActions = `
        <div class="card-approval-bar">
          <button class="card-btn-approve" onclick="event.stopPropagation(); approvePort('${p.id}')">
            <i class="ri-check-line"></i> Approve
          </button>
          <button class="card-btn-reject" onclick="event.stopPropagation(); openRejectModal('${p.id}')">
            <i class="ri-close-line"></i> Tolak
          </button>
        </div>
      `;
    }

    return `
      <div class="port-card" onclick="openPortModal('${p.id}')">
        <div class="port-cover-wrap">
          <img class="port-cover" src="${p.media.image}" alt="Cover">
          <span class="port-status-badge ${p.status}">${statusLabel}</span>
          <div class="port-cover-overlay">
            <span class="preview-hint"><i class="ri-eye-line"></i> Lihat Detail</span>
          </div>
        </div>
        <div class="port-body">
          <div class="port-id-row">
            <span class="port-id">${p.id}</span>
            <span class="port-srv-id"><i class="ri-tools-line"></i> ${p.service_id}</span>
          </div>
          <h3 class="port-title" title="${p.title}">${p.title}</h3>
          <p class="port-desc">${p.description}</p>
          <div class="port-meta-row">
            <span class="port-freelancer"><i class="ri-user-line"></i> ${p.freelancer_name}</span>
            <span class="port-date"><i class="ri-calendar-line"></i> ${p.created_at}</span>
          </div>
        </div>
        ${pendingActions}
        <div class="port-footer">
          <button class="card-btn-view" onclick="event.stopPropagation(); openPortModal('${p.id}')">
            <i class="ri-eye-line"></i> Lihat Portofolio
          </button>
          <button class="card-btn-delete" onclick="event.stopPropagation(); deletePort('${p.id}')">
            <i class="ri-delete-bin-line"></i>
          </button>
        </div>
      </div>
    `;
  }).join('');
}

function openPortModal(id) {
  const p = portfoliosData.find(x => x.id === id);
  if (!p) return;

  const overlay = document.getElementById('detail-modal-overlay');
  const box = document.getElementById('detail-modal-box');

  let actionHtml = '';
  if (p.status === 'pending') {
    actionHtml = `
      <button class="modal-btn-approve" onclick="approvePort('${p.id}'); closePortModal()"><i class="ri-check-line"></i> Approve</button>
      <button class="modal-btn-reject" onclick="openRejectModal('${p.id}'); closePortModal()"><i class="ri-close-line"></i> Reject</button>
    `;
  }
  actionHtml += `<button class="modal-btn-delete" onclick="deletePort('${p.id}'); closePortModal()"><i class="ri-delete-bin-line"></i> Hapus</button>`;

  let rejectHtml = '';
  if (p.status === 'rejected' && p.reject_reason) {
    rejectHtml = `
      <div class="reject-reason-box">
        <strong>Alasan Penolakan:</strong>
        ${p.reject_reason}
      </div>
    `;
  }

  const statusLabel = { approved: 'Approved', pending: 'Pending', rejected: 'Rejected', draft: 'Draft' }[p.status] || p.status;

  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closePortModal()"><i class="ri-close-line"></i></button>
      <img src="${p.media.image}" alt="Header">
      <button class="modal-preview-btn" onclick="openImagePreview('${p.media.image}', '${p.title.replace(/'/g, "\\'")}')">
        <i class="ri-zoom-in-line"></i> Lihat Portofolio
      </button>
    </div>
    <div class="modal-body">
      <div class="modal-role-row">
        <span class="port-id">${p.id}</span>
        <span class="port-status-badge ${p.status}" style="position:static; box-shadow:none;">${statusLabel}</span>
      </div>
      
      <h2 class="modal-name">${p.title}</h2>

      ${rejectHtml}

      <div class="modal-info-grid">
        <div class="modal-info-card">
          <div class="modal-info-label">Freelancer</div>
          <div class="modal-info-value"><i class="ri-user-line"></i> ${p.freelancer_name}</div>
        </div>
        <div class="modal-info-card">
          <div class="modal-info-label">Terkait Layanan</div>
          <div class="modal-info-value"><i class="ri-tools-line"></i> ${p.service_id}</div>
        </div>
        <div class="modal-info-card">
          <div class="modal-info-label">Email</div>
          <div class="modal-info-value"><i class="ri-mail-line"></i> ${p.freelancer_email}</div>
        </div>
        <div class="modal-info-card">
          <div class="modal-info-label">Tanggal Upload</div>
          <div class="modal-info-value"><i class="ri-calendar-line"></i> ${p.created_at}</div>
        </div>
      </div>

      <p class="modal-section-title">Deskripsi Karya</p>
      <div class="desc-box">${p.description}</div>

      <p class="modal-section-title">Dokumen Lampiran</p>
      <div class="file-download-box">
        <div class="file-info">
          <i class="ri-file-pdf-2-fill file-icon"></i>
          <div>
            <span class="file-name">${p.media.pdf}</span>
            <span class="file-size">PDF Document</span>
          </div>
        </div>
        <a href="#" class="btn-download" onclick="event.preventDefault()"><i class="ri-download-2-line"></i> Unduh</a>
      </div>

      <div class="modal-action-group">
        ${actionHtml}
      </div>
    </div>
  `;

  overlay.classList.add('open');
}

function openImagePreview(src, title) {
  const existing = document.getElementById('img-preview-overlay');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.id = 'img-preview-overlay';
  el.className = 'img-preview-overlay';
  el.innerHTML = `
    <div class="img-preview-backdrop"></div>
    <div class="img-preview-box">
      <div class="img-preview-header">
        <span class="img-preview-title"><i class="ri-image-line"></i> ${title}</span>
        <button class="img-preview-close" id="btn-close-preview"><i class="ri-close-line"></i></button>
      </div>
      <div class="img-preview-content">
        <img src="${src}" alt="${title}" />
      </div>
    </div>
  `;
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));

  const closeFn = () => { el.classList.remove('open'); setTimeout(() => el.remove(), 280); };
  el.querySelector('#btn-close-preview').addEventListener('click', closeFn);
  el.querySelector('.img-preview-backdrop').addEventListener('click', closeFn);
}

function closePortModal() {
  document.getElementById('detail-modal-overlay').classList.remove('open');
}

function approvePort(id) {
  const p = portfoliosData.find(x => x.id === id);
  if (p) {
    p.status = 'approved';
    p.reject_reason = null;
    refreshUI();
  }
}

function openRejectModal(id) {
  const p = portfoliosData.find(x => x.id === id);
  if (!p) return;

  const existing = document.getElementById('reject-port-overlay');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.className = 'modal-overlay';
  el.id = 'reject-port-overlay';

  el.innerHTML = `
    <div class="modal-content" style="max-width: 450px;">
      <div class="modal-header">
        <h2>Tolak Portofolio</h2>
        <button class="close-modal" id="btn-close-reject"><i class="ri-close-line"></i></button>
      </div>
      <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 16px;margin-bottom:16px;font-size:13px;color:#991b1b;font-weight:600;">
        <i class="ri-error-warning-line"></i> Anda akan menolak: <strong>${p.title}</strong> (${p.id})
      </div>
      <form id="form-reject-port">
        <div class="form-group">
          <label>Alasan Penolakan</label>
          <textarea id="reject-reason-input" rows="4" required placeholder="Tuliskan mengapa karya ini ditolak...">${p.reject_reason || ''}</textarea>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-secondary" id="btn-cancel-reject">Batal</button>
          <button type="submit" class="btn-reject-confirm"><i class="ri-close-circle-line"></i> Tolak Portofolio</button>
        </div>
      </form>
    </div>
  `;
  
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));

  const closeFn = () => { el.classList.remove('open'); setTimeout(() => el.remove(), 280); };
  el.querySelector('#btn-close-reject').addEventListener('click', closeFn);
  el.querySelector('#btn-cancel-reject').addEventListener('click', closeFn);
  
  el.querySelector('#form-reject-port').addEventListener('submit', (e) => {
    e.preventDefault();
    p.status = 'rejected';
    p.reject_reason = el.querySelector('#reject-reason-input').value;
    closeFn();
    refreshUI();
  });
}

function deletePort(id) {
  if (confirm(`Yakin ingin menghapus portofolio ${id}? Tindakan ini tidak dapat dibatalkan.`)) {
    portfoliosData = portfoliosData.filter(p => p.id !== id);
    refreshUI();
  }
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

function handleFileInput(inputEl, displayElId) {
  inputEl.addEventListener('change', (e) => {
    const file = e.target.files[0];
    const display = document.getElementById(displayElId);
    if (file) {
      display.innerHTML = `<i class="ri-check-double-line" style="color:#10B981;"></i><span style="color:#0f766e;">${file.name} terpilih</span>`;
    }
  });
}

function openAddPortModal() {
  const existing = document.getElementById('add-port-overlay');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.className = 'modal-overlay';
  el.id = 'add-port-overlay';

  el.innerHTML = `
    <div class="modal-content">
      <div class="modal-header">
        <h2>Tambah Portofolio</h2>
        <button class="close-modal" id="btn-close-add"><i class="ri-close-line"></i></button>
      </div>
      <form id="form-add-port">
        <div class="form-group">
          <label>Judul Portofolio</label>
          <input type="text" id="add-title" required />
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label>Service ID Terkait</label>
            <input type="text" id="add-srv-id" required placeholder="Contoh: SRV-001" />
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
          <input type="email" id="add-free-email" class="free-email-input" required placeholder="Cari freelancer..." />
          <div class="free-info" style="font-size:12px; margin-top:6px; font-weight:600;"></div>
          <input type="hidden" id="add-free-id" class="free-id-input" required />
          <input type="hidden" id="add-free-name" class="free-name-input" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Gambar Header (JPG/PNG)</label>
            <div class="file-upload-wrapper">
              <input type="file" id="add-img" accept="image/png, image/jpeg" required />
              <div class="file-upload-content" id="display-add-img">
                <i class="ri-image-add-line"></i>
                <span>Klik atau Drop gambar di sini</span>
                <small>Maksimal 2MB</small>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Lampiran Dokumen (PDF)</label>
            <div class="file-upload-wrapper">
              <input type="file" id="add-pdf" accept="application/pdf" required />
              <div class="file-upload-content" id="display-add-pdf">
                <i class="ri-file-add-line"></i>
                <span>Klik atau Drop file PDF</span>
                <small>Maksimal 5MB</small>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Deskripsi</label>
          <textarea id="add-desc" rows="3" required></textarea>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-secondary" id="btn-cancel-add">Batal</button>
          <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Simpan Portofolio</button>
        </div>
      </form>
    </div>
  `;
  
  document.body.appendChild(el);
  requestAnimationFrame(() => el.classList.add('open'));

  bindFreelancerEmailLookup(el);
  handleFileInput(el.querySelector('#add-img'), 'display-add-img');
  handleFileInput(el.querySelector('#add-pdf'), 'display-add-pdf');

  const closeFn = () => { el.classList.remove('open'); setTimeout(() => el.remove(), 280); };
  el.querySelector('#btn-close-add').addEventListener('click', closeFn);
  el.querySelector('#btn-cancel-add').addEventListener('click', closeFn);
  
  el.querySelector('#form-add-port').addEventListener('submit', (e) => {
    e.preventDefault();
    if (!el.querySelector('#add-free-id').value) {
      alert("Email Freelancer tidak valid atau tidak ditemukan.");
      return;
    }

    const imgFile = el.querySelector('#add-img').files[0];
    const pdfFile = el.querySelector('#add-pdf').files[0];

    const newPort = {
      id: 'PRT-' + Math.floor(Math.random() * 1000).toString().padStart(3, '0'),
      freelancer_id: el.querySelector('#add-free-id').value,
      freelancer_email: el.querySelector('#add-free-email').value,
      freelancer_name: el.querySelector('#add-free-name').value,
      service_id: el.querySelector('#add-srv-id').value,
      title: el.querySelector('#add-title').value,
      description: el.querySelector('#add-desc').value,
      media: {
        image: imgFile ? URL.createObjectURL(imgFile) : 'https://via.placeholder.com/800',
        pdf: pdfFile ? pdfFile.name : 'Document.pdf'
      },
      status: el.querySelector('#add-status').value,
      reject_reason: null,
      created_at: new Date().toISOString().split('T')[0]
    };
    portfoliosData.unshift(newPort);
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
  const input = document.getElementById('port-search-input');
  if (input) input.addEventListener('input', applyFilterAndSearch);
}

function applyFilterAndSearch() {
  const activeTab = document.querySelector('.filter-tab.active');
  const filter = activeTab ? activeTab.dataset.filter : 'all';
  const q = (document.getElementById('port-search-input')?.value || '').toLowerCase();

  let filtered = portfoliosData;
  if (filter !== 'all') {
    filtered = portfoliosData.filter(p => p.status === filter);
  }
  if (q) {
    filtered = filtered.filter(p => 
      p.title.toLowerCase().includes(q) || 
      p.freelancer_name.toLowerCase().includes(q) || 
      p.service_id.toLowerCase().includes(q)
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

  const btnAdd = document.getElementById('btn-add-port');
  if (btnAdd) btnAdd.addEventListener('click', openAddPortModal);

  const overlay = document.getElementById('detail-modal-overlay');
  if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) closePortModal(); });

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