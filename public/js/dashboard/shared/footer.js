/**
 * Footer modal logic — extracted from footer.blade.php
 * Handles privacy modal, terms modal, focus trap, overlay click, and ESC key.
 */

function openPrivacyModal() {
    var modal = document.getElementById('privacy-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    if (window.DigitalanceUtils?.focusTrap) {
        window.DigitalanceUtils.focusTrap(modal);
    }
}

function closePrivacyModal() {
    var modal = document.getElementById('privacy-modal');
    if (modal) modal.classList.add('hidden');
}

function openTnCModal() {
    var modal = document.getElementById('tnc-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    if (window.DigitalanceUtils?.focusTrap) {
        window.DigitalanceUtils.focusTrap(modal);
    }
}

function closeTnCModal() {
    var modal = document.getElementById('tnc-modal');
    if (modal) modal.classList.add('hidden');
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closePrivacyModal(); closeTnCModal(); }
});

var privacyModal = document.getElementById('privacy-modal');
if (privacyModal) {
    privacyModal.addEventListener('click', function (e) { if (e.target === this) closePrivacyModal(); });
}

var tncModal = document.getElementById('tnc-modal');
if (tncModal) {
    tncModal.addEventListener('click', function (e) { if (e.target === this) closeTnCModal(); });
}
