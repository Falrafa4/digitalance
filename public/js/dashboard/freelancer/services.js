(() => {
  const page = window.__FREELANCER_SERVICES__ || {};
  const servicesRaw = Array.isArray(page.services) ? page.services : [];
  const createUrl = page.createUrl || null;
  const links = page.links || {};

  const $ = (id) => document.getElementById(id);
  const $$ = (sel) => Array.from(document.querySelectorAll(sel));

  const STATUS_BADGE = {
    Approved: 'bg-emerald-50 text-emerald-700 border border-emerald-100',
    Pending: 'bg-orange-50 text-orange-700 border border-orange-100',
    Draft: 'bg-slate-50 text-slate-600 border border-slate-100',
    Rejected: 'bg-red-50 text-red-700 border border-red-100',
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

  function normalizeService(s) {
    const id = s?.id ?? null;
    const title = s?.title ?? s?.name ?? 'Service';
    const category = s?.service_category?.name ?? s?.category?.name ?? s?.service_category_name ?? s?.category_name ?? '—';
    const status = s?.status ?? 'Draft';
    const price = s?.price ?? s?.starting_price ?? s?.min_price ?? null;
    const desc = s?.description ?? s?.desc ?? '';
    const rejectReason = s?.reject_reason ?? null;
    return { id, title, category, status, price, desc, rejectReason, _raw: s };
  }

  let data = servicesRaw.map(normalizeService);

  function activeFilter() {
    const tab = document.querySelector('#filter-tabs .filter-tab.active');
    return tab?.dataset?.filter || 'all';
  }

  function query() {
    return ($('service-search')?.value || '').trim().toLowerCase();
  }

  function filterData() {
    const f = activeFilter();
    const q = query();

    let res = data;
    if (f !== 'all') res = res.filter((s) => safeText(s.status) === f);

    if (q) {
      res = res.filter((s) => {
        const blob = `${safeText(s.title)} ${safeText(s.category)} ${safeText(s.status)} ${safeText(s.desc)}`.toLowerCase();
        return blob.includes(q);
      });
    }
    return res;
  }

  function emptyStateForEmptyServices() {
    return `
      <div class="col-span-full py-16 px-5 text-center bg-white border-2 border-dashed border-slate-200 rounded-3xl">
        <div class="text-slate-300 text-[48px] mb-4">
          <i class="ri-tools-line"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900">Belum Ada Layanan</h3>
        <p class="text-slate-500 mt-2 mb-6">Buat layanan pertamamu untuk mulai menerima pesanan.</p>
        ${createUrl
          ? `<a href="${createUrl}" class="inline-flex items-center px-5 py-3 bg-[#0f766e] text-white rounded-xl font-bold hover:bg-[#0a5e58] transition-all">
                <i class="ri-add-line mr-2"></i> Buat Layanan Baru
             </a>`
          : ''}
      </div>
    `;
  }

  function emptyStateForFilter() {
    return `
      <div class="col-span-full py-16 px-5 text-center bg-white border-2 border-dashed border-slate-200 rounded-3xl">
        <i class="ri-inbox-archive-line text-[3rem] text-slate-300 block mb-3"></i>
        <h3 class="font-display text-[1.1rem] font-bold text-slate-700 mb-1.5">No services found</h3>
        <p class="text-[13px] text-slate-400">Coba ubah filter atau kata kunci pencarian.</p>
      </div>
    `;
  }

  function render() {
    const grid = $('service-grid');
    if (!grid) return;

    const rows = filterData();
    if (!rows.length) {
      grid.innerHTML = data.length === 0 ? emptyStateForEmptyServices() : emptyStateForFilter();
      return;
    }

    grid.innerHTML = rows.map((s) => {
      const badgeCls = STATUS_BADGE[s.status] || 'bg-slate-50 text-slate-600 border border-slate-100';
      const hrefShow = s.id ? `${links.showPrefix || '/freelancer/services/'}${s.id}` : null;
      const hrefEdit = s.id ? `${links.showPrefix || '/freelancer/services/'}${s.id}${links.editSuffix || '/edit'}` : null;

      const rejectionAlert = s.rejectReason ? `
        <div class="mb-3.5 p-2.5 bg-amber-50 border border-amber-100 rounded-xl flex items-center gap-2.5 animate-pulse-slow">
          <div class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
            <i class="ri-error-warning-fill text-sm"></i>
          </div>
          <span class="text-[10px] font-black text-amber-700 uppercase tracking-widest">Perlu Perbaikan</span>
        </div>
      ` : '';

      return `
        <div class="bg-white border border-slate-200 rounded-[22px] p-[18px] transition-all duration-200 hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-[0_10px_28px_rgba(2,6,23,0.08)] overflow-hidden">
          ${rejectionAlert}
          <div class="flex items-start justify-between gap-3 mb-3">
            <div class="min-w-0">
              <div class="text-slate-900 font-extrabold text-[15px] truncate">${safeText(s.title)}</div>
              <div class="text-slate-500 text-[12.5px] mt-1 truncate">
                <i class="ri-price-tag-3-line mr-1 text-slate-400"></i>
                ${safeText(s.category)}
              </div>
            </div>
            <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black uppercase tracking-wider ${badgeCls}">${safeText(s.status)}</span>
          </div>

          <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-[16px] px-4 py-3">
            <div class="text-[12px] text-slate-500 font-semibold">Starting price</div>
            <div class="text-[13px] font-extrabold text-[#0f766e]">${money(s.price)}</div>
          </div>

          ${s.desc ? `<p class="text-[12.5px] text-slate-500 mt-3 line-clamp-2">${safeText(s.desc)}</p>` : ''}

          <div class="flex gap-2 mt-4">
            ${hrefShow
              ? `<a href="${hrefShow}" class="flex-1 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] flex items-center justify-center gap-1.5 hover:bg-black transition-all">
                  <i class="ri-eye-line"></i> View
                </a>`
              : `<button type="button" class="flex-1 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] opacity-60 cursor-not-allowed">View</button>`
            }

            ${hrefEdit
              ? `<a href="${hrefEdit}" class="flex-1 py-2.5 rounded-[12px] bg-white border-[1.5px] border-slate-200 text-slate-700 font-bold text-[12.5px] flex items-center justify-center gap-1.5 hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
                  <i class="ri-pencil-line"></i> Edit
                </a>`
              : `<button type="button" class="flex-1 py-2.5 rounded-[12px] bg-white border-[1.5px] border-slate-200 text-slate-700 font-bold text-[12.5px] opacity-60 cursor-not-allowed">Edit</button>`
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
    const search = $('service-search');
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

