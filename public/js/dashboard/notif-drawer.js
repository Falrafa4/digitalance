(function () {
  function initNotifDrawer() {
    const btn = document.getElementById('notif-btn');
    const drawer = document.getElementById('notif-drawer');
    const panel = document.getElementById('notif-panel');
    const backdrop = document.getElementById('notif-backdrop');
    const closeBtn = document.getElementById('notif-close');

    if (!btn || !drawer || !panel || !backdrop || !closeBtn) return;

    const getRole = () => btn.getAttribute('data-role') || 'unknown';

    const open = () => {
      if (!drawer.classList.contains('hidden')) return;

      drawer.classList.remove('hidden');
      drawer.setAttribute('aria-hidden', 'false');

      requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        panel.classList.remove('translate-x-full');
      });

      const dot = btn.querySelector('.has-unread');
      if (dot) dot.remove();

      document.body.style.overflow = 'hidden';

      console.log('Opened notif for role:', getRole());
    };

    const close = () => {
      if (drawer.classList.contains('hidden')) return;

      backdrop.classList.add('opacity-0');
      panel.classList.add('translate-x-full');

      drawer.setAttribute('aria-hidden', 'true');

      setTimeout(() => {
        drawer.classList.add('hidden');
        document.body.style.overflow = '';
      }, 200);
    };

    btn.addEventListener('click', open);
    closeBtn.addEventListener('click', close);
    backdrop.addEventListener('click', close);

    document.addEventListener('keydown', (e) => {
      if (drawer.classList.contains('hidden')) return;
      if (e.key === 'Escape') close();
    });
  }

  document.addEventListener('DOMContentLoaded', initNotifDrawer);
})();