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
      freelancer_name: o.freelancer_name ?? o.service?.freelancer?.user?.name ?? o.service?.freelancer?.name ?? '-',
      freelancer_id: o.freelancer_id ?? o.service?.freelancer?.id ?? '',
      
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

    tbody.innerHTML = data.map(o => `
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

        <div class="modal-action-group">
          <button class="modal-btn-delete" style="width:100%" onclick="deleteOrder('${o.id}'); closeOrderModal();"><i class="ri-delete-bin-line"></i> Hapus Order</button>
        </div>
      </div>
    `;
    overlay.classList.add('open');
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
      ? clientsList.map(c => `<option value="${c.id}" data-name="${c.name}">${c.name}</option>`).join('')
      : '<option value="" disabled>Data client tidak tersedia</option>';

    const freelancerOptions = freelancersList.length > 0 
      ? freelancersList.map(f => `<option value="${f.id}" data-name="${f.name}">${f.name}</option>`).join('')
      : '<option value="" disabled>Data freelancer tidak tersedia</option>';

    const serviceOptions = servicesList.length > 0 
      ? servicesList.map(s => `<option value="${s.id}" data-name="${s.title}">${s.title}</option>`).join('')
      : '<option value="" disabled>Data service tidak tersedia</option>';

    const statusOptions = STATUS_OPTIONS.map(s => `<option value="${s}">${s.replace('_', ' ')}</option>`).join('');

    el.innerHTML = `
      <div class="modal-content">
        <div class="modal-header">
          <h2>Tambah Order Baru</h2>
          <button class="close-modal" id="btn-close-add"><i class="ri-close-line"></i></button>
        </div>
        <form id="form-add-order">
          <div class="form-row">
            <div class="form-group">
              <label>Client</label>
              <select id="add-client-id" required>
                <option value="" disabled selected>Pilih Client...</option>
                ${clientOptions}
              </select>
            </div>
            <div class="form-group">
              <label>Freelancer</label>
              <select id="add-free-id" required>
                <option value="" disabled selected>Pilih Freelancer...</option>
                ${freelancerOptions}
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Service</label>
              <select id="add-srv-id" required>
                <option value="" disabled selected>Pilih Layanan...</option>
                ${serviceOptions}
              </select>
            </div>
            <div class="form-group">
              <label>Agreed Price</label>
              <input type="number" id="add-price" placeholder="Contoh: 500000" required />
            </div>
          </div>
          <div class="form-group">
            <label>Status Awal</label>
            <select id="add-status">${statusOptions}</select>
          </div>
          <div class="form-group">
            <label>Brief Pekerjaan</label>
            <textarea id="add-brief" rows="3" placeholder="Jelaskan detail pesanan..." required></textarea>
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
    
    el.querySelector('#form-add-order').addEventListener('submit', (e) => {
      e.preventDefault();
      
      const clientEl = el.querySelector('#add-client-id');
      const freeEl = el.querySelector('#add-free-id');
      const srvEl = el.querySelector('#add-srv-id');

      const newOrder = normalizeOrder({
        id: 'ORD-' + Math.floor(Math.random() * 10000),
        client_id: clientEl.value,
        client_name: clientEl.options[clientEl.selectedIndex].getAttribute('data-name'),
        freelancer_id: freeEl.value,
        freelancer_name: freeEl.options[freeEl.selectedIndex].getAttribute('data-name'),
        service_id: srvEl.value,
        service_name: srvEl.options[srvEl.selectedIndex].getAttribute('data-name'),
        brief: el.querySelector('#add-brief').value,
        status: el.querySelector('#add-status').value,
        agreed_price: parseInt(el.querySelector('#add-price').value),
        created_at: new Date().toISOString()
      });

      ordersData.unshift(newOrder);
      closeFn();
      refreshUI();
    });
  }

  /**
   * 6. DELETE & FILTERING
   */
  window.deleteOrder = function(id) {
    if (confirm(`Apakah Anda yakin ingin menghapus order #${id}?`)) {
      ordersData = ordersData.filter(o => String(o.id) !== String(id));
      refreshUI();
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
        applyFilterAndSearch();
      });
    });

    // Event Listener Search Input
    const searchInput = document.getElementById('order-search-input');
    if (searchInput) {
      searchInput.addEventListener('input', applyFilterAndSearch);
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