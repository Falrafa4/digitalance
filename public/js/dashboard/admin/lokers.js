(() => {
    const U = window.DashboardUtils || window.DigitalanceUtils || {};
    const page = window.__ADMIN_LOKERS_PAGE__ || {};
    const lokers = Array.isArray(page.data) ? page.data : [];
    const csrfToken = page.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '';

    const $ = (id) => document.getElementById(id);
    const safeText = (value) => U.safeText ? U.safeText(value) : String(value ?? '');
    const formatRupiah = (value) => U.formatRupiah ? U.formatRupiah(value) : `Rp${Number(value || 0).toLocaleString('id-ID')}`;
    const openModal = (id) => {
        if (U.openModal) return U.openModal(id);
        const overlay = $(id);
        if (!overlay) return;
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.querySelector('.modal-box, [role="dialog"]')?.classList.remove('scale-95');
    };
    const closeModal = (id) => {
        if (U.closeModal) return U.closeModal(id);
        const overlay = $(id);
        if (!overlay) return;
        overlay.classList.add('opacity-0', 'pointer-events-none');
        overlay.querySelector('.modal-box, [role="dialog"]')?.classList.add('scale-95');
    };

    function statusBadge(status) {
        const map = {
            Open: 'bg-emerald-100 text-emerald-700',
            Closed: 'bg-slate-100 text-slate-600',
            Pending: 'bg-amber-100 text-amber-700',
            Approved: 'bg-emerald-100 text-emerald-700',
            Rejected: 'bg-rose-100 text-rose-700',
        };

        return map[status] || 'bg-slate-100 text-slate-600';
    }

    function formatBudget(min, max) {
        if (min && max) return `${formatRupiah(min)} - ${formatRupiah(max)}`;
        if (min) return `Min ${formatRupiah(min)}`;
        if (max) return `Maks ${formatRupiah(max)}`;

        return 'Belum ditentukan';
    }

    function formatDate(value, options = { day: 'numeric', month: 'short', year: 'numeric' }) {
        if (!value) return '—';

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '—';

        return date.toLocaleDateString('id-ID', options);
    }

    function renderApplicationActionForm(url, buttonClass, icon, label) {
        return `
            <form action="${safeText(url)}" method="POST" class="flex-1">
                <input type="hidden" name="_token" value="${safeText(csrfToken)}">
                <button type="submit" class="w-full py-2.5 rounded-xl ${buttonClass} font-bold text-[12px] transition-all">
                    <i class="${icon} mr-1"></i>${label}
                </button>
            </form>
        `;
    }

    function renderApplications(applications) {
        if (!applications.length) {
            return `
                <div class="rounded-[20px] border border-dashed border-slate-200 bg-slate-50 px-5 py-8 text-center">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-white border border-slate-200 text-slate-300 flex items-center justify-center text-2xl mb-3">
                        <i class="ri-user-search-line"></i>
                    </div>
                    <p class="text-[13px] font-bold text-slate-500">Belum ada lamaran untuk lowongan ini.</p>
                </div>
            `;
        }

        return `
            <div class="space-y-3">
                ${applications.map((application) => {
                    const isPending = application.status === 'Pending';

                    return `
                        <div class="rounded-[18px] border border-slate-200 bg-white p-4">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1.5">
                                        <p class="text-[14px] font-black text-slate-900">${safeText(application.freelancer?.name || 'Freelancer')}</p>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider ${statusBadge(application.status)}">
                                            ${safeText(application.status)}
                                        </span>
                                    </div>
                                    <p class="text-[12px] text-slate-500 font-semibold">
                                        ${safeText(application.freelancer?.major || 'Freelancer Digitalance')}
                                    </p>
                                    ${application.freelancer?.email ? `<p class="text-[11px] text-slate-400 mt-1">${safeText(application.freelancer.email)}</p>` : ''}
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Penawaran</p>
                                    <p class="text-[13px] font-black text-[#0f766e]">
                                        ${application.proposed_price ? formatRupiah(application.proposed_price) : 'Diskusi'}
                                    </p>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3 mb-3">
                                <p class="text-[12px] text-slate-600 leading-relaxed whitespace-pre-line">${safeText(application.proposal || 'Tidak ada proposal tambahan.')}</p>
                            </div>

                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <p class="text-[11px] font-semibold text-slate-400">
                                    Dikirim ${formatDate(application.created_at, { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                </p>
                                ${isPending ? `
                                    <div class="flex gap-2 w-full sm:w-auto">
                                        ${renderApplicationActionForm(application.routes.approve, 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100', 'ri-check-line', 'Setujui')}
                                        ${renderApplicationActionForm(application.routes.reject, 'bg-rose-50 text-rose-700 hover:bg-rose-100', 'ri-close-line', 'Tolak')}
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }

    window.openAdminLokerDetail = function (id) {
        const loker = lokers.find((item) => String(item.id) === String(id));
        if (!loker) return;

        const box = $('modal-admin-loker-box');
        if (!box) return;

        const isOpen = loker.status === 'Open';
        const toggleStatus = isOpen ? 'Closed' : 'Open';
        const toggleLabel = isOpen ? 'Tutup Lowongan' : 'Buka Kembali';
        const pendingCount = loker.pending_applications_count || 0;

        box.innerHTML = `
            <div class="flex flex-col min-h-0 max-h-[88vh]">
                <div class="relative px-7 py-6 border-b border-slate-100 bg-gradient-to-r from-[#0f766e] to-[#0b5f59] text-white flex-shrink-0">
                    <button type="button" data-close-admin-loker-modal class="absolute top-5 right-5 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 transition-all flex items-center justify-center border border-white/10">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/70 mb-2">Lowongan #LOK-${safeText(loker.id)}</p>
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider ${statusBadge(loker.status)}">
                            ${safeText(loker.status)}
                        </span>
                        ${loker.category?.name ? `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white/15 text-white">${safeText(loker.category.name)}</span>` : ''}
                        ${pendingCount ? `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-300/20 text-amber-100">${pendingCount} pending</span>` : ''}
                    </div>
                    <h2 id="modal-admin-loker-title" class="font-display text-[1.45rem] font-black leading-tight pr-8">${safeText(loker.title)}</h2>
                    <p class="text-[13px] font-semibold text-white/80 mt-2">
                        Diposting oleh ${safeText(loker.client?.name || 'Client')}
                    </p>
                </div>

                <div class="flex-1 min-h-0 overflow-y-auto px-7 py-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                        <div class="rounded-[18px] bg-slate-50 border border-slate-100 px-4 py-3.5">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Budget</p>
                            <p class="text-[13px] font-bold text-slate-700">${formatBudget(loker.budget_min, loker.budget_max)}</p>
                        </div>
                        <div class="rounded-[18px] bg-slate-50 border border-slate-100 px-4 py-3.5">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Deadline</p>
                            <p class="text-[13px] font-bold text-slate-700">${formatDate(loker.deadline)}</p>
                        </div>
                        <div class="rounded-[18px] bg-slate-50 border border-slate-100 px-4 py-3.5">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Lamaran Masuk</p>
                            <p class="text-[13px] font-bold text-slate-700">${safeText(loker.applications_count || 0)} freelancer</p>
                        </div>
                        <div class="rounded-[18px] bg-slate-50 border border-slate-100 px-4 py-3.5">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Diposting</p>
                            <p class="text-[13px] font-bold text-slate-700">${formatDate(loker.created_at)}</p>
                        </div>
                    </div>

                    <div class="rounded-[20px] border border-slate-200 bg-white p-5 mb-6">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Deskripsi Lowongan</p>
                        <p class="text-[13px] text-slate-600 leading-relaxed whitespace-pre-line">${safeText(loker.description || 'Tidak ada deskripsi.')}</p>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center justify-between gap-3 mb-3 flex-wrap">
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">Lamaran Freelancer</p>
                                <p class="text-[13px] font-semibold text-slate-500 mt-1">Admin bisa memantau sekaligus memoderasi lamaran pending dari sini.</p>
                            </div>
                        </div>
                        ${renderApplications(loker.applications || [])}
                    </div>
                </div>

                <div class="flex-shrink-0 px-7 py-5 border-t border-slate-100 bg-white">
                    <div class="flex gap-3 flex-col sm:flex-row">
                        <form action="${safeText(loker.routes.update)}" method="POST" class="flex-1">
                            <input type="hidden" name="_token" value="${safeText(csrfToken)}">
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="status" value="${safeText(toggleStatus)}">
                            <button type="submit" class="w-full py-3.5 rounded-[16px] ${isOpen ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'} font-bold text-[13px] transition-all">
                                ${toggleLabel}
                            </button>
                        </form>
                        <form action="${safeText(loker.routes.destroy)}" method="POST" class="flex-1">
                            <input type="hidden" name="_token" value="${safeText(csrfToken)}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="w-full py-3.5 rounded-[16px] bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-[13px] transition-all">
                                Hapus Lowongan
                            </button>
                        </form>
                        <button type="button" data-close-admin-loker-modal class="flex-1 py-3.5 rounded-[16px] bg-[#0f766e] text-white hover:bg-[#0a5e58] font-bold text-[13px] transition-all shadow-teal-sm">
                            Tutup Detail
                        </button>
                    </div>
                </div>
            </div>
        `;

        openModal('modal-admin-loker-overlay');
        if (U.focusTrap) {
            setTimeout(() => U.focusTrap(box), 0);
        }
    };

    window.closeAdminLokerDetail = function () {
        closeModal('modal-admin-loker-overlay');
    };

    document.addEventListener('click', async (event) => {
        const openBtn = event.target.closest('[data-open-loker-detail]');
        if (openBtn) {
            window.openAdminLokerDetail(openBtn.getAttribute('data-open-loker-detail'));
            return;
        }

        const closeBtn = event.target.closest('[data-close-admin-loker-modal]');
        if (closeBtn) {
            window.closeAdminLokerDetail();
            return;
        }

        const confirmBtn = event.target.closest('[data-submit-form]');
        if (!confirmBtn) return;

        const formId = confirmBtn.getAttribute('data-submit-form');
        const form = formId ? $(formId) : null;
        if (!form) return;

        const message = confirmBtn.getAttribute('data-confirm-message') || 'Lanjutkan aksi ini?';
        const confirmed = window.customConfirm ? await window.customConfirm(message) : window.confirm(message);
        if (confirmed) form.submit();
    });

    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;

        if (!form.closest('#modal-admin-loker-box')) return;

        const isDelete = form.querySelector('input[name="_method"][value="DELETE"]');
        const isToggle = form.querySelector('input[name="status"]');
        const isApprove = form.action.includes('/approve');
        const isReject = form.action.includes('/reject');

        let message = null;
        if (isDelete) {
            message = 'Hapus lowongan ini secara permanen?';
        } else if (isToggle) {
            message = isToggle.value === 'Closed' ? 'Tutup lowongan ini?' : 'Buka kembali lowongan ini?';
        } else if (isApprove) {
            message = 'Setujui lamaran ini dan buat order baru?';
        } else if (isReject) {
            message = 'Tolak lamaran freelancer ini?';
        }

        if (!message) return;

        event.preventDefault();
        const confirmed = window.customConfirm ? await window.customConfirm(message) : window.confirm(message);
        if (confirmed) form.submit();
    });

    document.addEventListener('DOMContentLoaded', () => {
        const overlay = $('modal-admin-loker-overlay');
        if (overlay) {
            overlay.addEventListener('click', (event) => {
                if (event.target === overlay) window.closeAdminLokerDetail();
            });
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const overlay = $('modal-admin-loker-overlay');
        if (!overlay || overlay.classList.contains('pointer-events-none')) return;
        window.closeAdminLokerDetail();
    });
})();
