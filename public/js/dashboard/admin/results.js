(() => {
    // 1. AMBIL DATA DARI WINDOW YANG SUDAH DI-SET DI BLADE
    const pageData = window.__RESULTS_PAGE__ || {};
    let allResults = Array.isArray(pageData.data) ? pageData.data : [];

    // State Filter
    let activeFilter = 'all';

    const $ = (id) => document.getElementById(id);

    // 2. KONFIGURASI STATUS SESUAI DATABASE
    // Key harus sama persis dengan string yang ada di database
    const STATUS_CONFIG = {
        'paid': { label: 'Paid', color: 'text-emerald-700', bg: 'bg-emerald-100' },
        'pending': { label: 'Pending', color: 'text-amber-700', bg: 'bg-amber-100' },
        'in_progress': { label: 'In Progress', color: 'text-blue-700', bg: 'bg-blue-100' },
        'cancelled': { label: 'Cancelled', color: 'text-red-700', bg: 'bg-red-100' }
    };

    // Helper untuk mendapat status string (fallback jika kosong)
    function getStatusKey(item) {
        // Cek di result.status, jika tidak ada cek di order.status
        const s = (item.status || item.order?.status || 'pending').toLowerCase();
        return s;
    }

    // 3. FUNGSI RENDER STATS
    function renderStats() {
        let stats = { paid: 0, pending: 0, in_progress: 0, cancelled: 0 };

        allResults.forEach(item => {
            const key = getStatusKey(item);
            if (stats[key] !== undefined) {
                stats[key]++;
            }
        });

        if ($('stat-paid')) $('stat-paid').textContent = stats.paid;
        if ($('stat-pending')) $('stat-pending').textContent = stats.pending;
        if ($('stat-in_progress')) $('stat-in_progress').textContent = stats.in_progress;
        if ($('stat-cancelled')) $('stat-cancelled').textContent = stats.cancelled;
    }

    // 4. FUNGSI RENDER TABLE
    function renderTable(data) {
        const tbody = $('result-tbody');
        const emptyEl = $('result-empty');
        const table = document.querySelector('table');

        if (!tbody) return;

        if (!data || data.length === 0) {
            if (table) table.style.display = 'none';
            if (emptyEl) emptyEl.classList.remove('hidden');
            return;
        }

        if (table) table.style.display = 'table';
        if (emptyEl) emptyEl.classList.add('hidden');

        tbody.innerHTML = data.map(item => {
            const key = getStatusKey(item);
            const style = STATUS_CONFIG[key] || { label: item.status, color: 'text-slate-700', bg: 'bg-slate-100' };
            
            const date = item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID') : '-';
            const orderId = item.order_id || item.order?.id || '-';

            return `
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors duration-150">
                    <td class="px-6 py-4 text-[12px] font-mono text-slate-400">#${item.id}</td>
                    <td class="px-6 py-4 text-[13px] font-semibold text-[#0f766e]">#${orderId}</td>
                    <td class="px-6 py-4">
                        ${item.file_url ? 
                            `<a href="/storage/${item.file_url}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 rounded-md text-xs font-bold hover:bg-blue-100">
                                <i class="ri-file-link-line"></i> View
                            </a>` : '<span class="text-slate-300 text-xs">—</span>'
                        }
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold ${style.bg} ${style.color}">
                            ${style.label}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-[12px] text-slate-400">${date}</td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="alert('Detail fitur belum aktif')" class="w-8 h-8 rounded-[9px] bg-slate-100 text-slate-500 flex items-center justify-center text-[14px] hover:bg-[#f0fdf9] hover:text-[#0f766e] transition-all duration-150 border-none cursor-pointer">
                            <i class="ri-eye-line"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // 5. FUNGSI FILTER & SEARCH
    function applyFilter() {
        const searchVal = $('result-search')?.value.toLowerCase() || '';
        
        let filtered = allResults.filter(item => {
            // 1. Filter Status
            if (activeFilter !== 'all') {
                const itemStatus = getStatusKey(item);
                if (itemStatus !== activeFilter) return false;
            }

            // 2. Filter Search
            if (searchVal) {
                const orderId = String(item.order_id || item.order?.id || '').toLowerCase();
                const id = String(item.id).toLowerCase();
                return orderId.includes(searchVal) || id.includes(searchVal);
            }

            return true;
        });

        renderTable(filtered);
    }

    // 6. INITIALIZATION
    function init() {
        // Jalankan Stats & Table awal
        renderStats();
        renderTable(allResults);

        // Setup Filter Tabs
        document.querySelectorAll('.filter-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                // Hapus active dari semua tombol
                document.querySelectorAll('.filter-tab').forEach(b => {
                    b.classList.remove('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]');
                    b.classList.add('bg-white', 'text-slate-500', 'border-slate-200');
                });
                
                // Tambah active ke tombol yang diklik
                btn.classList.add('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]');
                btn.classList.remove('bg-white', 'text-slate-500', 'border-slate-200');

                // Set state filter
                activeFilter = btn.dataset.filter;
                
                // Render ulang table
                applyFilter();
            });
        });

        // Setup Search Input
        const searchInput = $('result-search');
        if (searchInput) {
            searchInput.addEventListener('input', applyFilter);
        }
    }

    // Run when DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();