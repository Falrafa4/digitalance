let hasUnreadMessages = true;

let offersData = [
  {
    id: 'OFF-001',
    client_name: 'PT. Teknologi Maju',
    freelancer_name: 'Reva Anindya',
    title: 'Desain Ulang Landing Page E-Commerce',
    description: 'Saya menawarkan pembuatan desain ulang landing page dengan style modern dan konversi tinggi, mencakup wireframe dan prototype.',
    price: 4500000,
    delivery_time: '7 Hari',
    status: 'pending'
  },
  {
    id: 'OFF-002',
    client_name: 'Budi Santoso',
    freelancer_name: 'Dimas Prasetyo',
    title: 'Video Profile Durasi 3 Menit',
    description: 'Paket lengkap video shooting dan editing untuk company profile. Termasuk color grading dan royalty-free music.',
    price: 3000000,
    delivery_time: '14 Hari',
    status: 'accepted'
  },
  {
    id: 'OFF-003',
    client_name: 'Ayu Lestari',
    freelancer_name: 'Sari Wulandari',
    title: 'Manajemen Instagram 1 Bulan',
    description: 'Pembuatan 15 desain feed dan 10 story beserta copywriting dan riset hashtag.',
    price: 1500000,
    delivery_time: '30 Hari',
    status: 'rejected'
  }
];

let negoData = [
  {
    id: 'NEG-001',
    order_id: 'ORD-051',
    sender_id: 'C-102',
    sender_name: 'PT. Teknologi Maju',
    sender_type: 'Client',
    message: 'Halo Reva, apakah harganya bisa kurang sedikit? Budget kami maksimal di angka 4 juta untuk landing page ini.',
    created_at: '2026-02-28 09:30'
  },
  {
    id: 'NEG-002',
    order_id: 'ORD-051',
    sender_id: 'F-201',
    sender_name: 'Reva Anindya',
    sender_type: 'Freelancer',
    message: 'Baik Pak, saya bisa sesuaikan di harga 4 juta, namun untuk revisi mayor saya batasi maksimal 2 kali ya.',
    created_at: '2026-02-28 09:45'
  },
  {
    id: 'NEG-003',
    order_id: 'ORD-088',
    sender_id: 'C-105',
    sender_name: 'Budi Santoso',
    sender_type: 'Client',
    message: 'Bisa tolong tambahkan subtitle bahasa Inggris di videonya nanti?',
    created_at: '2026-02-27 15:20'
  }
];

function formatRupiah(number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
}

function renderStats() {
  const row = document.getElementById('stats-row');
  if (!row) return;

  const totalOffers = offersData.length;
  const pendingOffers = offersData.filter(o => o.status === 'pending').length;
  const totalNego = negoData.length;
  const todayNego = negoData.filter(n => n.created_at.includes('2026-02-28')).length;

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
        <span class="stat-value">${todayNego}</span>
        <span class="stat-label">Pesan Masuk Hari Ini</span>
      </div>
    </div>
  `;
}

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

  tbody.innerHTML = data.map(o => `
    <tr>
      <td><span class="id-badge">${o.id}</span></td>
      <td>
        <div class="user-pair">
          <span class="user-name" title="Klien"><i class="ri-user-line"></i> ${o.client_name}</span>
          <span class="user-name" title="Freelancer"><i class="ri-briefcase-line"></i> ${o.freelancer_name}</span>
        </div>
      </td>
      <td>
        <div class="offer-detail-cell">
          <span class="offer-title">${o.title}</span>
          <span class="offer-desc" title="${o.description}">${o.description}</span>
        </div>
      </td>
      <td>
        <div class="price-time-cell">
          <span class="price-val">${formatRupiah(o.price)}</span>
          <span class="time-val"><i class="ri-timer-line"></i> ${o.delivery_time}</span>
        </div>
      </td>
      <td><span class="status-pill status-${o.status}">${o.status}</span></td>
      <td>
        <div class="action-btns">
          <button class="btn-action" title="Detail" onclick="openOfferModal('${o.id}')"><i class="ri-eye-line"></i></button>
          <button class="btn-action danger" title="Hapus" onclick="deleteOffer('${o.id}')"><i class="ri-delete-bin-line"></i></button>
        </div>
      </td>
    </tr>
  `).join('');
}

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

  tbody.innerHTML = data.map(n => `
    <tr>
      <td><span class="id-badge">${n.id}</span></td>
      <td><span class="ref-badge"><i class="ri-file-list-3-line"></i> ${n.order_id}</span></td>
      <td>
        <div class="user-pair">
          <span class="user-name">${n.sender_name}</span>
          <span style="font-size:11px; color:var(--slate-400); font-weight:600; text-transform:uppercase;">${n.sender_type} (${n.sender_id})</span>
        </div>
      </td>
      <td><div class="msg-cell" title="${n.message}">${n.message}</div></td>
      <td style="font-size:12.5px; color:var(--slate-600); white-space:nowrap;">${n.created_at}</td>
      <td>
        <div class="action-btns">
          <button class="btn-action" title="Baca Pesan" onclick="openNegoModal('${n.id}')"><i class="ri-eye-line"></i></button>
          <button class="btn-action danger" title="Hapus Pesan" onclick="deleteNego('${n.id}')"><i class="ri-delete-bin-line"></i></button>
        </div>
      </td>
    </tr>
  `).join('');
}

function openOfferModal(id) {
  const o = offersData.find(x => x.id === id);
  if (!o) return;
  const overlay = document.getElementById('detail-modal-overlay');
  const box = document.getElementById('detail-modal-box');

  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closeModal()"><i class="ri-close-line"></i></button>
    </div>
    <div class="modal-body">
      <h2 class="modal-name">Detail Tawaran</h2>
      
      <div class="modal-info-list">
        <div class="modal-info-row">
          <i class="ri-hashtag"></i>
          <div class="modal-info-text"><strong>Offer ID</strong>${o.id}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-user-line"></i>
          <div class="modal-info-text"><strong>Klien Target</strong>${o.client_name}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-briefcase-line"></i>
          <div class="modal-info-text"><strong>Freelancer Pengirim</strong>${o.freelancer_name}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-money-dollar-circle-line"></i>
          <div class="modal-info-text"><strong>Harga Ditawarkan</strong>${formatRupiah(o.price)}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-timer-line"></i>
          <div class="modal-info-text"><strong>Estimasi Waktu</strong>${o.delivery_time}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-checkbox-circle-line"></i>
          <div class="modal-info-text"><strong>Status Tawaran</strong><span class="status-pill status-${o.status}" style="margin-top:4px;">${o.status}</span></div>
        </div>
      </div>

      <p class="modal-section-title">Judul & Deskripsi Tawaran</p>
      <div class="msg-box">
        <strong style="display:block; margin-bottom:6px; color:var(--slate-900);">${o.title}</strong>
        ${o.description}
      </div>

      <button class="modal-btn-delete" onclick="deleteOffer('${o.id}'); closeModal();"><i class="ri-delete-bin-line"></i> Hapus Tawaran Ini</button>
    </div>
  `;
  overlay.classList.add('open');
}

function openNegoModal(id) {
  const n = negoData.find(x => x.id === id);
  if (!n) return;
  const overlay = document.getElementById('detail-modal-overlay');
  const box = document.getElementById('detail-modal-box');

  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closeModal()"><i class="ri-close-line"></i></button>
    </div>
    <div class="modal-body">
      <h2 class="modal-name">Log Pesan Negosiasi</h2>
      
      <div class="modal-info-list">
        <div class="modal-info-row">
          <i class="ri-file-list-3-line"></i>
          <div class="modal-info-text"><strong>Terkait Order ID</strong>${n.order_id}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-user-smile-line"></i>
          <div class="modal-info-text"><strong>Pengirim Pesan</strong>${n.sender_name} (${n.sender_type})</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-calendar-line"></i>
          <div class="modal-info-text"><strong>Waktu Kirim</strong>${n.created_at}</div>
        </div>
      </div>

      <p class="modal-section-title">Isi Pesan</p>
      <div class="msg-box">
        ${n.message}
      </div>

      <button class="modal-btn-delete" onclick="deleteNego('${n.id}'); closeModal();"><i class="ri-delete-bin-line"></i> Hapus Pesan Ini</button>
    </div>
  `;
  overlay.classList.add('open');
}

function closeModal() {
  document.getElementById('detail-modal-overlay').classList.remove('open');
}

function deleteOffer(id) {
  if (confirm(`Yakin ingin menghapus tawaran ${id}?`)) {
    offersData = offersData.filter(o => o.id !== id);
    refreshUI();
  }
}

function deleteNego(id) {
  if (confirm(`Yakin ingin menghapus log pesan negosiasi ${id}?`)) {
    negoData = negoData.filter(n => n.id !== id);
    refreshUI();
  }
}

function initTabs() {
  const tabs = document.querySelectorAll('.section-tab');
  const contents = document.querySelectorAll('.tab-content');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));
      
      tab.classList.add('active');
      document.getElementById(tab.dataset.target).classList.add('active');
      
      document.getElementById('global-search-input').value = '';
      refreshUI();
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
    const filter = document.querySelector('#offers-filters .filter-tab.active')?.dataset.filter || 'all';
    let filteredOffers = offersData;
    
    if (filter !== 'all') {
      filteredOffers = filteredOffers.filter(o => o.status === filter);
    }
    if (q) {
      filteredOffers = filteredOffers.filter(o => 
        o.id.toLowerCase().includes(q) || 
        o.title.toLowerCase().includes(q) || 
        o.client_name.toLowerCase().includes(q) ||
        o.freelancer_name.toLowerCase().includes(q)
      );
    }
    renderOffersTable(filteredOffers);
  } else {
    let filteredNego = negoData;
    if (q) {
      filteredNego = filteredNego.filter(n => 
        n.id.toLowerCase().includes(q) || 
        n.order_id.toLowerCase().includes(q) || 
        n.sender_name.toLowerCase().includes(q) ||
        n.message.toLowerCase().includes(q)
      );
    }
    renderNegoTable(filteredNego);
  }
}

function refreshUI() {
  renderStats();
  applySearchAndFilter();
}

function initPage() {
  initTabs();
  initFilters();
  initSearch();
  refreshUI();

  const overlay = document.getElementById('detail-modal-overlay');
  if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });

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