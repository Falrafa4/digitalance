// ─── DATA ───
const projects = [
  { title: 'Website Redesign',       client: 'Acme Corp',  amount: 'Rp5.000',  status: 'In Progress' },
  { title: 'Mobile App Development', client: 'Beta LLC',   amount: 'Rp12.000', status: 'Pending'     },
  { title: 'SEO Optimization',       client: 'Gamma Inc',  amount: 'Rp3.000',  status: 'Completed'   }
];

const hasUnreadMessages = true;

// ─── BADGE MAP ───
const BADGE_MAP = {
  'In Progress': 'badge-progress',
  'Pending':     'badge-pending',
  'Completed':   'badge-completed'
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

// ─── RENDER PROJECT CARDS ───
function renderProjects() {
  const grid = document.getElementById('project-grid');
  if (!grid) return;

  if (!projects || projects.length === 0) {
    grid.innerHTML = `
      <div class="empty-state">
        <div class="empty-icon"><i class="ri-folder-open-line"></i></div>
        <h3>No projects found</h3>
        <p>It seems you don't have any active projects right now.</p>
        <button class="btn-empty">Post New Project</button>
      </div>`;
    return;
  }

  grid.innerHTML = projects.map(p => `
    <div class="project-card">
      <span class="badge ${BADGE_MAP[p.status] ?? 'badge-pending'}">${p.status}</span>
      <h3 class="card-title-text">${p.title}</h3>
      <div class="card-footer-item">
        <span class="client-name">${p.client}</span>
        <span class="project-amount">${p.amount}</span>
      </div>
    </div>`
  ).join('');
}

// ─── INIT ───
function initDashboard() {
  initNotification();
  renderProjects();
}

document.addEventListener('DOMContentLoaded', initDashboard);