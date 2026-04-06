(function () {
  function initNotifDrawer() {
    const btn = document.getElementById('notif-btn');
    const drawer = document.getElementById('notif-drawer');
    const panel = document.getElementById('notif-panel');
    const backdrop = document.getElementById('notif-backdrop');
    const closeBtn = document.getElementById('notif-close');

    if (!btn || !drawer || !panel || !backdrop || !closeBtn) return;

    const open = () => {
      drawer.classList.remove('hidden');
      drawer.setAttribute('aria-hidden', 'false');

      requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        panel.classList.remove('translate-x-full');
      });

      btn.classList.remove('has-unread');
      document.body.style.overflow = 'hidden';
    };

    const close = () => {
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