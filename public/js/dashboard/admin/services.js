(() => {
    const U = window.DashboardUtils;
    const $ = (id) => U.$(id);
    const $$ = (sel) => U.$$(sel);
    const formatRupiah = (n) => U.formatRupiah(n);
    const apiRequest = (url, opts) => U.apiRequest(url, opts);
    const showToast = (msg, type) => U.showToast(msg, type);
    const openModal = (id) => U.openModal(id);
    const closeModal = (id) => U.closeModal(id);
    const safeText = (v) => U.safeText(v);

const page = window.__SERVICES_PAGE__ || {};
    let servicesData = Array.isArray(page.data) ? page.data : (page.data?.data || []);
    let perPage = 12;
    let currentPage = 1;
    let deleteTargetId = null;

    const STATUS_BADGE = {
        Approved: 'bg-emerald-100 text-emerald-800',
        Pending: 'bg-amber-100 text-amber-800',
        Draft: 'bg-slate-100 text-slate-600',
        Rejected: 'bg-red-100 text-red-800',
    };

    function formatPriceRange(min, max) {
        if (max && max > min) return `${formatRupiah(min)} - ${formatRupiah(max)}`;
        return formatRupiah(min);
    }

    function sBadge(s) {
        return STATUS_BADGE[s] || 'bg-slate-100 text-slate-600';
    }

    window.openModal = openModal;
    window.closeModal = closeModal;

    function setMeta(totalShown, totalAll) {
        const meta = $('pagination-meta');
        if (meta) {
            if (totalAll === 0) meta.textContent = `Menampilkan 0–0 dari 0`;
            else meta.textContent = `Menampilkan 1–${totalShown} dari ${totalAll}`;
        }
    }

    function getFilteredData() {
        const active = document.querySelector('.filter-tab.active');
        const f = active ? active.dataset.filter.toLowerCase() : 'all';
        const q = ($('service-search-input')?.value || '').toLowerCase();

        let res = servicesData;

        if (f !== 'all') {
            res = res.filter(s => String(s.status).toLowerCase() === f);
        }

        if (q) {
            res = res.filter(s => {
                const fName = String(s.freelancer?.skomda_student?.name ?? s.freelancer?.name ?? '').toLowerCase();
                const catName = String(s.service_category?.name ?? s.category?.name ?? '').toLowerCase();
                const title = String(s.title ?? '').toLowerCase();
                return title.includes(q) || catName.includes(q) || fName.includes(q);
            });
        }

        return res;
    }

    function renderStats() {
        const total = servicesData.length;
        const approved = servicesData.filter(s => String(s.status).toLowerCase() === 'approved').length;
        const pending = servicesData.filter(s => String(s.status).toLowerCase() === 'pending').length;
        const draft = servicesData.filter(s => String(s.status).toLowerCase() === 'draft').length;

        const row = $('stats-row');
        if (!row) return;

        row.innerHTML = `
            <div class="stat-card">
                <div class="stat-icon blue"><i class="ri-tools-line"></i></div>
                <div class="stat-text">
                    <span class="stat-value">${total}</span>
                    <span class="stat-label">Total Layanan</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon teal"><i class="ri-checkbox-circle-line"></i></div>
                <div class="stat-text">
                    <span class="stat-value">${approved}</span>
                    <span class="stat-label">Disetujui</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="ri-time-line"></i></div>
                <div class="stat-text">
                    <span class="stat-value">${pending}</span>
                    <span class="stat-label">Menunggu Persetujuan</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon slate"><i class="ri-draft-line"></i></div>
                <div class="stat-text">
                    <span class="stat-value">${draft}</span>
                    <span class="stat-label">Draft</span>
                </div>
            </div>
        `;
    }

    function renderCards(data) {
        const wrap = $('service-cards-wrap');
        const emptyEl = $('service-empty');

        if (!wrap) return;

        if (!data || data.length === 0) {
            wrap.style.display = 'none';
            if (emptyEl) emptyEl.style.display = 'block';
            setMeta(0, 0);
            return;
        }

        wrap.style.display = 'grid';
        if (emptyEl) emptyEl.style.display = 'none';

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const paginated = data.slice(start, end);

        setMeta(paginated.length, data.length);

        wrap.innerHTML = paginated.map(s => {
            const fName = safeText(s.freelancer?.skomda_student?.name ?? s.freelancer?.name ?? 'Freelancer');
            const catName = safeText(s.service_category?.name ?? s.category?.name ?? 'Kategori');
            const title = safeText(s.title ?? '');
            const rawStatus = String(s.status || 'Draft').toLowerCase();

            return `
                <div class="service-card animate-fadeUp">
                    <div class="card-header">
                        <span class="service-id">#${s.id}</span>
                        <span class="status-pill status-${rawStatus} ${sBadge(s.status)}">${s.status}</span>
                    </div>
                    <div class="card-body">
                        <span class="cat-badge">${catName}</span>
                        <h3 class="service-title" title="${title}">${title}</h3>
                        <div style="margin-top: 4px; display: flex; flex-direction: column; gap: 6px;">
                            <div class="card-info-row"><i class="ri-user-line"></i> ${fName}</div>
                            <div class="card-info-row"><i class="ri-money-dollar-circle-line"></i> ${formatPriceRange(s.price_min, s.price_max)}</div>
                            <div class="card-info-row"><i class="ri-timer-line"></i> ${s.delivery_time ? s.delivery_time + ' Hari' : '-'}</div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="action-btns">
                            <button class="btn-action" title="Rincian" onclick="window.openServiceModal('${s.id}')"><i class="ri-eye-line"></i></button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        renderPaginationControls(Math.ceil(data.length / perPage));
    }

    function renderPaginationControls(totalPages) {
        const wrap = $('pagination-wrap');
        if (!wrap) return;

        if (totalPages <= 1) {
            wrap.innerHTML = '';
            return;
        }

        let html = '';
        html += `<button class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-[13px] font-bold text-slate-500 hover:bg-slate-50 disabled:opacity-50 transition-all" ${currentPage === 1 ? 'disabled' : ''} onclick="window.changeServicePage(${currentPage - 1})">Sebelumnya</button>`;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html += `<button class="w-8 h-8 rounded-lg border ${i === currentPage ? 'bg-[#0f766e] text-white border-[#0f766e]' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'} text-[13px] font-bold transition-all flex items-center justify-center" onclick="window.changeServicePage(${i})">${i}</button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<span class="w-8 h-8 flex items-center justify-center text-slate-400 text-[13px]">...</span>`;
            }
        }

        html += `<button class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-[13px] font-bold text-slate-500 hover:bg-slate-50 disabled:opacity-50 transition-all" ${currentPage === totalPages ? 'disabled' : ''} onclick="window.changeServicePage(${currentPage + 1})">Selanjutnya</button>`;
        wrap.innerHTML = html;
    }

    window.changeServicePage = function(page) {
        currentPage = page;
        refreshGrid();
    };

    function refreshGrid() {
        renderCards(getFilteredData());
    }

    window.openServiceModal = function(id) {
        const s = servicesData.find(x => String(x.id) === String(id));
        if (!s) return;

        const overlay = $('modal-services-overlay');
        const box = $('modal-services-box');
        if (!box) return;

        const fName = safeText(s.freelancer?.skomda_student?.name ?? s.freelancer?.name ?? 'Freelancer');
        const catName = safeText(s.service_category?.name ?? s.category?.name ?? 'Kategori');
        const title = safeText(s.title ?? '');
        const desc = safeText(s.description || '-');
        const rawStatus = String(s.status || 'Draft').toLowerCase();

        box.innerHTML = `
            <div class="modal-hero"><button class="modal-close" onclick="window.closeServiceModal()"><i class="ri-close-line"></i></button></div>
            <div class="modal-body">
                <span class="service-id" style="margin-bottom:8px; display:inline-block;">#${s.id}</span>
                <h2 class="modal-name">${title}</h2>
                <div class="modal-role-row">
                    <span class="status-pill status-${rawStatus} ${sBadge(s.status)}">${s.status}</span>
                    <span class="cat-badge">${catName}</span>
                </div>
                <div class="modal-info-grid">
                    <div class="modal-info-card">
                        <div class="modal-info-label">Freelancer</div>
                        <div class="modal-info-value"><i class="ri-user-line"></i> ${fName}</div>
                    </div>
                    <div class="modal-info-card">
                        <div class="modal-info-label">Waktu Pengerjaan</div>
                        <div class="modal-info-value"><i class="ri-timer-line"></i> ${s.delivery_time ? s.delivery_time + ' Hari' : '-'}</div>
                    </div>
                    <div class="modal-info-card">
                        <div class="modal-info-label">Harga Minimum</div>
                        <div class="modal-info-value">${formatRupiah(s.price_min)}</div>
                    </div>
                    <div class="modal-info-card">
                        <div class="modal-info-label">Harga Maksimum</div>
                        <div class="modal-info-value">${s.price_max ? formatRupiah(s.price_max) : '-'}</div>
                    </div>
                </div>
                <p class="modal-section-title">Deskripsi Layanan</p>
                <div class="desc-box">${desc}</div>
                ${s.reject_reason ? `
                <div class="reject-reason-box" style="background: #fef2f2; padding: 16px; border-radius: 12px; font-size: 13.5px; color: #991b1b; line-height: 1.6; margin-bottom: 24px; border: 1px solid #fecaca;">
                    <strong style="display: block; margin-bottom: 6px; color: #7f1d1d; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Alasan Penolakan</strong>
                    ${safeText(s.reject_reason)}
                </div>
                ` : ''}
                <div class="modal-actions-row" style="margin-top: 24px; display: flex; gap: 12px;">
                    ${s.status === 'Pending' ? `
                        <button onclick="window.approveService('${s.id}')" class="btn-approve" style="flex: 1; padding: 12px; background: #0f766e; color: white; border: none; border-radius: 11px; font-weight: 700; font-size: 13px; cursor: pointer;"><i class="ri-check-line"></i> Setujui</button>
                        <button onclick="window.openRejectModal('${s.id}')" class="btn-reject" style="flex: 1; padding: 12px; background: #fee2e2; color: #dc2626; border: none; border-radius: 11px; font-weight: 700; font-size: 13px; cursor: pointer;"><i class="ri-close-line"></i> Reject</button>
                    ` : s.status === 'Rejected' ? `
                        <button onclick="window.approveService('${s.id}')" class="btn-approve" style="flex: 1; padding: 12px; background: #0f766e; color: white; border: none; border-radius: 11px; font-weight: 700; font-size: 13px; cursor: pointer;"><i class="ri-check-line"></i> Setujui</button>
                        <button onclick="window.openRejectModal('${s.id}')" class="btn-reject" style="flex: 1; padding: 12px; background: #fffbeb; color: #d97706; border: none; border-radius: 11px; font-weight: 700; font-size: 13px; cursor: pointer;"><i class="ri-refresh-line"></i> Reject Ulang</button>
                    ` : `
                        <button onclick="window.closeServiceModal(); window.openDeleteService('${s.id}')" class="btn-delete" style="flex: 1; padding: 12px; background: #fee2e2; color: #dc2626; border: none; border-radius: 11px; font-weight: 700; font-size: 13px; cursor: pointer;"><i class="ri-delete-bin-line"></i> Hapus</button>
                    `}
                </div>
            </div>
        `;

        if (overlay) openModal('modal-services-overlay');
    };

    window.closeServiceModal = function() {
        closeModal('modal-services-overlay');
    };

    window.approveService = async function(id) {
        if (!(await customConfirm('Yakin ingin menyetujui layanan ini?'))) return;

        try {
            await apiRequest(`/admin/services/${id}/status`, {
                method: 'POST',
                body: { status: 'Approved' }
            });

            const s = servicesData.find(x => String(x.id) === String(id));
            if (s) { s.status = 'Approved'; s.reject_reason = null; }

            closeModal('modal-services-overlay');
            showToast('Layanan berhasil disetujui!', 'success');
            renderStats();
            refreshGrid();
        } catch (error) {
            showToast(error.message || 'Gagal menyetujui layanan.', 'danger');
        }
    };

    window.openRejectModal = function(id) {
        const s = servicesData.find(x => String(x.id) === String(id));
        if (!s) return;

        const existing = $('reject-service-overlay');
        if (existing) existing.remove();

        const el = document.createElement('div');
        el.className = 'overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200';
        el.id = 'reject-service-overlay';

        el.innerHTML = `
            <div class="modal-box bg-white rounded-3xl w-full max-w-[450px] shadow-2xl overflow-hidden">
                <div class="px-[26px] pt-[30px] pb-[24px] text-center">
                    <div class="w-[72px] h-[72px] mx-auto mb-5 bg-red-50 rounded-full flex items-center justify-center text-[2rem] text-red-500"><i class="ri-error-warning-fill"></i></div>
                    <h3 class="font-display text-[1.2rem] font-extrabold text-slate-900 mb-2">Tolak Layanan #${s.id}?</h3>
                    <form id="form-reject-service" style="margin-top: 20px;">
                        <div style="margin-bottom: 16px; text-align: left;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px;">Alasan Penolakan</label>
                            <textarea id="reject-reason-input" rows="4" required placeholder="Tuliskan mengapa layanan ini ditolak..." style="width: 100%; padding: 10px 13px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 11px; font-size: 13.5px; outline: none; font-family: inherit;"></textarea>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="button" id="btn-cancel-reject" style="flex: 1; padding: 11px; border-radius: 11px; background: #e2e8f0; color: #64748b; font-weight: 700; font-size: 13px; cursor: pointer; border: none;">Batal</button>
                            <button type="submit" style="flex: 1; padding: 11px; border-radius: 11px; background: #ef4444; color: white; font-weight: 700; font-size: 13px; cursor: pointer; border: none; box-shadow: 0 3px 10px rgba(239,68,68,.25);"><i class="ri-close-circle-line"></i> Tolak Layanan</button>
                        </div>
                    </form>
                </div>
            </div>
        `;

        document.body.appendChild(el);
        requestAnimationFrame(() => {
            el.classList.remove('opacity-0', 'pointer-events-none');
            el.style.opacity = '1';
            el.style.pointerEvents = 'all';
        });

        const closeFn = () => {
            el.classList.add('opacity-0', 'pointer-events-none');
            el.style.opacity = '0';
            el.style.pointerEvents = 'none';
            setTimeout(() => el.remove(), 200);
        };

        el.querySelector('#btn-cancel-reject').addEventListener('click', closeFn);

        el.querySelector('#form-reject-service').addEventListener('submit', async (e) => {
            e.preventDefault();
            const reason = el.querySelector('#reject-reason-input').value;

            try {
                await apiRequest(`/admin/services/${id}/status`, {
                    method: 'POST',
                    body: { status: 'Rejected', reject_reason: reason }
                });

                const service = servicesData.find(x => String(x.id) === String(id));
                if (service) { service.status = 'Rejected'; service.reject_reason = reason; }

                closeFn();
                closeModal('modal-services-overlay');
                showToast('Layanan berhasil ditolak.', 'success');
                renderStats();
                refreshGrid();
            } catch (error) {
                showToast(error.message || 'Gagal menolak layanan.', 'danger');
            }
        });
    };

    window.openDeleteService = function(id) {
        const s = servicesData.find(x => String(x.id) === String(id));
        if (!s) return;

        deleteTargetId = id;

        const deleteTextEl = $('delete-service-text');
        if (deleteTextEl) {
            const title = safeText(s.title ?? '');
            deleteTextEl.innerHTML = `Tindakan ini tidak dapat dibatalkan. Layanan <strong>#${s.id} - ${title}</strong> akan dihapus permanen.`;
        }

        openModal('modal-delete-service');
    };

    window.confirmDeleteService = async function() {
        if (!deleteTargetId) return;

        try {
            await apiRequest(`/admin/services/${deleteTargetId}`, {
                method: 'DELETE'
            });

            servicesData = servicesData.filter(s => String(s.id) !== String(deleteTargetId));

            closeModal('modal-delete-service');
            showToast('Layanan berhasil dihapus', 'success');
            renderStats();
            refreshGrid();

            deleteTargetId = null;
        } catch (error) {
            showToast(error.message || 'Gagal menghapus layanan.', 'danger');
        }
    };

    function initFilters() {
        const tabs = document.querySelectorAll('.filter-tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => {
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

    function initSearch() {
        const input = $('service-search-input');
        if (input) {
            input.addEventListener('input', () => {
                currentPage = 1;
                refreshGrid();
            });
        }
    }

    function initPagination() {
        const perPageSelect = $('per-page');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                perPage = parseInt(this.value);
                currentPage = 1;
                refreshGrid();
            });
        }
    }

    function init() {
        U.setupOverlayListeners();
        renderStats();
        refreshGrid();
        initFilters();
        initSearch();
        initPagination();

        const overlay = $('modal-services-overlay');
        if (overlay) {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) window.closeServiceModal();
            });
        }

        const btnConfirmDelete = $('btn-confirm-delete-service');
        if (btnConfirmDelete) {
            btnConfirmDelete.addEventListener('click', window.confirmDeleteService);
        }
    }

    U.ready(init);
})();
