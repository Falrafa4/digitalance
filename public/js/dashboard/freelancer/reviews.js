(() => {
  const page = window.__FREELANCER_REVIEWS__ || {};
  const reviewsRaw = Array.isArray(page.reviews) ? page.reviews : [];
  const links = page.links || {};

  const $ = (id) => document.getElementById(id);
  const $$ = (sel) => Array.from(document.querySelectorAll(sel));

  const safeText = (v) => (v === null || v === undefined ? '' : String(v));

  function normalizeReview(r) {
    const id = r?.id ?? null;
    const rating = Number(r?.rating ?? 0);
    const comment = r?.comment ?? r?.message ?? '';
    const created = r?.created_at ?? r?.date ?? null;

    const orderId = r?.order_id ?? r?.order?.id ?? null;
    const serviceTitle = r?.order?.service?.title ?? r?.order?.service?.name ?? r?.service_title ?? 'Service';

    return { id, rating, comment, created, orderId, serviceTitle, _raw: r };
  }

  let data = reviewsRaw.map(normalizeReview);

  function activeFilter() {
    const tab = document.querySelector('#filter-tabs .filter-tab.active');
    return tab?.dataset?.filter || 'all';
  }

  function query() {
    return ($('review-search')?.value || '').trim().toLowerCase();
  }

  function filterData() {
    const f = activeFilter();
    const q = query();

    let res = data;
    if (f !== 'all') {
      const n = Number(f);
      res = res.filter((r) => Math.round(r.rating) === n);
    }

    if (q) {
      res = res.filter((r) => {
        const blob = `${safeText(r.orderId)} ${safeText(r.serviceTitle)} ${safeText(r.comment)}`.toLowerCase();
        return blob.includes(q);
      });
    }
    return res;
  }

  function stars(n) {
    const x = Math.max(0, Math.min(5, Math.round(Number(n) || 0)));
    return `
      <div class="flex items-center gap-1">
        ${Array.from({ length: 5 }).map((_, i) => `
          <i class="ri-star-fill text-[14px] ${i < x ? 'text-amber-400' : 'text-slate-200'}"></i>
        `).join('')}
        <span class="ml-1 text-[12px] font-bold text-slate-500">${x}.0</span>
      </div>
    `;
  }

  function emptyState() {
    return `
      <div class="col-span-full py-16 px-5 text-center bg-white border-2 border-dashed border-slate-200 rounded-3xl">
        <i class="ri-star-smile-line text-[3rem] text-slate-300 block mb-3"></i>
        <h3 class="font-display text-[1.1rem] font-bold text-slate-700 mb-1.5">No reviews found</h3>
        <p class="text-[13px] text-slate-400">Coba ubah filter atau kata kunci pencarian.</p>
      </div>
    `;
  }

  function render() {
    const grid = $('review-grid');
    if (!grid) return;

    const rows = filterData();
    if (!rows.length) {
      grid.innerHTML = emptyState();
      return;
    }

    grid.innerHTML = rows.map((r) => {
      const href = r.orderId ? `${links.detailPrefix || '/freelancer/reviews/order/'}${r.orderId}` : null;

      return `
        <div class="bg-white border border-slate-200 rounded-[22px] p-[18px] transition-all duration-200 hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-[0_10px_28px_rgba(2,6,23,0.08)] overflow-hidden">
          <div class="flex items-start justify-between gap-3 mb-3">
            <div class="min-w-0">
              <div class="text-slate-900 font-extrabold text-[15px] truncate">${safeText(r.serviceTitle)}</div>
              <div class="text-slate-500 text-[12.5px] mt-1">
                <i class="ri-file-list-3-line text-slate-400 mr-1"></i>
                Order #${safeText(r.orderId || '—')}
              </div>
            </div>
            ${stars(r.rating)}
          </div>

          ${r.comment
            ? `<p class="text-[13px] text-slate-600 leading-relaxed line-clamp-4">${safeText(r.comment)}</p>`
            : `<p class="text-[13px] text-slate-400 italic">Tidak ada komentar.</p>`
          }

          <div class="mt-4 flex items-center justify-between gap-2">
            <div class="text-[12px] text-slate-400 font-semibold">
              <i class="ri-time-line mr-1"></i>${safeText(r.created || '—')}
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
    const search = $('review-search');
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

