(() => {
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

    const $ = (id) => document.getElementById(id);

    function formatRupiah(number) {
        if (!number) return '-';
        return new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR', 
            minimumFractionDigits: 0 
        }).format(number);
    }

    function formatPriceRange(min, max) {
        if (max && max > min) return `${formatRupiah(min)} - ${formatRupiah(max)}`;
        return formatRupiah(min);
    }

    function sBadge(s) { 
        return STATUS_BADGE[s] || 'bg-slate-100 text-slate-600'; 
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || 
               document.querySelector('input[name="_token"]')?.value || '';
    }

    async function apiRequest(url, { method = 'POST', body = null } = {}) {
        const headers = { 
            'X-CSRF-TOKEN': getCsrfToken(), 
            'Accept': 'application/json' 
        };
        
        let payload = body;
        if (body && typeof body === 'object' && !(body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
            payload = JSON.stringify(body);
        }

        const res = await fetch(url, { method, headers, body: payload });
        
        let data = null;
        const ct = res.headers.get('content-type') || '';
        if (ct.includes('application/json')) {
            try { data = await res.json(); } catch (e) {}
        }

        if (!res.ok) throw new Error(data?.message || `Request gagal (${res.status}).`);
        return data;
    }

    function showToast(msg, type = 'success') {
        if (window.showToast) return window.showToast(msg, type);
        alert(msg);
    }

    function openModal(id) {
        const el = $(id);
        if (el) { 
            el.classList.remove('opacity-0', 'pointer-events-none'); 
            el.style.opacity = '1'; 
            el.style.pointerEvents = 'all'; 
        }
    }

    function closeModal(id) {
        const el = $(id);
        if (el) { 
            el.classList.add('opacity-0', 'pointer-events-none'); 
            el.style.opacity = '0'; 
            el.style.pointerEvents = 'none'; 
        }
    }

    window.openModal = openModal;
    window.closeModal = closeModal;

    function setupOverlayListeners() {
        document.querySelectorAll('.overlay').forEach(ov => {
            ov.addEventListener('click', e => { 
                if (e.target === ov) closeModal(ov.id); 
            });
        });
    }

    function setMeta(totalShown, totalAll) {
        const meta = $('pagination-meta');
        if (meta) {
            if (totalAll === 0) meta.textContent = `Showing 0–0 of 0`;
            else meta.textContent = `Showing 1–${totalShown} of ${totalAll}`;
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
                    <span class="stat-label">Total Services</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon teal"><i class="ri-checkbox-circle-line"></i></div>
                <div class="stat-text">
                    <span class="stat-value">${approved}</span>
                    <span class="stat-label">Approved</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="ri-time-line"></i></div>
                <div class="stat-text">
                    <span class="stat-value">${pending}</span>
                    <span class="stat-label">Pending Approval</span>
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
            const fName = s.freelancer?.skomda_student?.name ?? s.freelancer?.name ?? 'Freelancer';
            const catName = s.service_category?.name ?? s.category?.name ?? 'Kategori';
            const rawStatus = String(s.status || 'Draft').toLowerCase();

            return `
                <div class="service-card">
                    <div class="card-header">
                        <span class="service-id">#${s.id}</span>
                        <span class="status-pill status-${rawStatus} ${sBadge(s.status)}">${s.status}</span>
                    </div>
                    <div class="card-body">
                        <span class="cat-badge">${catName}</span>
                        <h3 class="service-title" title="${s.title}">${s.title}</h3>
                        
                        <div style="margin-top: 4px; display: flex; flex-direction: column; gap: 6px;">
                            <div class="card-info-row">
                                <i class="ri-user-line"></i> ${fName}
                            </div>
                            <div class="card-info-row">
                                <i class="ri-money-dollar-circle-line"></i> ${formatPriceRange(s.price_min, s.price_max)}
                            </div>
                            <div class="card-info-row">
                                <i class="ri-timer-line"></i> ${s.delivery_time ? s.delivery_time + ' Hari' : '-'}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="action-btns">
                            <button class="btn-action" title="Detail" onclick="window.openServiceModal('${s.id}')">
                                <i class="ri-eye-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function refreshGrid() {
        renderCards(getFilteredData());
    }

    window.openServiceModal = function(id) {
        const s = servicesData.find(x => String(x.id) === String(id));
        if (!s) return;

        const overlay = $('detail-modal-overlay');
        const box = $('detail-modal-box');

        if (!box) return;

        const fName = s.freelancer?.skomda_student?.name ?? s.freelancer?.name ?? 'Freelancer';
        const catName = s.service_category?.name ?? s.category?.name ?? 'Kategori';
        const rawStatus = String(s.status || 'Draft').toLowerCase();

        box.innerHTML = `
            <div class="modal-hero">
                <button class="modal-close" onclick="window.closeServiceModal()"><i class="ri-close-line"></i></button>
            </div>
            <div class="modal-body">
                <span class="service-id" style="margin-bottom:8px; display:inline-block;">#${s.id}</span>
                <h2 class="modal-name">${s.title}</h2>
                
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
                <div class="desc-box">${s.description || '-'}</div>

                <div class="modal-actions-row" style="margin-top: 24px; display: flex; gap: 12px;">
                    ${s.status === 'Pending' ? `
                        <button onclick="window.approveService('${s.id}')" class="btn-approve" style="flex: 1; padding: 12px; background: #0f766e; color: white; border: none; border-radius: 11px; font-weight: 700; font-size: 13px; cursor: pointer;">
                            <i class="ri-check-line"></i> Approve
                        </button>
                        <button onclick="window.openRejectModal('${s.id}')" class="btn-reject" style="flex: 1; padding: 12px; background: #fee2e2; color: #dc2626; border: none; border-radius: 11px; font-weight: 700; font-size: 13px; cursor: pointer;">
                            <i class="ri-close-line"></i> Reject
                        </button>
                    ` : `
                        <button onclick="window.closeServiceModal(); window.openDeleteService('${s.id}')" class="btn-delete" style="flex: 1; padding: 12px; background: #fee2e2; color: #dc2626; border: none; border-radius: 11px; font-weight: 700; font-size: 13px; cursor: pointer;">
                            <i class="ri-delete-bin-line"></i> Delete
                        </button>
                    `}
                </div>
            </div>
        `;
        
        if (overlay) openModal('detail-modal-overlay');
    };

    window.closeServiceModal = function() {
        closeModal('detail-modal-overlay');
    };

    window.approveService = async function(id) {
        if (!confirm('Yakin ingin menyetujui layanan ini?')) return;

        try {
            await apiRequest(`/admin/services/${id}/status`, { 
                method: 'POST', 
                body: { status: 'Approved' } 
            });

            const s = servicesData.find(x => String(x.id) === String(id));
            if (s) {
                s.status = 'Approved';
                s.reject_reason = null;
            }

            closeModal('detail-modal-overlay');
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
                    <div class="w-[72px] h-[72px] mx-auto mb-5 bg-red-50 rounded-full flex items-center justify-center text-[2rem] text-red-500">
                        <i class="ri-error-warning-fill"></i>
                    </div>
                    <h3 class="font-display text-[1.2rem] font-extrabold text-slate-900 mb-2">Tolak Layanan #${s.id}?</h3>
                    <form id="form-reject-service" style="margin-top: 20px;">
                        <div style="margin-bottom: 16px; text-align: left;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px;">Alasan Penolakan</label>
                            <textarea id="reject-reason-input" rows="4" required placeholder="Tuliskan mengapa layanan ini ditolak..." 
                                style="width: 100%; padding: 10px 13px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 11px; font-size: 13.5px; outline: none; font-family: inherit;"></textarea>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button type="button" id="btn-cancel-reject" style="flex: 1; padding: 11px; border-radius: 11px; background: #e2e8f0; color: #64748b; font-weight: 700; font-size: 13px; cursor: pointer; border: none;">Batal</button>
                            <button type="submit" style="flex: 1; padding: 11px; border-radius: 11px; background: #ef4444; color: white; font-weight: 700; font-size: 13px; cursor: pointer; border: none; box-shadow: 0 3px 10px rgba(239,68,68,.25);">
                                <i class="ri-close-circle-line"></i> Tolak Layanan
                            </button>
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
                if (service) {
                    service.status = 'Rejected';
                    service.reject_reason = reason;
                }

                closeFn();
                closeModal('detail-modal-overlay');
                showToast('Layanan berhasil ditolak.', 'success');
                renderStats();
                refreshGrid();
            } catch (error) {
                showToast(error.message || 'Gagal menolak layanan.', 'danger');
            }
        });
    };

    // DELETE SERVICE
    window.openDeleteService = function(id) {
        const s = servicesData.find(x => String(x.id) === String(id));
        if (!s) return;
        
        deleteTargetId = id;
        
        const deleteTextEl = $('delete-service-text');
        if (deleteTextEl) {
            deleteTextEl.innerHTML = `Tindakan ini tidak dapat dibatalkan. Layanan <strong>#${s.id} - ${s.title}</strong> akan dihapus permanen.`;
        }
        
        openModal('modal-delete-service');
    };

    window.confirmDeleteService = async function() {
        if (!deleteTargetId) return;
        
        try {
            await apiRequest(`/admin/services/${deleteTargetId}`, { 
                method: 'DELETE' 
            });

            // Remove from local data
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
        setupOverlayListeners();
        renderStats();
        refreshGrid();
        initFilters();
        initSearch();
        initPagination();

        const overlay = $('detail-modal-overlay');
        if (overlay) {
            overlay.addEventListener('click', (e) => { 
                if (e.target === overlay) window.closeServiceModal(); 
            });
        }

        // Setup confirm delete button
        const btnConfirmDelete = $('btn-confirm-delete-service');
        if (btnConfirmDelete) {
            btnConfirmDelete.addEventListener('click', window.confirmDeleteService);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();