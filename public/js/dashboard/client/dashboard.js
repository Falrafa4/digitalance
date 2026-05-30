(() => {
  const page = window.__PAGE__ || {};
  const projects = page.projects || [];
  const stats = page.stats || {};

  const $ = (id) => document.getElementById(id);
  const $$ = (sel) => Array.from(document.querySelectorAll(sel));

  let isLoading = false;

  const showLoading = () => {
    isLoading = true;
    const grid = $('project-grid');
    if (!grid) return;

    grid.innerHTML = Array(3).fill(`
      <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1 min-w-0 space-y-2">
          <div class="skeleton h-5 w-3/4"></div>
          <div class="skeleton h-4 w-1/2"></div>
          <div class="flex gap-2 mt-3">
            <div class="skeleton h-6 w-20 rounded-full"></div>
            <div class="skeleton h-6 w-28 rounded-full"></div>
            <div class="skeleton h-6 w-24 rounded-full"></div>
          </div>
        </div>
        <div class="flex gap-2 sm:flex-col sm:items-end">
          <div class="skeleton h-10 w-16 rounded-xl"></div>
          <div class="skeleton h-10 w-24 rounded-xl"></div>
        </div>
      </div>
    `).join('');
  };

  const hideLoading = () => {
    isLoading = false;
    renderProjects();
  };

  const money = (v) => {
    if (!v && v !== 0) return '—';
    try {
      return window.DigitalanceUtils?.formatRupiah(v)
        || new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(v);
    } catch (e) {
      return String(v);
    }
  };

  const safeText = (v) => (v === null || v === undefined ? '' : String(v));

  const STATUS_BADGE = {
    'In Progress': 'bg-indigo-50 text-indigo-700 border border-indigo-100',
    'Pending': 'bg-amber-50 text-amber-700 border border-amber-100',
    'Negotiated': 'bg-cyan-50 text-cyan-700 border border-cyan-100',
    'Paid': 'bg-emerald-50 text-emerald-700 border border-emerald-100',
    'Revision': 'bg-violet-50 text-violet-700 border border-violet-100',
    'Completed': 'bg-emerald-50 text-emerald-700 border border-emerald-100',
    'Cancelled': 'bg-rose-50 text-rose-700 border border-rose-100',
    'Rejected': 'bg-rose-50 text-rose-700 border border-rose-100',
  };

  function formatDeadline(date) {
    if (!date) return '—';
    try {
      const d = new Date(date);
      return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    } catch {
      return safeText(date);
    }
  }

  function renderStats() {
    const statCards = $$('[data-client-stat]');
    statCards.forEach(el => {
      const key = el.dataset.clientStat;
      const value = stats[key];
      if (value !== undefined) {
        if (key === 'totalSpent') {
          el.textContent = money(value);
        } else {
          el.textContent = value;
        }
      }
    });
  }

  function renderProjects() {
    const grid = $('project-grid');
    if (!grid) return;

    if (!projects.length) {
      grid.innerHTML = `
        <div class="col-span-full py-10 px-5 text-center bg-white border-2 border-dashed border-slate-200 rounded-[18px]">
          <div class="text-slate-300 text-[42px] mb-2"><i class="ri-inbox-2-line"></i></div>
          <p class="text-slate-900 font-extrabold text-[1.05rem]">Belum ada proyek</p>
          <p class="text-slate-500 mt-1.5 text-[13px]">Sepertinya kamu belum punya proyek aktif saat ini.</p>
        </div>`;
      return;
    }

    grid.innerHTML = projects.slice(0, 6).map(p => {
      const badgeCls = STATUS_BADGE[p.status] || 'bg-slate-50 text-slate-600 border border-slate-100';
      const deadline = formatDeadline(p.deadline);
      const amount = p.agreed_price ? money(p.agreed_price) : '—';
      const href = p.href || `/client/orders/${p.id}`;
      const serviceTitle = p.service?.title || p.service_title || 'Layanan';

      return `
        <div class="bg-white border border-slate-200 rounded-[18px] p-5 flex flex-col sm:flex-row sm:items-center gap-4 hover:shadow-lg transition-all">
          <div class="flex-1 min-w-0">
            <p class="text-slate-900 font-extrabold text-[14.5px] truncate">${safeText(serviceTitle)}</p>
            <p class="text-slate-500 text-[13px] mt-1 line-clamp-1">${safeText(p.brief || '')}</p>
            <div class="flex flex-wrap items-center gap-2 mt-3">
              <span class="px-3 py-1 rounded-full text-[12px] font-bold ${badgeCls}">${safeText(p.status)}</span>
              <span class="px-3 py-1 rounded-full text-[12px] font-bold bg-white text-slate-600 border border-slate-200">Deadline: ${deadline}</span>
              <span class="px-3 py-1 rounded-full text-[12px] font-bold bg-white text-slate-600 border border-slate-200">${amount}</span>
            </div>
          </div>
          <div class="flex gap-2 sm:flex-col sm:items-end">
            <a href="${href}" class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">Rincian</a>
            ${p.service_id ? `<a href="/client/services/${p.service_id}" class="px-4 py-2.5 rounded-[12px] bg-white border-[1.5px] border-slate-200 text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">Lihat Layanan</a>` : ''}
          </div>
        </div>`;
    }).join('');
  }

  function refresh() {
    renderStats();
    renderProjects();
  }

  function init() {
    refresh();

    const notifBtn = $('notif-btn');
    if (notifBtn) {
      const hasUnread = page.hasUnread || page.unread || false;
      notifBtn.classList.toggle('has-unread', hasUnread);
      notifBtn.addEventListener('click', () => notifBtn.classList.remove('has-unread'));
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
