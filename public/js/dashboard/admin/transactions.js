(() => {
    const U = window.DashboardUtils;
    const $ = (id) => U.$(id);
    const formatRupiah = (n) => U.formatRupiah(n);
    const apiRequest = (url, opts) => U.apiRequest(url, opts);
    const showToast = (msg, type) => U.showToast(msg, type);
    const openModal = (id) => U.openModal(id);
    const closeModal = (id) => U.closeModal(id);

    const page = window.__TRANSACTIONS_PAGE__ || {};
    let trxData = Array.isArray(page.data) ? page.data : (page.data?.data || []);

    let currentPage = 1;
    let itemsPerPage = 10;
    let reportTargetId = null;

    window.openModal = openModal;
    window.closeModal = closeModal;

    // RENDER STATS
    function renderStats() {
        const row = $('stats-row');
        if (!row) return;

        const total = trxData.length;
        const totalRevenue = trxData
            .filter(t => String(t.status).toLowerCase() === 'paid' && String(t.type).toLowerCase() !== 'refund')
            .reduce((sum, t) => sum + (Number(t.amount) || 0), 0);
        const pendingCount = trxData.filter(t => String(t.status).toLowerCase() === 'pending').length;
        const totalRefund = trxData
            .filter(t => String(t.type).toLowerCase() === 'refund' && String(t.status).toLowerCase() === 'paid')
            .reduce((sum, t) => sum + (Number(t.amount) || 0), 0);

        row.innerHTML = `
            <div class="bg-white border border-slate-200 rounded-2xl px-5 py-[18px] flex items-center gap-3.5 transition-all duration-200 hover:shadow-md hover:-translate-y-px">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-[20px] flex-shrink-0 bg-blue-50 text-blue-600"><i class="ri-bank-card-line"></i></div>
                <div><div class="font-display text-[1.5rem] font-extrabold text-slate-900 leading-none">${total}</div><div class="text-[12px] text-slate-500 font-semibold mt-0.5">Total Transaksi</div></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl px-5 py-[18px] flex items-center gap-3.5 transition-all duration-200 hover:shadow-md hover:-translate-y-px">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-[20px] flex-shrink-0 bg-emerald-50 text-emerald-600"><i class="ri-wallet-3-line"></i></div>
                <div><div class="font-display text-[1.5rem] font-extrabold text-slate-900 leading-none">${formatRupiah(totalRevenue).replace(',00', '')}</div><div class="text-[12px] text-slate-500 font-semibold mt-0.5">Pemasukan (Paid)</div></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl px-5 py-[18px] flex items-center gap-3.5 transition-all duration-200 hover:shadow-md hover:-translate-y-px">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-[20px] flex-shrink-0 bg-amber-50 text-amber-600"><i class="ri-time-line"></i></div>
                <div><div class="font-display text-[1.5rem] font-extrabold text-slate-900 leading-none">${pendingCount}</div><div class="text-[12px] text-slate-500 font-semibold mt-0.5">Transaksi Pending</div></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl px-5 py-[18px] flex items-center gap-3.5 transition-all duration-200 hover:shadow-md hover:-translate-y-px">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-[20px] flex-shrink-0 bg-red-50 text-red-600"><i class="ri-refund-2-line"></i></div>
                <div><div class="font-display text-[1.5rem] font-extrabold text-slate-900 leading-none">${formatRupiah(totalRefund).replace(',00', '')}</div><div class="text-[12px] text-slate-500 font-semibold mt-0.5">Total Refund</div></div>
            </div>
        `;
    }

    // RENDER TABLE
    function renderTable(data = trxData) {
        const tbody = $('trx-tbody');
        const emptyEl = $('trx-empty');
        const tableEl = $('trx-table');
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

        tbody.innerHTML = paginatedData.map(t => {
            const rawStatus = String(t.status || 'pending').toLowerCase();
            const date = t.created_at ? new Date(t.created_at).toLocaleDateString('id-ID') : '-';

            return `
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-mono font-bold text-slate-900">#${t.id}</td>
                    <td class="px-6 py-4 font-mono text-slate-600">#${t.order_id ?? '-'}</td>
                    <td class="px-6 py-4 font-bold text-slate-800">${formatRupiah(t.amount || 0)}</td>
                    <td class="px-6 py-4"><span class="type-pill">${t.type || '-'}</span></td>
                    <td class="px-6 py-4"><span class="status-pill status-${rawStatus}">${t.status || '-'}</span></td>
                    <td class="px-6 py-4 text-slate-500">${date}</td>
                    <td class="px-6 py-4 text-center">
                        <button class="btn-action" title="Detail" onclick="window.openTrxModal('${t.id}')">
                            <i class="ri-eye-line"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        renderPaginationControls(totalPages);
    }

    function renderPaginationControls(totalPages) {
        let wrap = $('pagination-wrap');
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.id = 'pagination-wrap';
            wrap.className = 'flex justify-end gap-2 mt-4 px-6';
            $('trx-table').parentNode.appendChild(wrap);
        }
        
        if (totalPages <= 1) {
            wrap.innerHTML = '';
            return;
        }

        let html = '';
        html += `<button class="px-3 py-1 rounded border border-slate-200 bg-white text-sm hover:bg-slate-50 disabled:opacity-50" ${currentPage === 1 ? 'disabled' : ''} onclick="window.changeTrxPage(${currentPage - 1})">Prev</button>`;
        
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html += `<button class="px-3 py-1 rounded border ${i === currentPage ? 'bg-[#0f766e] text-white border-[#0f766e]' : 'border-slate-200 bg-white hover:bg-slate-50'} text-sm" onclick="window.changeTrxPage(${i})">${i}</button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<span class="px-2 py-1 text-slate-400">...</span>`;
            }
        }

        html += `<button class="px-3 py-1 rounded border border-slate-200 bg-white text-sm hover:bg-slate-50 disabled:opacity-50" ${currentPage === totalPages ? 'disabled' : ''} onclick="window.changeTrxPage(${currentPage + 1})">Next</button>`;
        wrap.innerHTML = html;
    }

    window.changeTrxPage = function(page) {
        currentPage = page;
        refreshGrid();
    };

    // OPEN DETAIL MODAL
    window.openTrxModal = function(id) {
        const t = trxData.find(x => String(x.id) === String(id));
        if (!t) return;

        const box = $('detail-trx-box');
        const rawStatus = String(t.status || 'pending').toLowerCase();
        const rawType = String(t.type || 'full').toLowerCase();
        const date = t.created_at ? new Date(t.created_at).toLocaleString('id-ID') : '-';

        box.innerHTML = `
            <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
                <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Detail Transaksi #${t.id}</span>
                <button onclick="window.closeModal('modal-detail-trx')" class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all"><i class="ri-close-line"></i></button>
            </div>
            <div class="px-[26px] py-[22px] overflow-y-auto flex-1">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-100 text-center">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nominal</span>
                        <span class="text-[1.5rem] font-display font-extrabold text-[#0f766e]">${formatRupiah(t.amount || 0)}</span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex flex-col gap-1.5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</span>
                        <span class="status-pill status-${rawStatus} w-fit">${t.status || '-'}</span>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Order ID</span>
                        <p class="text-[13.5px] text-slate-700 font-medium font-mono">#${t.order_id ?? '-'}</p>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tipe Pembayaran</span>
                        <p class="text-[13.5px] text-slate-700 font-medium">${t.type || '-'}</p>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Waktu Dibuat</span>
                        <p class="text-[13.5px] text-slate-700 font-medium">${date}</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50 flex-shrink-0">
                <button onclick="window.closeModal('modal-detail-trx')" class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all">Close</button>
            </div>
        `;
        openModal('modal-detail-trx');
    };

    // REPORT FAKE
    window.openReportModal = function(id) {
        reportTargetId = id;
        $('report-reason').value = '';
        openModal('modal-report-trx');
    };

    $('btn-confirm-report')?.addEventListener('click', async () => {
        if (!reportTargetId) return;
        const reason = $('report-reason').value;
        
        try {
            // Asumsi endpoint report: POST /admin/transactions/{id}/report
            await apiRequest(`/admin/transactions/${reportTargetId}/report`, {
                method: 'POST',
                body: { reason: reason }
            });

            // Update local data
            const t = trxData.find(x => String(x.id) === String(reportTargetId));
            if (t) t.status = 'Failed'; // Atau status lain sesuai backend

            closeModal('modal-report-trx');
            closeModal('modal-detail-trx');
            showToast('Transaksi berhasil dilaporkan sebagai Fake.', 'success');
            renderStats();
            refreshGrid();
            reportTargetId = null;
        } catch (error) {
            showToast(error.message || 'Gagal melaporkan transaksi.', 'danger');
        }
    });

    function refreshGrid() {
        const active = document.querySelector('.filter-tab.active');
        const f = active ? active.dataset.filter.toLowerCase() : 'all';
        const q = ($('trx-search-input')?.value || '').toLowerCase();

        let res = trxData;
        if (f !== 'all') res = res.filter(t => String(t.status).toLowerCase() === f);
        if (q) {
            res = res.filter(t => 
                String(t.id).toLowerCase().includes(q) || 
                String(t.order_id || '').toLowerCase().includes(q)
            );
        }
        renderTable(res);
    }

    function initFilters() {
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.filter-tab').forEach(t => {
                    t.classList.remove('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]', 'shadow-teal-sm');
                    t.classList.add('border-slate-200', 'bg-white', 'text-slate-500');
                });
                tab.classList.add('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]', 'shadow-teal-sm');
                tab.classList.remove('border-slate-200', 'bg-white', 'text-slate-500');
                currentPage = 1;
                refreshGrid();
            });
        });
    }

    function init() {
        U.setupOverlayListeners();
        renderStats();
        refreshGrid();
        initFilters();

        $('trx-search-input')?.addEventListener('input', () => { currentPage = 1; refreshGrid(); });
    }

    U.ready(init);
})();