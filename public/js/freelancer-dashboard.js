// ─── DATA ───
const latestOrders = [
  { title: 'Logo Design for Coffee Shop', client: 'Kopi Kenangan', amount: '$350', status: 'In Progress' },
  { title: 'Landing Page HTML/CSS',       client: 'StartUp Indo',  amount: '$800', status: 'Pending'     }
];

const jobOpportunities = [
  { title: 'Mobile App UI Redesign',         client: 'TechCorp',   amount: '$1,200', status: 'New' },
  { title: 'SEO Article Writing (10 Posts)', client: 'BlogMedia',  amount: '$200',   status: 'New' },
  { title: 'Wordpress Plugin Fix',           client: 'Agency One', amount: '$150',   status: 'New' }
];

const hasUnreadMessages = true;

// ─── BADGE MAP ───
const BADGE_MAP = {
  'In Progress': 'badge-progress',
  'Pending':     'badge-pending',
  'Completed':   'badge-completed',
  'New':         'badge-new'
};

// ─── NOTIFIKASI ───
function initNotification() {
  const notifBtn = document.getElementById('notif-btn');
  if (!notifBtn) {
    console.error("Element #notif-btn tidak ditemukan.");
    return;
  }

  notifBtn.classList.toggle('has-unread', hasUnreadMessages);

  notifBtn.addEventListener('click', () => {
    notifBtn.classList.remove('has-unread');
  });
}

// ─── RENDER KARTU ───
function renderList(gridEl, data, emptyMessage) {
  if (!gridEl) return;

  if (!data || data.length === 0) {
    gridEl.innerHTML = `
      <div class="empty-state">
        <i class="ri-inbox-archive-line" style="font-size: 32px; color: #94A3B8;"></i>
        <p style="color: #64748B; margin-top: 8px;">${emptyMessage}</p>
      </div>`;
    return;
  }

  gridEl.innerHTML = data.map(item => `
    <div class="project-card">
      <span class="badge ${BADGE_MAP[item.status] ?? 'badge-new'}">${item.status}</span>
      <h3 class="card-title-text">${item.title}</h3>
      <div class="card-footer-item">
        <span class="client-name">${item.client}</span>
        <span class="project-amount">${item.amount}</span>
      </div>
    </div>`
  ).join('');
}

// ─── INIT ───
function initFreelancerDashboard() {
  initNotification();
  renderList(document.getElementById('latest-order-grid'), latestOrders,      'Belum ada order aktif.');
  renderList(document.getElementById('job-opp-grid'),       jobOpportunities, 'Tidak ada lowongan saat ini.');
}

document.addEventListener('DOMContentLoaded', initFreelancerDashboard);