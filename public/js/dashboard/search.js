document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('search-input');
  const dropdown = document.getElementById('search-dropdown');
  const results = document.getElementById('search-results');

  if (!input || !dropdown || !results) return;

  // daftar menu admin
  const menus = [
  { name: 'Dasbor', url: '/admin' },
  { name: 'Pengguna', url: '/admin/clients' },
  { name: 'Pesanan', url: '/admin/orders' },
  { name: 'Sedang Dikerjakan', url: '/admin/orders?status=in_progress' },
  { name: 'Layanan', url: '/admin/services' },
  { name: 'Penawaran', url: '/admin/offers' },
  { name: 'Transaksi', url: '/admin/transactions' },
  { name: 'Pengaturan', url: '/admin/settings' },
  { name: 'Akun', url: '/admin/profile' },
];

  input.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();

    if (!q) {
      dropdown.classList.add('hidden');
      results.innerHTML = '';
      return;
    }

    const filtered = menus.filter(m => m.name.toLowerCase().includes(q));

    if (!filtered.length) {
      results.innerHTML = `<div class="px-4 py-3 text-sm text-slate-400 flex items-center gap-2">
        <i class="ri-search-line"></i> Tidak ada menu yang cocok
      </div>`;
    } else {
      results.innerHTML = filtered.map(m => `
        <a href="${m.url}" class="block px-4 py-2 text-sm hover:bg-slate-100">
          ${m.name}
        </a>
      `).join('');
    }

    dropdown.classList.remove('hidden');
  });

  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const q = this.value.trim();
        if (q) {
            window.location.href = `/admin/search?q=${encodeURIComponent(q)}`;
        }
    }
  });

  // klik luar → tutup dropdown
  document.addEventListener('click', (e) => {
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.classList.add('hidden');
    }
  });
});