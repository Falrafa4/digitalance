document.addEventListener('DOMContentLoaded', function () {
  const openBtn = document.getElementById('privacyBtn');
  const modal = document.getElementById('privacyModal');
  const closeBtn = document.getElementById('closePrivacy');
  const closeFooterBtn = document.getElementById('closePrivacyFooter');

  function openModal() {
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
  }

  if (openBtn) openBtn.addEventListener('click', openModal);
  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (closeFooterBtn) closeFooterBtn.addEventListener('click', closeModal);

  // Close when clicking outside modal content
  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeModal();
    });
  }
});
