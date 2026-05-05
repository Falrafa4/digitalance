(() => {
  const page = window.__FREELANCER_DASHBOARD__ || {};

  const $ = (id) => document.getElementById(id);
  const $$ = (sel) => Array.from(document.querySelectorAll(sel));

  const money = (v) => {
    if (v === null || v === undefined || v === '') return '—';
    if (typeof v === 'string') return v;
    try {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v);
    } catch (e) {
      return String(v);
    }
  };

  const num = (v) => {
    const n = Number(v);
    return Number.isFinite(n) ? n : 0;
  };

  const safeText = (v) => (v === null || v === undefined ? '' : String(v));

  // Flexible keys (backend can pass any of these)
  const stats = page.stats || page.summary || page.kpis || {};
  const latestOrders = page.latestOrders || page.latest_orders || page.orders || page.latest_orders_list || [];
  const opportunities = page.jobOpportunities || page.job_opportunities || page.opportunities || page.jobs || [];

  // Optional links from controller (if you want)
  const links = page.links || {};

  const STATUS_BADGE = {
    'In Progress': 'bg-blue-50 text-blue-700 border border-blue-100',
    'Pending': 'bg-orange-50 text-orange-700 border border-orange-100',
    'Completed': 'bg-emerald-50 text-emerald-700 border border-emerald-100',
    'New': 'bg-sky-50 text-sky-700 border border-sky-100',
  };

  function setStat(key, value, { isMoney = false } = {}) {
    const el = document.querySelector(`[data-stat="${key}"]`);
    if (!el) return;
    el.textContent = isMoney ? money(value) : (value === null || value === undefined || value === '' ? '—' : String(value));
  }

  function renderStats() {
    setStat('activeOrders', stats.activeOrders ?? stats.active_orders ?? stats.active_orders_count ?? stats.active ?? 0);
    setStat('services', stats.services ?? stats.services_count ?? stats.total_services ?? 0);
    setStat('avgRating', stats.avgRating ?? stats.avg_rating ?? stats.rating ?? '—');
    setStat('balance', stats.balance ?? stats.availableBalance ?? stats.available_balance ?? stats.wallet ?? '—', { isMoney: true });
  }

  function cardEmpty(icon, title, desc) {
    return `
      <div class="py-10 px-5 text-center bg-white border-2 border-dashed border-slate-200 rounded-[18px]">
        <div class="text-slate-300 text-[42px] mb-2"><i class="${icon}"></i></div>
        <p class="text-slate-900 font-extrabold text-[1.05rem]">${title}</p>
        <p class="text-slate-500 mt-1.5 text-[13px]">${desc}</p>
      </div>
    `;
  }

  function normalizeOrder(o) {
    const id = o?.id ?? o?.order_id ?? null;
    const title = o?.title ?? o?.service_title ?? o?.service?.title ?? o?.service?.name ?? o?.service_name ?? 'Order';
    const client = o?.client_name ?? o?.client?.name ?? o?.buyer?.name ?? o?.customer_name ?? '—';
    const status = o?.status ?? o?.state ?? 'Pending';
    const amount = o?.amount ?? o?.agreed_price ?? o?.price ?? o?.total ?? null;
    const deadline = o?.deadline ?? o?.due_date ?? o?.due ?? null;
    const href = o?.href || o?.url || (id ? `${links.orderShowPrefix || '/freelancer/orders/'}${id}` : null);
    return { id, title, client, status, amount, deadline, href };
  }

  function normalizeJob(j) {
    const id = j?.id ?? j?.job_id ?? null;
    const title = j?.title ?? j?.service_title ?? j?.name ?? 'Job';
    const client = j?.client_name ?? j?.client?.name ?? j?.company ?? '—';
    const status = j?.status ?? 'New';
    const budget = j?.budget ?? j?.amount ?? j?.price ?? null;
    const href = j?.href || j?.url || (id ? `${links.jobShowPrefix || '/freelancer/orders/'}${id}` : null);
    return { id, title, client, status, budget, href };
  }

  function applyFilterSearch({ orders, jobs }) {
    const activeTab = document.querySelector('#freelancer-filter-tabs .filter-tab.active');
    const filter = activeTab?.dataset?.filter || 'all';
    const q = ($('freelancer-search')?.value || '').trim().toLowerCase();

    const match = (x) => {
      if (!q) return true;
      const blob = `${safeText(x.title)} ${safeText(x.client)} ${safeText(x.status)}`.toLowerCase();
      return blob.includes(q);
    };

    const ord = (orders || []).filter(match);
    const job = (jobs || []).filter(match);

    if (filter === 'orders') return { orders: ord, jobs: [] };
    if (filter === 'opportunities') return { orders: [], jobs: job };
    return { orders: ord, jobs: job };
  }

  function renderOrders(list) {
    const el = $('latest-order-list');
    if (!el) return;

    if (!list.length) {
      el.innerHTML = cardEmpty('ri-inbox-archive-line', 'No orders yet', 'When you get new orders, they will appear here.');
      return;
    }

    el.innerHTML = list.slice(0, 6).map((o) => {
      const badgeCls = STATUS_BADGE[o.status] || 'bg-slate-50 text-slate-600 border border-slate-100';
      const deadline = o.deadline ? safeText(o.deadline) : '—';
      const amount = o.amount === null ? '—' : money(o.amount);
      const cta = o.href
        ? `<a href="${o.href}" class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">Detail</a>`
        : `<button type="button" class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] opacity-60 cursor-not-allowed">Detail</button>`;

      return `
        <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
          <div class="flex-1 min-w-0">
            <p class="text-slate-900 font-extrabold text-[14.5px] truncate">${safeText(o.title)}</p>
            <p class="text-slate-500 text-[13px] mt-1 truncate">${safeText(o.client)}</p>

            <div class="flex flex-wrap items-center gap-2 mt-3">
              <span class="px-3 py-1 rounded-full text-[12px] font-bold ${badgeCls}">${safeText(o.status)}</span>
              <span class="px-3 py-1 rounded-full text-[12px] font-bold bg-white text-slate-600 border border-slate-200">Deadline: ${deadline}</span>
              <span class="px-3 py-1 rounded-full text-[12px] font-bold bg-white text-slate-600 border border-slate-200">${amount}</span>
            </div>
          </div>

          <div class="flex gap-2 sm:flex-col sm:items-end">
            ${cta}
          </div>
        </div>
      `;
    }).join('');
  }

  function renderJobs(list) {
    const el = $('job-opp-list');
    if (!el) return;

    if (!list.length) {
      el.innerHTML = cardEmpty('ri-briefcase-3-line', 'No opportunities', 'Try again later — new jobs will show up here.');
      return;
    }

    el.innerHTML = list.slice(0, 6).map((j) => {
      const badgeCls = STATUS_BADGE[j.status] || 'bg-sky-50 text-sky-700 border border-sky-100';
      const budget = j.budget === null ? '—' : money(j.budget);
      const href = j.href ? `href="${j.href}"` : '';

      return `
        <a ${href} class="block p-4 rounded-[16px] bg-slate-50 border border-slate-200 hover:border-[#0f766e] hover:bg-white transition-all">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <p class="font-extrabold text-slate-900 text-[13.5px] truncate">${safeText(j.title)}</p>
              <p class="text-slate-500 text-[12.5px] mt-1 truncate">${safeText(j.client)}</p>
            </div>
            <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black uppercase tracking-wider ${badgeCls}">${safeText(j.status)}</span>
          </div>
          <div class="mt-3 flex items-center justify-between">
            <span class="text-[12px] text-slate-500 font-semibold">Budget</span>
            <span class="text-[12.5px] font-extrabold text-[#0f766e]">${budget}</span>
          </div>
        </a>
      `;
    }).join('');
  }

  function setActiveTab(tabEl) {
    $$('#freelancer-filter-tabs .filter-tab').forEach((t) => {
      t.classList.remove('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]', 'shadow-teal-sm');
      t.classList.add('border-slate-200', 'bg-white', 'text-slate-500');
    });
    tabEl.classList.add('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]', 'shadow-teal-sm');
    tabEl.classList.remove('border-slate-200', 'bg-white', 'text-slate-500');
  }

  function refresh() {
    const normalizedOrders = (Array.isArray(latestOrders) ? latestOrders : []).map(normalizeOrder);
    const normalizedJobs = (Array.isArray(opportunities) ? opportunities : []).map(normalizeJob);
    const filtered = applyFilterSearch({ orders: normalizedOrders, jobs: normalizedJobs });
    renderOrders(filtered.orders);
    renderJobs(filtered.jobs);
  }

  function init() {
    // Keep notif dot behavior consistent with other pages if controller provides it
    const notifBtn = $('notif-btn');
    const hasUnread = Boolean(page.hasUnread || page.has_unread || page.unread || false);
    if (notifBtn) {
      notifBtn.classList.toggle('has-unread', hasUnread);
      notifBtn.addEventListener('click', () => notifBtn.classList.remove('has-unread'));
    }

    renderStats();
    refresh();

    const searchEl = $('freelancer-search');
    if (searchEl) searchEl.addEventListener('input', refresh);

    $$('#freelancer-filter-tabs .filter-tab').forEach((t) => {
      t.addEventListener('click', () => {
        setActiveTab(t);
        refresh();
      });
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();