(() => {
  const page = window.__FREELANCER_ORDERS__ || {};
  const ordersRaw = Array.isArray(page.orders) ? page.orders : [];

  const $ = (id) => document.getElementById(id);
  const $$ = (sel) => Array.from(document.querySelectorAll(sel));

  const STATUS_BADGE = {
    Pending: 'bg-orange-50 text-orange-700 border border-orange-100',
    Negotiated: 'bg-sky-50 text-sky-700 border border-sky-100',
    Paid: 'bg-indigo-50 text-indigo-700 border border-indigo-100',
    'In Progress': 'bg-emerald-50 text-emerald-700 border border-emerald-100',
    Revision: 'bg-purple-50 text-purple-700 border border-purple-100',
    Completed: 'bg-green-50 text-green-700 border border-green-100',
    Cancelled: 'bg-red-50 text-red-700 border border-red-100',
  };

  const money = (v) => {
    if (v === null || v === undefined || v === '') return '—';
    if (typeof v === 'string') return v;
    try {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v);
    } catch (e) {
      return String(v);
    }
  };

  const safeText = (v) => (v === null || v === undefined ? '' : String(v));

  function normalizeOrder(o) {
    const id = o?.id ?? o?.order_id ?? null;
    const status = o?.status ?? o?.state ?? 'Pending';
    const brief = o?.brief ?? '';
    const price = o?.agreed_price ?? o?.amount ?? o?.price ?? null;
    const created = o?.created_at ?? o?.date ?? o?.created ?? null;

    const serviceTitle = o?.service?.title ?? o?.service?.name ?? o?.service_title ?? o?.service_name ?? 'Service';
    const clientName = o?.client?.name ?? o?.client_name ?? o?.buyer?.name ?? 'Client';

    return { id, status, brief, price, created, serviceTitle, clientName, _raw: o };
  }

  let data = ordersRaw.map(normalizeOrder);

  function activeFilter() {
    const tab = document.querySelector('#filter-tabs .filter-tab.active');
    return tab?.dataset?.filter || 'all';
  }

  function query() {
    return ($('order-search')?.value || '').trim().toLowerCase();
  }

  function filterData() {
    const f = activeFilter();
    const q = query();

    let res = data;
    if (f !== 'all') res = res.filter((o) => safeText(o.status) === f);

    if (q) {
      res = res.filter((o) => {
        const blob = `${safeText(o.id)} ${safeText(o.clientName)} ${safeText(o.serviceTitle)} ${safeText(o.status)} ${safeText(o.brief)}`.toLowerCase();
        return blob.includes(q);
      });
    }
    return res;
  }

  function emptyState() {
    return `
      <div class="py-16 px-5 text-center bg-white border-2 border-dashed border-slate-200 rounded-3xl">
        <i class="ri-file-list-3-line text-[3rem] text-slate-300 block mb-3"></i>
        <h3 class="font-display text-[1.1rem] font-bold text-slate-700 mb-1.5">No orders found</h3>
        <p class="text-[13px] text-slate-400">Coba ubah filter atau kata kunci pencarian.</p>
      </div>
    `;
  }

  function render() {
    const list = $('order-list');
    if (!list) return;

    const rows = filterData();
    if (!rows.length) {
      list.innerHTML = emptyState();
      return;
    }

    list.innerHTML = rows.map((o) => {
      const badgeCls = STATUS_BADGE[o.status] || 'bg-slate-50 text-slate-600 border border-slate-100';

      return `
        <div class="bg-white border border-slate-200 rounded-[18px] p-5 hover:border-[#0f766e] hover:shadow-lg transition-all">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black uppercase tracking-wider ${badgeCls}">
                  ${safeText(o.status)}
                </span>
                <span class="text-[12px] text-slate-400 font-bold uppercase tracking-[.12em]">
                  Order #${safeText(o.id)}
                </span>
              </div>
              <div class="text-slate-900 font-extrabold text-[15px] mt-2 truncate">${safeText(o.serviceTitle)}</div>
              <div class="text-slate-500 text-[13px] mt-1 truncate">
                <i class="ri-user-line text-slate-400 mr-1"></i>${safeText(o.clientName)}
              </div>
            </div>

            <div class="text-right flex-shrink-0">
              <div class="text-[11px] font-bold text-slate-400 uppercase tracking-[.12em]">Agreed</div>
              <div class="text-[14px] font-extrabold text-slate-900 mt-1">${money(o.price)}</div>
            </div>
          </div>

          ${o.brief ? `<p class="text-[13px] text-slate-500 mt-3 line-clamp-2">${safeText(o.brief)}</p>` : ''}

          <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
            <div class="text-[12px] text-slate-400 font-semibold">
              <i class="ri-time-line mr-1"></i>${safeText(o.created || '—')}
            </div>
            <div class="flex gap-2">
              <a href="${page?.links?.showPrefix ? `${page.links.showPrefix}${o.id}` : '#'}"
                class="px-4 py-2 rounded-[11px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all ${page?.links?.showPrefix ? '' : 'pointer-events-none opacity-60'}">
                Detail
              </a>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  function setActiveTab(tab) {
    $$('#filter-tabs .filter-tab').forEach((t) => {
      t.classList.remove('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]', 'shadow-teal-sm');
      t.classList.add('border-slate-200', 'bg-white', 'text-slate-500');
    });
    tab.classList.add('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]', 'shadow-teal-sm');
    tab.classList.remove('border-slate-200', 'bg-white', 'text-slate-500');
  }

  function init() {
    const search = $('order-search');
    if (search) search.addEventListener('input', render);

    $$('#filter-tabs .filter-tab').forEach((t) => {
      t.addEventListener('click', () => {
        setActiveTab(t);
        render();
      });
    });

    render();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();

