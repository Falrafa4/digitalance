let hasUnreadMessages = true;

let reviewsData = [
  {
    id: 'REV-001',
    order_id: 'ORD-001',
    rating: 5,
    comment: 'Pekerjaan sangat cepat dan hasilnya memuaskan. Komunikasi lancar selama project berlangsung.',
    created_at: '2026-02-21'
  },
  {
    id: 'REV-002',
    order_id: 'ORD-002',
    rating: 4,
    comment: 'Desainnya bagus, tapi ada sedikit revisi di bagian warna yang tidak sesuai brief awal. Secara keseluruhan oke.',
    created_at: '2026-02-23'
  },
  {
    id: 'REV-003',
    order_id: 'ORD-005',
    rating: 2,
    comment: 'Waktu pengerjaan sangat lambat, pelayanannya jelek dan desainnya sangat buruk. Kualitasnya parah.',
    created_at: '2026-02-26'
  }
];

function generateStars(rating) {
  let starsHtml = '<div class="stars-wrap">';
  for (let i = 1; i <= 5; i++) {
    starsHtml += `<i class="${i <= rating ? 'ri-star-fill' : 'ri-star-line empty'}"></i>`;
  }
  starsHtml += '</div>';
  return starsHtml;
}

function renderStats() {
  const row = document.getElementById('stats-row');
  if (!row) return;

  const total = reviewsData.length;
  const totalRating = reviewsData.reduce((acc, curr) => acc + curr.rating, 0);
  const avgRating = total === 0 ? 0 : (totalRating / total).toFixed(1);
  const fiveStars = reviewsData.filter(r => r.rating === 5).length;
  const lowStars = reviewsData.filter(r => r.rating <= 3).length;

  row.innerHTML = `
    <div class="stat-card">
      <div class="stat-icon blue"><i class="ri-chat-1-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${total}</span>
        <span class="stat-label">Total Review</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon amber"><i class="ri-star-fill"></i></div>
      <div class="stat-text">
        <span class="stat-value">${avgRating}</span>
        <span class="stat-label">Rata-rata Rating</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon teal"><i class="ri-thumb-up-fill"></i></div>
      <div class="stat-text">
        <span class="stat-value">${fiveStars}</span>
        <span class="stat-label">Review 5 Bintang</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon purple"><i class="ri-error-warning-line"></i></div>
      <div class="stat-text">
        <span class="stat-value">${lowStars}</span>
        <span class="stat-label">Rating Rendah (≤3)</span>
      </div>
    </div>
  `;
}

function renderCards(data = reviewsData) {
  const wrap = document.getElementById('review-cards-wrap');
  const emptyEl = document.getElementById('review-empty');
  if (!wrap) return;

  if (!data || data.length === 0) {
    wrap.style.display = 'none';
    emptyEl.style.display = 'block';
    return;
  }
  
  wrap.style.display = 'grid';
  emptyEl.style.display = 'none';

  wrap.innerHTML = data.map(r => `
    <div class="review-card">
      <div class="card-header">
        <div class="card-id-group">
          <span class="id-badge">${r.id}</span>
          <span class="ref-badge"><i class="ri-file-list-3-line"></i> ${r.order_id}</span>
        </div>
        <span class="card-date">${r.created_at}</span>
      </div>
      <div class="card-body">
        ${generateStars(r.rating)}
        <div class="card-comment" title="${r.comment}">${r.comment}</div>
      </div>
      <div class="card-footer">
        <div class="action-btns">
          <button class="btn-action" title="Detail Ulasan" onclick="openReviewModal('${r.id}')"><i class="ri-eye-line"></i></button>
          <button class="btn-action danger" title="Hapus Ulasan" onclick="deleteReview('${r.id}')"><i class="ri-delete-bin-line"></i></button>
        </div>
      </div>
    </div>
  `).join('');
}

function openReviewModal(id) {
  const r = reviewsData.find(x => x.id === id);
  if (!r) return;

  const overlay = document.getElementById('detail-modal-overlay');
  const box = document.getElementById('detail-modal-box');

  box.innerHTML = `
    <div class="modal-hero">
      <button class="modal-close" onclick="closeReviewModal()"><i class="ri-close-line"></i></button>
    </div>
    <div class="modal-body">
      <h2 class="modal-name">Detail Review</h2>
      
      <div class="modal-info-list">
        <div class="modal-info-row">
          <i class="ri-hashtag"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Review ID</span>${r.id}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-file-list-3-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Order ID</span>${r.order_id}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-star-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Rating</span>${generateStars(r.rating)}</div>
        </div>
        <div class="modal-info-row">
          <i class="ri-calendar-line"></i>
          <div><span style="display:block;font-weight:700;color:var(--slate-800)">Tanggal</span>${r.created_at}</div>
        </div>
      </div>

      <p class="modal-section-title">Komentar</p>
      <div class="comment-box">${r.comment}</div>

      <div class="modal-action-group">
        <button class="modal-btn-delete" onclick="deleteReview('${r.id}'); closeReviewModal();"><i class="ri-delete-bin-line"></i> Hapus</button>
      </div>
    </div>
  `;
  overlay.classList.add('open');
}

function closeReviewModal() {
  document.getElementById('detail-modal-overlay').classList.remove('open');
}

function deleteReview(id) {
  if (confirm(`Yakin ingin menghapus review ${id}?`)) {
    reviewsData = reviewsData.filter(r => r.id !== id);
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
  const input = document.getElementById('review-search-input');
  if (input) input.addEventListener('input', applyFilterAndSearch);
}

function applyFilterAndSearch() {
  const activeTab = document.querySelector('.filter-tab.active');
  const filter = activeTab ? activeTab.dataset.filter : 'all';
  const q = (document.getElementById('review-search-input')?.value || '').toLowerCase();

  let filtered = reviewsData;
  
  if (filter === '5') {
    filtered = filtered.filter(r => r.rating === 5);
  } else if (filter === '4') {
    filtered = filtered.filter(r => r.rating === 4);
  } else if (filter === 'low') {
    filtered = filtered.filter(r => r.rating <= 3);
  }

  if (q) {
    filtered = filtered.filter(r => 
      r.id.toLowerCase().includes(q) || 
      r.order_id.toLowerCase().includes(q) || 
      r.comment.toLowerCase().includes(q)
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

  const overlay = document.getElementById('detail-modal-overlay');
  if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) closeReviewModal(); });

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