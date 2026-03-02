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
const pendingVerifications = [];

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
  renderPendingVerifications();
}

// Jalankan ketika DOM siap
document.addEventListener('DOMContentLoaded', initAdminDashboard);