/**
 * Flash message animation logic — extracted from flash.blade.php
 * Handles slide-in entrance and auto-dismiss.
 */
document.addEventListener('DOMContentLoaded', function () {
    try {
        var wrapper = document.getElementById('global-flash');
        if (!wrapper) return;

        var flashes = wrapper.querySelectorAll('[data-flash]');
        flashes.forEach(function (f, i) {
            f.style.opacity = '0';
            f.style.transform = 'translateX(100%)';
            setTimeout(function () {
                f.style.transition = 'opacity 400ms cubic-bezier(0.34, 1.56, 0.64, 1), transform 400ms cubic-bezier(0.34, 1.56, 0.64, 1)';
                f.style.opacity = '1';
                f.style.transform = 'translateX(0)';
            }, 50 + i * 100);
        });

        flashes.forEach(function (f, i) {
            setTimeout(function () {
                if (!f.isConnected) return;
                f.style.transition = 'opacity 300ms ease, transform 300ms ease';
                f.style.opacity = '0';
                f.style.transform = 'translateX(100%)';
                setTimeout(function () { if (f.isConnected) f.remove(); }, 320);
            }, 4000 + i * 200);
        });
    } catch (e) { console.error(e); }
});
