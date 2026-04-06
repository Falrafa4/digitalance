(() => {
  // DATA DARI BLADE
  const rawReviews = window.__REVIEWS_PAGE__?.data;
  let reviewsData = Array.isArray(rawReviews) ? rawReviews : (rawReviews?.data || []);

  // GENERATE STARS
  function generateStars(rating) {
    let starsHtml = '<div class="stars-wrap">';
    const safeRating = Number(rating) || 0;
    for (let i = 1; i <= 5; i++) {
      starsHtml += `<i class="${i <= safeRating ? 'ri-star-fill' : 'ri-star-line empty'}"></i>`;
    }
    starsHtml += '</div>';
    return starsHtml;
  }

  // RENDER STATS
  function renderStats() {
    const row = document.getElementById('stats-row');
    if (!row) return;

    const total = reviewsData.length;
    const totalRating = reviewsData.reduce((acc, curr) => acc + (Number(curr.rating) || 0), 0);
    const avgRating = total === 0 ? 0 : (totalRating / total).toFixed(1);
    const fiveStars = reviewsData.filter(r => Number(r.rating) === 5).length;
    const lowStars = reviewsData.filter(r => Number(r.rating) <= 3).length;

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

  // RENDER CARDS
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

    wrap.innerHTML = data.map(r => {
      const date = r.created_at ? new Date(r.created_at).toLocaleDateString('id-ID') : '-';
      
      return `
      <div class="review-card animate-fadeUp">
        <div class="card-header">
          <div class="card-id-group">
            <span class="id-badge">#${r.id}</span>
            <span class="ref-badge"><i class="ri-file-list-3-line"></i> #${r.order_id ?? '-'}</span>
          </div>
          <span class="card-date">${date}</span>
        </div>
        <div class="card-body">
          ${generateStars(r.rating)}
          <div class="card-comment" title="${r.comment || '-'}">${r.comment || 'Tidak ada komentar'}</div>
        </div>
        <div class="card-footer">
          <div class="action-btns">
            <button class="btn-action" title="Detail Ulasan" onclick="openReviewModal('${r.id}')"><i class="ri-eye-line"></i></button>
            <button class="btn-action btn-delete" title="Hapus Ulasan" onclick="deleteReview('${r.id}')" style="color: #ef4444;"><i class="ri-delete-bin-line"></i></button>
          </div>
        </div>
      </div>
    `}).join('');
  }

  // OPEN REVIEW MODAL
  window.openReviewModal = function(id) {
    const r = reviewsData.find(x => String(x.id) === String(id));
    if (!r) return;

    const overlay = document.getElementById('detail-modal-overlay');
    const box = document.getElementById('detail-modal-box');
    const date = r.created_at ? new Date(r.created_at).toLocaleString('id-ID') : '-';

    box.innerHTML = `
      <div class="modal-hero">
        <button class="modal-close" onclick="closeReviewModal()"><i class="ri-close-line"></i></button>
      </div>
      <div class="modal-body">
        <h2 class="modal-name">Detail Review</h2>
        
        <div class="modal-info-list">
          <div class="modal-info-row">
            <i class="ri-hashtag"></i>
            <div><span style="display:block;font-weight:700;color:var(--slate-800)">Review ID</span>#${r.id}</div>
          </div>
          <div class="modal-info-row">
            <i class="ri-file-list-3-line"></i>
            <div><span style="display:block;font-weight:700;color:var(--slate-800)">Order ID</span>#${r.order_id ?? '-'}</div>
          </div>
          <div class="modal-info-row">
            <i class="ri-star-line"></i>
            <div><span style="display:block;font-weight:700;color:var(--slate-800)">Rating</span>${generateStars(r.rating)}</div>
          </div>
          <div class="modal-info-row">
            <i class="ri-calendar-line"></i>
            <div><span style="display:block;font-weight:700;color:var(--slate-800)">Tanggal Diberikan</span>${date}</div>
          </div>
        </div>

        <p class="modal-section-title">Komentar</p>
        <div class="comment-box">${r.comment || 'Tidak ada komentar yang ditinggalkan.'}</div>

        <div class="modal-action-group" style="margin-top: 24px;">
          <button class="modal-btn-delete" style="width: 100%; justify-content: center;" onclick="deleteReview('${r.id}'); closeReviewModal();">
            <i class="ri-delete-bin-line"></i> Hapus Ulasan Permanen
          </button>
        </div>
      </div>
    `;
    overlay.classList.add('open');
  }

  // CLOSE REVIEW MODAL
  window.closeReviewModal = function() {
    document.getElementById('detail-modal-overlay').classList.remove('open');
  }

  // DELETE REVIEW
  window.deleteReview = function(id) {
    if (confirm(`Apakah Anda yakin ingin menghapus ulasan #${id}?`)) {
      reviewsData = reviewsData.filter(r => String(r.id) !== String(id));
      refreshUI();
    }
  }

  // APPLY FILTER ONLY
  function applyFilter() {
    const activeTab = document.querySelector('.filter-tab.active');
    const filter = activeTab ? activeTab.dataset.filter : 'all';

    let filtered = [...reviewsData];
    
    if (filter === '5') {
      filtered = filtered.filter(r => Number(r.rating) === 5);
    } else if (filter === '4') {
      filtered = filtered.filter(r => Number(r.rating) === 4);
    } else if (filter === 'low') {
      filtered = filtered.filter(r => Number(r.rating) <= 3);
    }
    
    renderCards(filtered);
  }

  // REFRESH UI
  function refreshUI() {
    renderStats();
    applyFilter();
  }

  // INITIALIZE PAGE
  function initPage() {
    renderStats();
    renderCards();
    
    // Filter Tabs Events
    const tabs = document.querySelectorAll('.filter-tab');
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        applyFilter();
      });
    });

    // Close Modal on Overlay Click
    const overlay = document.getElementById('detail-modal-overlay');
    if (overlay) {
      overlay.addEventListener('click', (e) => { 
        if (e.target === overlay) closeReviewModal(); 
      });
    }
  }

  document.addEventListener('DOMContentLoaded', initPage);
})();