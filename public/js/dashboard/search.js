document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('search-input');
  const dropdown = document.getElementById('search-dropdown');
  const results = document.getElementById('search-results');

  if (!input || !dropdown || !results) return;

  // daftar menu admin
  const menus = [
  { name: 'Dashboard', url: '/admin' },
  { name: 'Users', url: '/admin/clients' },
  { name: 'Orders', url: '/admin/orders' },
  { name: 'Working', url: '/admin/orders?status=in_progress' },
  { name: 'Services', url: '/admin/services' },
  { name: 'Offers', url: '/admin/offers' },
  { name: 'Transactions', url: '/admin/transactions' },
  { name: 'Settings', url: '/admin/settings' },
  { name: 'Account', url: '/admin/profile' },
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
      results.innerHTML = `<div class="px-4 py-2 text-sm text-slate-400">No results</div>`;
    } else {
      results.innerHTML = filtered.map(m => `
        <a href="${m.url}" class="block px-4 py-2 text-sm hover:bg-slate-100">
          ${m.name}
        </a>
      `).join('');
    }

    dropdown.classList.remove('hidden');
  });

  // klik luar → tutup dropdown
  document.addEventListener('click', (e) => {
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.classList.add('hidden');
    }
  });
});