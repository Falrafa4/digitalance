// ─── PENGATURAN NOTIFIKASI ADMIN ───
const hasUnreadMessages = true;

// ─── BADGE MAP ───
const BADGE_MAP = {
  'Pending':   'badge-pending',
  'Approved':  'badge-completed',
  'Rejected':  'badge-new',
  'In Review': 'badge-progress',
};

// ─── DATA DUMMY PENDING VERIFICATIONS ───
// Ganti dengan data dari API saat integrasi backend
const pendingVerifications = [
  {
    id: 1,
    name: 'Arya Wicaksana',
    role: 'Freelancer',
    avatar: 'https://picsum.photos/seed/arya/100/100',
    skill: 'UI/UX Designer',
    submittedAt: '2 hours ago',
    status: 'Pending',
  },
  {
    id: 2,
    name: 'Sinta Rahayu',
    role: 'Skomda Student',
    avatar: 'https://picsum.photos/seed/sinta/100/100',
    skill: 'Front-end Developer',
    submittedAt: '5 hours ago',
    status: 'In Review',
  },
  {
    id: 3,
    name: 'Bimo Prakoso',
    role: 'Freelancer',
    avatar: 'https://picsum.photos/seed/bimo/100/100',
    skill: 'Mobile Developer',
    submittedAt: 'Yesterday',
    status: 'Pending',
  },
];

// ─── RENDER PENDING VERIFICATIONS ───
function renderPendingVerifications() {
  const grid = document.getElementById('admin-approval-grid');
  if (!grid) return;

  if (!pendingVerifications || pendingVerifications.length === 0) {
    grid.innerHTML = `
      <div class="empty-state">
        <div class="empty-icon"><i class="ri-shield-check-line"></i></div>
        <h3>All clear!</h3>
        <p>No pending verifications at the moment. Everything is up to date.</p>
      </div>`;
    return;
  }

  grid.innerHTML = pendingVerifications.map(v => `
    <div class="approval-card" data-id="${v.id}">
      <div class="approval-card-top">
        <img class="approval-avatar" src="${v.avatar}" alt="${v.name}" />
        <div class="approval-info">
          <span class="approval-name">${v.name}</span>
          <span class="approval-meta">
            <i class="ri-briefcase-line"></i> ${v.role} &middot; ${v.skill}
          </span>
          <span class="approval-time">
            <i class="ri-time-line"></i> Submitted ${v.submittedAt}
          </span>
        </div>
        <span class="badge ${BADGE_MAP[v.status] ?? 'badge-pending'}">${v.status}</span>
      </div>
      <div class="approval-actions">
        <button class="btn-approve" onclick="handleApprove(${v.id})">
          <i class="ri-check-line"></i> Approve
        </button>
        <button class="btn-reject" onclick="handleReject(${v.id})">
          <i class="ri-close-line"></i> Reject
        </button>
      </div>
    </div>
  `).join('');
}

// ─── HANDLE APPROVE ───
function handleApprove(id) {
  const card = document.querySelector(`.approval-card[data-id="${id}"]`);
  if (!card) return;
  card.classList.add('card-approved');
  card.querySelector('.approval-actions').innerHTML = `
    <span class="action-feedback approved">
      <i class="ri-check-double-line"></i> Approved
    </span>`;
  setTimeout(() => {
    card.style.transition = 'opacity 0.3s, transform 0.3s';
    card.style.opacity = '0';
    card.style.transform = 'translateX(20px)';
    setTimeout(() => card.remove(), 320);
  }, 1200);
}

// ─── HANDLE REJECT ───
function handleReject(id) {
  const card = document.querySelector(`.approval-card[data-id="${id}"]`);
  if (!card) return;
  card.classList.add('card-rejected');
  card.querySelector('.approval-actions').innerHTML = `
    <span class="action-feedback rejected">
      <i class="ri-close-circle-line"></i> Rejected
    </span>`;
  setTimeout(() => {
    card.style.transition = 'opacity 0.3s, transform 0.3s';
    card.style.opacity = '0';
    card.style.transform = 'translateX(20px)';
    setTimeout(() => card.remove(), 320);
  }, 1200);
}

// ─── INIT NOTIFIKASI ───
function initAdminDashboard() {
  const notifBtn = document.getElementById('notif-btn');
  if (!notifBtn) {
    console.error("Element #notif-btn tidak ditemukan.");
    return;
  }

  notifBtn.classList.toggle('has-unread', hasUnreadMessages);
  notifBtn.addEventListener('click', () => {
    notifBtn.classList.remove('has-unread');
  });

  initSidebarNav();
  renderPendingVerifications();
}

// ─── INIT SIDEBAR NAV ───
// Nav item tidak bisa pindah active karena belum ada path/halaman yang terhubung.
// Active state hanya diset via class di HTML secara manual sesuai halaman aktif.
function initSidebarNav() {
  const navItems = document.querySelectorAll('.nav-item');
  navItems.forEach(item => {
    // Cegah perpindahan active state saat diklik
    item.addEventListener('click', (e) => {
      if (!item.classList.contains('active')) {
        e.preventDefault();
        // Tidak ada aksi — hanya hover yang berfungsi
      }
    });
  });
}

// Jalankan ketika DOM siap
document.addEventListener('DOMContentLoaded', initAdminDashboard);


// ════════════════════════════════════════
// ─── USER PAGE ───
// ════════════════════════════════════════

// ─── DATA DUMMY USERS ───
const usersData = [
  {
    id: 1,
    name: 'Arya Wicaksana',
    email: 'arya.wicaksana@email.com',
    avatar: 'https://picsum.photos/seed/arya/200/200',
    role: 'Freelancer',
    status: 'Active',
    joinDate: '12 Jan 2025',
    location: 'Jakarta, Indonesia',
    phone: '+62 812-3456-7890',
    skills: ['UI/UX Design', 'Figma', 'Prototyping'],
    totalOrders: 24,
    totalEarning: '$3,200',
    lastActive: '2 hours ago',
    bio: 'UI/UX Designer dengan 4 tahun pengalaman menciptakan pengalaman digital yang berpusat pada pengguna untuk startup dan perusahaan.',
  },
  {
    id: 2,
    name: 'Sinta Rahayu',
    email: 'sinta.rahayu@email.com',
    avatar: 'https://picsum.photos/seed/sinta/200/200',
    role: 'Skomda Student',
    status: 'Active',
    joinDate: '3 Feb 2025',
    location: 'Bandung, Indonesia',
    phone: '+62 821-9876-5432',
    skills: ['React', 'Tailwind CSS', 'JavaScript'],
    totalOrders: 8,
    totalEarning: '$640',
    lastActive: '1 day ago',
    bio: 'Mahasiswa front-end developer yang antusias membangun proyek nyata dan mendapatkan pengalaman profesional.',
  },
  {
    id: 3,
    name: 'Bimo Prakoso',
    email: 'bimo.prakoso@email.com',
    avatar: 'https://picsum.photos/seed/bimo/200/200',
    role: 'Freelancer',
    status: 'Inactive',
    joinDate: '27 Nov 2024',
    location: 'Surabaya, Indonesia',
    phone: '+62 857-1122-3344',
    skills: ['Flutter', 'Dart', 'Firebase'],
    totalOrders: 17,
    totalEarning: '$2,150',
    lastActive: '3 weeks ago',
    bio: 'Mobile developer spesialis cross-platform menggunakan Flutter. Telah menyelesaikan 17+ proyek dari berbagai industri.',
  },
  {
    id: 4,
    name: 'Nadya Kusuma',
    email: 'nadya.kusuma@email.com',
    avatar: 'https://picsum.photos/seed/nadya/200/200',
    role: 'Skomda Student',
    status: 'Active',
    joinDate: '18 Mar 2025',
    location: 'Yogyakarta, Indonesia',
    phone: '+62 878-5544-6677',
    skills: ['Graphic Design', 'Illustrator', 'Branding'],
    totalOrders: 5,
    totalEarning: '$380',
    lastActive: '30 minutes ago',
    bio: 'Mahasiswa komunikasi visual dengan passion di brand identity dan ilustrasi digital.',
  },
  {
    id: 5,
    name: 'Rizky Aditya',
    email: 'rizky.aditya@email.com',
    avatar: 'https://picsum.photos/seed/rizky/200/200',
    role: 'Freelancer',
    status: 'Active',
    joinDate: '5 Dec 2024',
    location: 'Medan, Indonesia',
    phone: '+62 813-6677-8899',
    skills: ['Node.js', 'Express', 'PostgreSQL'],
    totalOrders: 31,
    totalEarning: '$5,800',
    lastActive: '5 hours ago',
    bio: 'Full-stack engineer dengan keahlian mendalam di sistem backend. Suka membangun REST API yang scalable.',
  },
  {
    id: 6,
    name: 'Layla Permata',
    email: 'layla.permata@email.com',
    avatar: 'https://picsum.photos/seed/layla/200/200',
    role: 'Skomda Student',
    status: 'Suspended',
    joinDate: '9 Jan 2025',
    location: 'Makassar, Indonesia',
    phone: '+62 852-3344-5566',
    skills: ['Content Writing', 'SEO', 'Copywriting'],
    totalOrders: 2,
    totalEarning: '$90',
    lastActive: '2 months ago',
    bio: 'Content creator dengan kemampuan menulis persuasif dan artikel teroptimasi SEO.',
  },
];

// ─── MAPS ───
const USER_STATUS_MAP = {
  'Active':    'badge-completed',
  'Inactive':  'badge-pending',
  'Suspended': 'badge-new',
};
const USER_DOT_MAP = {
  'Active':    'online',
  'Inactive':  'offline',
  'Suspended': 'suspended',
};
const ROLE_ICON_MAP = {
  'Freelancer':     'ri-user-star-line',
  'Skomda Student': 'ri-graduation-cap-line',
};

// ─── RENDER USER CARDS ───
function renderUserCards(data = usersData) {
  const grid = document.getElementById('user-card-grid');
  if (!grid) return;

  if (!data || data.length === 0) {
    grid.innerHTML = `
      <div class="empty-state">
        <div class="empty-icon"><i class="ri-user-search-line"></i></div>
        <h3>No users found</h3>
        <p>Try adjusting the filter or search keyword.</p>
      </div>`;
    return;
  }

  grid.innerHTML = data.map(u => `
    <div class="user-card-item">
      <div class="user-card-top">
        <div class="user-card-avatar-wrap">
          <img class="user-card-avatar" src="${u.avatar}" alt="${u.name}" />
          <span class="user-online-dot ${USER_DOT_MAP[u.status] ?? 'offline'}"></span>
        </div>
        <span class="badge ${USER_STATUS_MAP[u.status] ?? 'badge-pending'} badge-inline">${u.status}</span>
      </div>

      <h3 class="user-card-name">${u.name}</h3>
      <span class="user-card-role">
        <i class="${ROLE_ICON_MAP[u.role] ?? 'ri-user-line'}"></i> ${u.role}
      </span>
      <span class="user-card-location">
        <i class="ri-map-pin-line"></i> ${u.location}
      </span>

      <div class="user-card-skills">
        ${u.skills.map(s => `<span class="skill-chip">${s}</span>`).join('')}
      </div>

      <div class="user-card-stats">
        <div class="user-stat-item">
          <span class="user-stat-value">${u.totalOrders}</span>
          <span class="user-stat-label">Orders</span>
        </div>
        <div class="user-stat-item">
          <span class="user-stat-value">${u.totalEarning}</span>
          <span class="user-stat-label">Earned</span>
        </div>
      </div>

      <button class="btn-detail" onclick="openUserModal(${u.id})">
        <i class="ri-eye-line"></i> Lihat Profil
      </button>
    </div>
  `).join('');
}

// ─── OPEN MODAL ───
function openUserModal(id) {
  const u = usersData.find(u => u.id === id);
  if (!u) return;

  const overlay = document.getElementById('user-modal-overlay');
  const box     = document.getElementById('user-modal-box');

  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closeUserModal()">
        <i class="ri-close-line"></i>
      </button>
      <div class="modal-avatar-wrap">
        <img class="modal-avatar" src="${u.avatar}" alt="${u.name}" />
      </div>
    </div>

    <div class="modal-body">
      <h2 class="modal-name">${u.name}</h2>
      <div class="modal-role-row">
        <span class="badge ${USER_STATUS_MAP[u.status] ?? 'badge-pending'} badge-inline">${u.status}</span>
        <span class="user-card-role" style="margin:0">
          <i class="${ROLE_ICON_MAP[u.role] ?? 'ri-user-line'}"></i> ${u.role}
        </span>
      </div>
      <p class="modal-bio">${u.bio}</p>

      <div class="modal-stats">
        <div class="modal-stat">
          <span class="modal-stat-value">${u.totalOrders}</span>
          <span class="modal-stat-label">Orders</span>
        </div>
        <div class="modal-stat">
          <span class="modal-stat-value">${u.totalEarning}</span>
          <span class="modal-stat-label">Earned</span>
        </div>
        <div class="modal-stat">
          <span class="modal-stat-value">${u.lastActive}</span>
          <span class="modal-stat-label">Last Active</span>
        </div>
      </div>

      <div class="modal-info-list">
        <div class="modal-info-row">
          <i class="ri-mail-line"></i>
          <span>${u.email}</span>
        </div>
        <div class="modal-info-row">
          <i class="ri-phone-line"></i>
          <span>${u.phone}</span>
        </div>
        <div class="modal-info-row">
          <i class="ri-map-pin-line"></i>
          <span>${u.location}</span>
        </div>
        <div class="modal-info-row">
          <i class="ri-calendar-line"></i>
          <span>Bergabung ${u.joinDate}</span>
        </div>
      </div>

      <p class="modal-section-title">Skills</p>
      <div class="modal-skills">
        ${u.skills.map(s => `<span class="modal-skill-chip">${s}</span>`).join('')}
      </div>

      <div class="modal-actions">
        <button class="modal-btn-primary">
          <i class="ri-mail-send-line"></i> Kirim Pesan
        </button>
        <button class="modal-btn-danger">
          <i class="ri-forbid-line"></i> Suspend
        </button>
      </div>
    </div>
  `;

  overlay.classList.add('open');
}

// ─── CLOSE MODAL ───
function closeUserModal() {
  document.getElementById('user-modal-overlay').classList.remove('open');
}

// ─── FILTER TABS ───
function initUserFilters() {
  const tabs = document.querySelectorAll('.filter-tab');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const filter = tab.dataset.filter;
      const filtered = filter === 'all'
        ? usersData
        : usersData.filter(u => u.status === filter || u.role === filter);
      renderUserCards(filtered);
    });
  });
}

// ─── SEARCH USER ───
function initUserSearch() {
  const input = document.getElementById('user-search-input');
  if (!input) return;
  input.addEventListener('input', () => {
    const q = input.value.toLowerCase();
    const filtered = usersData.filter(u =>
      u.name.toLowerCase().includes(q) ||
      u.email.toLowerCase().includes(q) ||
      u.role.toLowerCase().includes(q) ||
      u.location.toLowerCase().includes(q)
    );
    renderUserCards(filtered);
  });
}

// ─── INIT USER PAGE ───
function initUserPage() {
  renderUserCards();
  initUserFilters();
  initUserSearch();

  // Tutup modal saat klik overlay
  const overlay = document.getElementById('user-modal-overlay');
  if (overlay) {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeUserModal();
    });
  }

  // Notif
  const notifBtn = document.getElementById('notif-btn');
  if (notifBtn) {
    notifBtn.classList.toggle('has-unread', hasUnreadMessages);
    notifBtn.addEventListener('click', () => notifBtn.classList.remove('has-unread'));
  }

  initSidebarNav();
}

// Jalankan jika ada elemen user page
if (document.getElementById('user-card-grid')) {
  document.addEventListener('DOMContentLoaded', initUserPage);
}