<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Offers & Negotiations | Digitalance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/admin-offers.css" />
  </head>
  <body>
    <div class="app">
      <aside class="sidebar">
        <div class="sidebar-header">
          <div class="logo">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect width="32" height="32" rx="10" fill="url(#logo-gradient)" />
              <path d="M16 7L25 11.5V20.5L16 25L7 20.5V11.5L16 7Z" fill="white" />
              <defs>
                <linearGradient id="logo-gradient" x1="0" y1="0" x2="32" y2="32">
                  <stop stop-color="#0F766E" />
                  <stop offset="1" stop-color="#10B981" />
                </linearGradient>
              </defs>
            </svg>
            <span>Digitalance</span>
          </div>
        </div>

        <nav class="nav nav-scroll">
          <button class="nav-item"><i class="ri-dashboard-fill"></i> Dashboard</button>
          <button class="nav-item"><i class="ri-user-line"></i> User</button>
          <button class="nav-item"><i class="ri-user-star-line"></i> Admin</button>
          <button class="nav-item"><i class="ri-file-list-3-line"></i> Orders</button>
          <button class="nav-item"><i class="ri-star-line"></i> Reviews</button>
          <button class="nav-item"><i class="ri-tools-line"></i> Services</button>
          <button class="nav-item"><i class="ri-bank-card-line"></i> Transactions</button>
          <button class="nav-item"><i class="ri-folder-user-line"></i> Portofolios</button>
          <button class="nav-item active"><i class="ri-price-tag-3-line"></i> Offers</button>
          <button class="nav-item"><i class="ri-hammer-line"></i> Working</button>
          <button class="nav-item"><i class="ri-discuss-line"></i> Negotiations</button>
        </nav>

        <div class="sidebar-footer">
          <div class="nav-divider"></div>
          <nav class="nav">
            <button class="nav-item"><i class="ri-user-line"></i> Account</button>
            <button class="nav-item"><i class="ri-settings-3-line"></i> Settings</button>
            <button class="nav-item text-danger"><i class="ri-logout-box-line"></i> Logout</button>
          </nav>
          <div class="copyright">&copy; 2026 Digitalance.<br />All rights reserved.</div>
        </div>
      </aside>

      <main class="main">
        <header class="main-header">
          <div class="search-wrap">
            <i class="ri-search-line search-icon"></i>
            <i class="ri-equalizer-line" style="position:absolute;right:16px;top:50%;transform:translateY(-50%);color:#94a3b8;cursor:pointer;"></i>
            <input class="search-input" id="global-search-input" type="text" placeholder="Cari ID, nama pengirim, atau isi pesan..." />
          </div>
          <div class="header-right">
            <button class="btn-icon" id="notif-btn">
              <i class="ri-notification-3-line"></i>
              <span class="notif-dot"></span>
            </button>
            <div class="user-card">
              <img class="user-avatar" src="https://picsum.photos/seed/admin/100/100" alt="Admin Profile" />
              <div class="user-info">
                <span class="user-name">Reva</span>
                <span class="user-role">System Manager</span>
              </div>
            </div>
          </div>
        </header>

        <div class="page-header">
          <div class="page-header-left">
            <h1>Offers & Negotiations</h1>
            <p>Pantau tawaran jasa masuk dan log pesan negosiasi antar pengguna.</p>
          </div>
        </div>

        <div class="stats-row" id="stats-row"></div>

        <div class="section-tabs">
          <button class="section-tab active" data-target="offers-section"><i class="ri-price-tag-3-line"></i> Data Tawaran (Offers)</button>
          <button class="section-tab" data-target="nego-section"><i class="ri-discuss-line"></i> Pesan Negosiasi</button>
        </div>

        <div id="offers-section" class="tab-content active">
          <div class="filter-tabs" id="offers-filters">
            <button class="filter-tab active" data-filter="all">Semua</button>
            <button class="filter-tab" data-filter="pending">Pending</button>
            <button class="filter-tab" data-filter="accepted">Accepted</button>
            <button class="filter-tab" data-filter="rejected">Rejected</button>
          </div>
          <div class="table-wrap">
            <table class="data-table" id="offers-table">
              <thead>
                <tr>
                  <th>Offer ID</th>
                  <th>Klien & Freelancer</th>
                  <th>Detail Tawaran</th>
                  <th>Harga & Waktu</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="offers-tbody"></tbody>
            </table>
            <div id="offers-empty" class="empty-state" style="display:none;">
              <div class="empty-icon"><i class="ri-price-tag-3-line"></i></div>
              <h3>Tidak ada tawaran ditemukan</h3>
              <p>Coba ubah kata kunci pencarian.</p>
            </div>
          </div>
        </div>

        <div id="nego-section" class="tab-content">
          <div class="table-wrap">
            <table class="data-table" id="nego-table">
              <thead>
                <tr>
                  <th>Nego ID</th>
                  <th>Order ID Terkait</th>
                  <th>Pengirim (Sender)</th>
                  <th>Pesan Negosiasi</th>
                  <th>Tanggal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="nego-tbody"></tbody>
            </table>
            <div id="nego-empty" class="empty-state" style="display:none;">
              <div class="empty-icon"><i class="ri-discuss-line"></i></div>
              <h3>Tidak ada negosiasi ditemukan</h3>
              <p>Coba ubah kata kunci pencarian.</p>
            </div>
          </div>
        </div>

      </main>
    </div>

    <div class="modal-overlay" id="detail-modal-overlay">
      <div class="modal-box" id="detail-modal-box"></div>
    </div>

    <script src="../assets/js/admin-offers.js"></script>
  </body>
</html>