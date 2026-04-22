<script>
(function () {
  function initPager(root) {
    const list = root.querySelector('[data-pager-list]');
    const items = Array.from(list.querySelectorAll('[data-pager-item]'));
    const size = Number(root.getAttribute('data-page-size') || 8);

    const prevBtn = root.querySelector('[data-pager-prev]');
    const nextBtn = root.querySelector('[data-pager-next]');
    const info = root.querySelector('[data-pager-info]');
    const numbers = root.querySelector('[data-pager-numbers]');

    let page = 1;
    const totalPages = Math.max(1, Math.ceil(items.length / size));

    function render() {
      const start = (page - 1) * size;
      const end = start + size;

      items.forEach((el, i) => {
        el.style.display = (i >= start && i < end) ? '' : 'none';
      });

      if (info) info.textContent = `Page ${page} / ${totalPages}`;
      if (prevBtn) prevBtn.disabled = page === 1;
      if (nextBtn) nextBtn.disabled = page === totalPages;

      if (numbers) {
        numbers.innerHTML = '';
        const max = Math.min(totalPages, 7);
        let from = Math.max(1, page - 3);
        let to = Math.min(totalPages, from + (max - 1));
        from = Math.max(1, to - (max - 1));

        for (let p = from; p <= to; p++) {
          const b = document.createElement('button');
          b.type = 'button';
          b.textContent = p;
          b.className =
            'px-3 py-2 rounded-[12px] text-[12px] font-extrabold border transition ' +
            (p === page
              ? 'bg-slate-900 text-white border-slate-900'
              : 'bg-white text-slate-700 border-slate-200 hover:border-[#0f766e] hover:text-[#0f766e]');
          b.addEventListener('click', () => { page = p; render(); });
          numbers.appendChild(b);
        }
      }
    }

    if (prevBtn) prevBtn.addEventListener('click', () => { if (page > 1) { page--; render(); }});
    if (nextBtn) nextBtn.addEventListener('click', () => { if (page < totalPages) { page++; render(); }});

    render();
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-client-pager]').forEach(initPager);
  });
})();
</script>