(() => {
  const page = window.__FREELANCER_TRANSACTIONS__ || {};
  const trxRaw = Array.isArray(page.transactions) ? page.transactions : [];
  const links = page.links || {};

  const $ = (id) => document.getElementById(id);
  const $$ = (sel) => Array.from(document.querySelectorAll(sel));

  const STATUS_BADGE = {
    Pending: 'bg-orange-50 text-orange-700 border border-orange-100',
    Paid: 'bg-emerald-50 text-emerald-700 border border-emerald-100',
    Success: 'bg-emerald-50 text-emerald-700 border border-emerald-100',
    Failed: 'bg-red-50 text-red-700 border border-red-100',
    Cancelled: 'bg-red-50 text-red-700 border border-red-100',
  };

  const safeText = (v) => (v === null || v === undefined ? '' : String(v));

  const money = (v) => {
    if (v === null || v === undefined || v === '') return '—';
    if (typeof v === 'string') return v;
    try {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v);
    } catch (e) {
      return String(v);
    }
  };

  function normalizeTrx(t) {
    const id = t?.id ?? null;
    const orderId = t?.order_id ?? t?.order?.id ?? null;
    const status = t?.status ?? 'Pending';
    const type = t?.type ?? t?.method ?? '—';
    const amount = t?.amount ?? t?.total ?? null;
    const created = t?.created_at ?? t?.date ?? null;
    const serviceTitle = t?.order?.service?.title ?? t?.order?.service?.name ?? t?.service_title ?? 'Service';
    return { id, orderId, status, type, amount, created, serviceTitle, _raw: t };
  }

  let data = trxRaw.map(normalizeTrx);

  function activeFilter() {
    const tab = document.querySelector('#filter-tabs .filter-tab.active');
    return tab?.dataset?.filter || 'all';
  }

  function query() {
    return ($('trx-search')?.value || '').trim().toLowerCase();
  }

  function filterData() {
    const f = activeFilter();
    const q = query();

    let res = data;
    if (f !== 'all') res = res.filter((t) => safeText(t.status) === f);

    if (q) {
      res = res.filter((t) => {
        const blob = `${safeText(t.id)} ${safeText(t.orderId)} ${safeText(t.type)} ${safeText(t.status)} ${safeText(t.serviceTitle)}`.toLowerCase();
        return blob.includes(q);
      });
    }
    return res;
  }

  function emptyState() {
    return `
      <div class="py-16 px-5 text-center bg-white border-2 border-dashed border-slate-200 rounded-3xl">
        <i class="ri-bank-card-line text-[3rem] text-slate-300 block mb-3"></i>
        <h3 class="font-display text-[1.1rem] font-bold text-slate-700 mb-1.5">No transactions found</h3>
        <p class="text-[13px] text-slate-400">Coba ubah filter atau kata kunci pencarian.</p>
      </div>
    `;
  }

  function render() {
    const list = $('trx-list');
    if (!list) return;

    const rows = filterData();
    if (!rows.length) {
      list.innerHTML = emptyState();
      return;
    }

    list.innerHTML = rows.map((t) => {
      const badgeCls = STATUS_BADGE[t.status] || 'bg-slate-50 text-slate-600 border border-slate-100';
      const href = t.orderId ? `${links.detailPrefix || '/freelancer/transactions/order/'}${t.orderId}` : null;

      return `
        <div class="bg-white border border-slate-200 rounded-[18px] p-5 hover:border-[#0f766e] hover:shadow-lg transition-all">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black uppercase tracking-wider ${badgeCls}">
                  ${safeText(t.status)}
                </span>
                <span class="text-[12px] text-slate-400 font-bold uppercase tracking-[.12em]">
                  Trx #${safeText(t.id || '—')}
                </span>
              </div>
              <div class="text-slate-900 font-extrabold text-[15px] mt-2 truncate">${safeText(t.serviceTitle)}</div>
              <div class="text-slate-500 text-[13px] mt-1 flex flex-wrap gap-3">
                <span class="inline-flex items-center gap-1.5">
                  <i class="ri-file-list-3-line text-slate-400"></i> Order #${safeText(t.orderId || '—')}
                </span>
                <span class="inline-flex items-center gap-1.5">
                  <i class="ri-secure-payment-line text-slate-400"></i> ${safeText(t.type)}
                </span>
              </div>
            </div>

            <div class="text-right flex-shrink-0">
              <div class="text-[11px] font-bold text-slate-400 uppercase tracking-[.12em]">Amount</div>
              <div class="text-[14px] font-extrabold text-slate-900 mt-1">${money(t.amount)}</div>
            </div>
          </div>

          <div class="mt-4 flex items-center justify-between gap-2">
            <div class="text-[12px] text-slate-400 font-semibold">
              <i class="ri-time-line mr-1"></i>${safeText(t.created || '—')}
            </div>
            ${href
              ? `<a href="${href}" class="px-4 py-2 rounded-[11px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">Detail</a>`
              : `<button type="button" class="px-4 py-2 rounded-[11px] bg-slate-900 text-white font-bold text-[12.5px] opacity-60 cursor-not-allowed">Detail</button>`
            }
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
    const search = $('trx-search');
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

