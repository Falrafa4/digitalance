(() => {
  let hasUnreadMessages = true;

  /**
   * 1. DATA NORMALIZER
   * Fungsi ini sangat penting untuk menyamakan format data.
   * Baik data yang datang dari database (Eloquent Laravel) maupun data dari input form
   * akan diproses menjadi satu format objek yang seragam.
   */
  function normalizeOrder(o) {
    return {
      ...o,
      // Pastikan ID selalu string untuk perbandingan yang aman
      id: String(o.id),
      
      // Deteksi Nama Client: Cek relasi Laravel (client.user.name) atau properti langsung
      client_name: o.client_name ?? o.client?.user?.name ?? o.client?.name ?? '-',
      client_id: o.client_id ?? o.client?.id ?? '',
      
      // Deteksi Nama Freelancer: Cek relasi Laravel atau properti langsung
      freelancer_name: o.freelancer?.user?.name ?? o.freelancer?.name ?? o.freelancer_name ?? o.service?.freelancer?.user?.name ?? o.service?.freelancer?.name ?? '-',
      freelancer_id: o.freelancer_id ?? o.freelancer?.id ?? o.service?.freelancer?.id ?? '',
      
      // Deteksi Nama Service
      service_name: o.service_name ?? o.service?.title ?? o.service_id ?? '-',
      service_id: o.service_id ?? o.service?.id ?? '',
      
      // Seragamkan format status (kecil, tanpa spasi)
      status: String(o.status || 'pending').toLowerCase().replace(/\s+/g, '_'),
      
      // Format tanggal untuk tampilan tabel
      display_date: o.created_at ? new Date(o.created_at).toLocaleDateString('id-ID') : '-'
    };
  }

  // Ambil data utama dan data dropdown dari window object (yang di-pass dari Blade)
  const pageData = window.__ORDERS_PAGE__ || {};
  const rawOrders = pageData.data || [];
  
  // List untuk Dropdown (Ambil dari data yang dikirim Controller)
  const clientsList = pageData.clients || [];
  const freelancersList = pageData.freelancers || [];
  const servicesList = pageData.services || []; 

  // Inisialisasi data dengan normalisasi
  let ordersData = (Array.isArray(rawOrders) ? rawOrders : []).map(normalizeOrder);

  let currentPage = 1;
  let itemsPerPage = 10;

  const STATUS_OPTIONS = ['pending', 'negotiated', 'paid', 'in_progress', 'revision', 'completed', 'cancelled'];

  // Helper format mata uang
  function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', { 
      style: 'currency', 
      currency: 'IDR', 
      minimumFractionDigits: 0 
    }).format(number);
  }

  /**
   * 2. RENDER STATS
   * Menghitung total dan status untuk ditampilkan di card atas.
   */
  function renderStats() {
    const row = document.getElementById('stats-row');
    if (!row) return;

    const total = ordersData.length;
    const inProgress = ordersData.filter(o => o.status === 'in_progress').length;
    const completed = ordersData.filter(o => o.status === 'completed').length;
    const pending = ordersData.filter(o => o.status === 'pending').length;

    row.innerHTML = `
      <div class="stat-card">
        <div class="stat-icon blue"><i class="ri-file-list-3-line"></i></div>
        <div class="stat-text">
          <span class="stat-value">${total}</span>
          <span class="stat-label">Total Orders</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon teal"><i class="ri-loader-4-line"></i></div>
        <div class="stat-text">
          <span class="stat-value">${inProgress}</span>
          <span class="stat-label">In Progress</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple"><i class="ri-check-double-line"></i></div>
        <div class="stat-text">
          <span class="stat-value">${completed}</span>
          <span class="stat-label">Completed</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon amber"><i class="ri-time-line"></i></div>
        <div class="stat-text">
          <span class="stat-value">${pending}</span>
          <span class="stat-label">Pending</span>
        </div>
      </div>
    `;
  }

  /**
   * 3. RENDER TABLE
   * Menampilkan data ke dalam tabel HTML.
   */
  function renderTable(data = ordersData) {
    const tbody = document.getElementById('order-tbody');
    const emptyEl = document.getElementById('order-empty');
    const tableEl = document.getElementById('order-table');
    if (!tbody) return;

    if (!data || data.length === 0) {
      tableEl.style.display = 'none';
      emptyEl.style.display = 'block';
      return;
    }
    tableEl.style.display = 'table';
    emptyEl.style.display = 'none';

    const totalPages = Math.ceil(data.length / itemsPerPage);
    if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
    const startIndex = (currentPage - 1) * itemsPerPage;
    const paginatedData = data.slice(startIndex, startIndex + itemsPerPage);

    tbody.innerHTML = paginatedData.map(o => `
      <tr>
        <td><span class="order-id-badge">#${o.id}</span></td>
        <td>
          <div class="user-pair">
            <span class="user-name"><i class="ri-user-line"></i> ${o.client_name}</span>
            <span class="user-name"><i class="ri-briefcase-line"></i> ${o.freelancer_name}</span>
          </div>
        </td>
        <td>
          <div class="user-pair">
            <span class="user-name" title="${o.service_name}">${o.service_name.slice(0, 25)}${o.service_name.length > 25 ? '...' : ''}</span>
          </div>
        </td>
        <td><span class="price-text">${formatRupiah(o.agreed_price || 0)}</span></td>
        <td><span class="status-pill status-${o.status}">${o.status.replace('_', ' ')}</span></td>
        <td>${o.display_date}</td>
        <td>
          <div class="action-btns">
            <button class="btn-action" title="Detail" onclick="openOrderModal('${o.id}')"><i class="ri-eye-line"></i></button>
          </div>
        </td>
      </tr>
    `).join('');

    renderPaginationControls(totalPages);
  }

  function renderPaginationControls(totalPages) {
    let wrap = document.getElementById('pagination-wrap');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'pagination-wrap';
        wrap.className = 'flex justify-end gap-2 mt-4 px-6';
        document.getElementById('order-table').parentNode.appendChild(wrap);
    }
    
    if (totalPages <= 1) {
        wrap.innerHTML = '';
        return;
    }

    let html = '';
    html += `<button class="px-3 py-1 rounded border border-slate-200 bg-white text-sm hover:bg-slate-50 disabled:opacity-50" ${currentPage === 1 ? 'disabled' : ''} onclick="window.changeOrderPage(${currentPage - 1})">Prev</button>`;
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `<button class="px-3 py-1 rounded border ${i === currentPage ? 'bg-[#0f766e] text-white border-[#0f766e]' : 'border-slate-200 bg-white hover:bg-slate-50'} text-sm" onclick="window.changeOrderPage(${i})">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<span class="px-2 py-1 text-slate-400">...</span>`;
        }
    }
    html += `<button class="px-3 py-1 rounded border border-slate-200 bg-white text-sm hover:bg-slate-50 disabled:opacity-50" ${currentPage === totalPages ? 'disabled' : ''} onclick="window.changeOrderPage(${currentPage + 1})">Next</button>`;
    wrap.innerHTML = html;
  }

  window.changeOrderPage = function(page) {
      currentPage = page;
      applyFilterAndSearch();
  };

  function getCsrfToken() {
      return document.querySelector('meta[name="csrf-token"]')?.content || 
             document.querySelector('input[name="_token"]')?.value || '';
  }

  function showToast(msg, type = 'success') {
      if (window.showToast) return window.showToast(msg, type);
      alert(msg);
  }

  /**
   * 4. DETAIL MODAL
   */
  window.openOrderModal = function(id) {
    const o = ordersData.find(x => String(x.id) === String(id));
    if (!o) return;
    
    const overlay = document.getElementById('detail-modal-overlay');
    const box = document.getElementById('detail-modal-box');

    box.innerHTML = `
      <div class="modal-hero">
        <button class="modal-close" onclick="closeOrderModal()"><i class="ri-close-line"></i></button>
      </div>
      <div class="modal-body">
        <h2 class="modal-name">Order #${o.id}</h2>
        <div class="modal-role-row">
          <span class="status-pill status-${o.status}">${o.status.replace('_', ' ')}</span>
        </div>
        
        <div class="modal-info-list">
          <div class="modal-info-row">
            <i class="ri-user-line"></i>
            <div><span style="display:block;font-weight:700;color:var(--slate-800)">Client</span>${o.client_name}</div>
          </div>
          <div class="modal-info-row">
            <i class="ri-briefcase-line"></i>
            <div><span style="display:block;font-weight:700;color:var(--slate-800)">Freelancer</span>${o.freelancer_name}</div>
          </div>
          <div class="modal-info-row">
            <i class="ri-tools-line"></i>
            <div><span style="display:block;font-weight:700;color:var(--slate-800)">Service</span>${o.service_name}</div>
          </div>
          <div class="modal-info-row">
            <i class="ri-money-dollar-circle-line"></i>
            <div><span style="display:block;font-weight:700;color:var(--slate-800)">Agreed Price</span>${formatRupiah(o.agreed_price)}</div>
          </div>
        </div>

        <p class="modal-section-title">Brief</p>
        <div class="brief-box">${o.brief || '-'}</div>

        <p class="modal-section-title mt-4">Ubah Status</p>
        <div style="display: flex; gap: 8px; margin-bottom: 20px;">
          <select id="modal-update-status" class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none">
            ${STATUS_OPTIONS.map(s => {
                const label = s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                return `<option value="${label}" ${o.status === s ? 'selected' : ''}>${label}</option>`;
            }).join('')}
          </select>
          <button onclick="updateOrderStatus('${o.id}')" class="bg-teal-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-teal-700 transition-all">Update</button>
        </div>

        <div class="modal-action-group">
          <button class="modal-btn-delete" style="width:100%" onclick="deleteOrder('${o.id}'); closeOrderModal();"><i class="ri-delete-bin-line"></i> Hapus Order</button>
        </div>
      </div>
    `;
    overlay.classList.add('open');
  };

  window.updateOrderStatus = async function(id) {
      const newStatus = document.getElementById('modal-update-status').value;
      try {
          const res = await fetch(`/admin/orders/${id}/status`, {
              method: 'POST', // Use POST with _method=PATCH for Laravel forms, or just POST if the route accepts it
              headers: {
                  'X-CSRF-TOKEN': getCsrfToken(),
                  'Content-Type': 'application/json',
                  'Accept': 'application/json'
              },
              body: JSON.stringify({ status: newStatus })
          });
          
          if (!res.ok && res.status !== 200 && res.status !== 302) {
              throw new Error('Gagal mengupdate status');
          }

          const o = ordersData.find(x => String(x.id) === String(id));
          if (o) o.status = newStatus.toLowerCase().replace(/\s+/g, '_');
          
          showToast('Status berhasil diupdate', 'success');
          closeOrderModal();
          refreshUI();
      } catch (e) {
          showToast(e.message, 'danger');
      }
  };

  window.closeOrderModal = function() {
    document.getElementById('detail-modal-overlay').classList.remove('open');
  };

  /**
   * 5. ADD ORDER MODAL (Dengan Dropdown Dinamis)
   */
  window.openAddOrderModal = function() {
    const existing = document.getElementById('add-order-overlay');
    if (existing) existing.remove();

    const el = document.createElement('div');
    el.className = 'modal-overlay';
    el.id = 'add-order-overlay';
    
    // Generate Pilihan Dropdown
    const clientOptions = clientsList.length > 0 
      ? clientsList.map(c => `<option value="${c.id}" data-name="${c.name || c.user?.name}">${c.name || c.user?.name}</option>`).join('')
      : '<option value="" disabled>Data client tidak tersedia</option>';

    const serviceOptions = servicesList.length > 0 
      ? servicesList.map(s => `<option value="${s.id}" data-name="${s.title}">${s.title}</option>`).join('')
      : '<option value="" disabled>Data service tidak tersedia</option>';

    const statusOptions = STATUS_OPTIONS.map(s => {
        // format options ke format title case
        const label = s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        // status di controller expect enum: Pending,Negotiated,Paid,In Progress,Revision,Completed,Cancelled
        return `<option value="${label}">${label}</option>`;
    }).join('');

    el.innerHTML = `
      <div class="modal-content">
        <div class="modal-header">
          <h2>Tambah Order Baru</h2>
          <button class="close-modal" type="button" id="btn-close-add"><i class="ri-close-line"></i></button>
        </div>
        <form id="form-add-order" action="/admin/orders" method="POST">
          <input type="hidden" name="_token" value="${getCsrfToken()}">
          <div class="form-row">
            <div class="form-group">
              <label>Client</label>
              <select name="client_id" required>
                <option value="" disabled selected>Pilih Client...</option>
                ${clientOptions}
              </select>
            </div>
            <div class="form-group">
              <label>Service</label>
              <select name="service_id" required>
                <option value="" disabled selected>Pilih Layanan...</option>
                ${serviceOptions}
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Agreed Price</label>
              <input type="number" name="agreed_price" placeholder="Contoh: 500000" />
            </div>
            <div class="form-group">
              <label>Status Awal</label>
              <select name="status">${statusOptions}</select>
            </div>
          </div>
          <div class="form-group">
            <label>Brief Pekerjaan</label>
            <textarea name="brief" rows="3" placeholder="Jelaskan detail pesanan..." required></textarea>
          </div>
          <div class="modal-actions">
            <button type="button" class="btn-secondary" id="btn-cancel-add">Batal</button>
            <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Simpan Order</button>
          </div>
        </form>
      </div>
    `;
    
    document.body.appendChild(el);
    requestAnimationFrame(() => el.classList.add('open'));

    const closeFn = () => { el.classList.remove('open'); setTimeout(() => el.remove(), 280); };
    el.querySelector('#btn-close-add').addEventListener('click', closeFn);
    el.querySelector('#btn-cancel-add').addEventListener('click', closeFn);
  }

  /**
   * 6. DELETE & FILTERING
   */
  window.deleteOrder = async function(id) {
    if (await customConfirm(`Apakah Anda yakin ingin menghapus order #${id}?`)) {
      try {
          const res = await fetch(`/admin/orders/${id}`, {
              method: 'DELETE',
              headers: {
                  'X-CSRF-TOKEN': getCsrfToken(),
                  'Accept': 'application/json'
              }
          });
          
          if (!res.ok) {
              // Jika controller melempar redirect (seperti default Laravel), fetch akan mengikuti dan res.ok = true biasanya
              // tapi kalo failed akan nembak throw
              if(res.status !== 200 && res.status !== 302) throw new Error('Gagal menghapus order');
          }

          ordersData = ordersData.filter(o => String(o.id) !== String(id));
          showToast('Order berhasil dihapus', 'success');
          refreshUI();
      } catch (e) {
          showToast(e.message, 'danger');
      }
    }
  };

  function applyFilterAndSearch() {
    const activeTab = document.querySelector('.filter-tab.active');
    const filter = activeTab ? activeTab.dataset.filter : 'all';
    const q = (document.getElementById('order-search-input')?.value || '').toLowerCase();

    let filtered = ordersData;
    
    // Filter Berdasarkan Status
    if (filter !== 'all') {
      filtered = filtered.filter(o => o.status === filter);
    }
    
    // Filter Berdasarkan Pencarian (Search)
    if (q) {
      filtered = filtered.filter(o => 
        o.id.toLowerCase().includes(q) || 
        o.client_name.toLowerCase().includes(q) || 
        o.freelancer_name.toLowerCase().includes(q) ||
        o.service_name.toLowerCase().includes(q)
      );
    }
    renderTable(filtered);
  }

  function refreshUI() {
    renderStats();
    applyFilterAndSearch();
  }

  /**
   * 7. INITIALIZE PAGE
   */
  function initPage() {
    renderStats();
    renderTable();

    // Event Listener Filter Tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        currentPage = 1;
        applyFilterAndSearch();
      });
    });

    // Event Listener Search Input
    const searchInput = document.getElementById('order-search-input');
    if (searchInput) {
      searchInput.addEventListener('input', () => { currentPage = 1; applyFilterAndSearch(); });
    }

    // Event Listener Add Button
    const btnAdd = document.getElementById('btn-add-order');
    if (btnAdd) {
      btnAdd.addEventListener('click', window.openAddOrderModal);
    }

    // Close Modal on Overlay Click
    const overlay = document.getElementById('detail-modal-overlay');
    if (overlay) {
      overlay.addEventListener('click', (e) => { 
        if (e.target === overlay) closeOrderModal(); 
      });
    }
  }

  // Jalankan saat dokumen siap
  document.addEventListener('DOMContentLoaded', initPage);

})();