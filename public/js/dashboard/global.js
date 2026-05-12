/**
 * Digitalance Dashboard Global JS
 * Extracted from layouts/dashboard.blade.php - all shared dashboard functionality
 * Safe to load on every dashboard page.
 */

(function() {
    'use strict';

    // ─── Error Boundary ───────────────────────────────────────
    window.addEventListener('error', function(e) {
        var msg = e.message || 'Unknown error';
        var src = e.filename || '';
        var line = e.lineno || 0;

        if (src && !src.includes(window.location.origin) && !src.includes('dashboard')) {
            return;
        }

        if (typeof console !== 'undefined') {
            console.warn('[JS Error Boundary]', msg, 'at', src + ':' + line);
        }

        if (msg && !msg.includes('ResizeObserver') && !msg.includes('Non-Error')) {
            window.showToast?.('Terjadi kesalahan. Halaman mungkin perlu di-refresh.', 'danger');
        }
    });

    window.addEventListener('unhandledrejection', function(e) {
        var reason = e.reason;
        if (reason && typeof reason === 'object' && reason.message) {
            if (typeof console !== 'undefined') {
                console.warn('[Unhandled Promise Rejection]', reason.message);
            }
        }
        e.preventDefault();
    });

    // ─── Toast System ────────────────────────────────────────────
    window.showToast = function(arg1, arg2, arg3) {
        var container = document.getElementById('toast-container');
        if (!container) return;

        var message = '';
        var type = 'success';

        if (arg3 !== undefined) {
            message = arg1 ? '<strong>' + arg1 + '</strong>: ' + arg2 : arg2;
            type = arg3;
        } else if (arg2 !== undefined) {
            message = arg1;
            type = arg2;
        } else {
            message = arg1;
        }

        if (type === 'error' || type === 'danger') type = 'danger';
        if (type === 'welcome') type = 'success';

        var toast = document.createElement('div');
        toast.className = 'toast toast-' + type;

        var icon = 'ri-checkbox-circle-fill';
        if (type === 'danger') icon = 'ri-close-circle-fill';
        else if (type === 'info') icon = 'ri-information-fill';

        toast.innerHTML =
            '<div class="toast-icon"><i class="' + icon + '"></i></div>' +
            '<div style="flex: 1; line-height: 1.4;">' + message + '</div>' +
            '<button class="toast-close" onclick="this.parentElement.remove()" aria-label="Tutup">' +
                '<i class="ri-close-line"></i>' +
            '</button>';

        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'polite');
        container.appendChild(toast);

        setTimeout(function() {
            if (!document.body.contains(toast)) return;
            toast.classList.add('toast-hide');
            setTimeout(function() { if (document.body.contains(toast)) toast.remove(); }, 300);
        }, 5000);
    };

    // ─── Modal System ───────────────────────────────────────────
    window.openModal = function(id) {
        var overlay = document.getElementById(id);
        if (!overlay) return;
        var box = overlay.querySelector('.modal-box') || overlay.querySelector('div > div');

        overlay.classList.remove('opacity-0', 'pointer-events-none');
        if (box) {
            box.classList.remove('scale-95');
            box.classList.add('scale-100');
        }
    };

    window.closeModal = function(id) {
        var overlay = id ? document.getElementById(id) : document.querySelector('.overlay:not(.opacity-0), .modal-overlay:not(.opacity-0)');
        if (!overlay) return;
        var box = overlay.querySelector('.modal-box') || overlay.querySelector('div > div');

        overlay.classList.add('opacity-0', 'pointer-events-none');
        if (box) {
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
        }
    };

    // ─── Event Listeners ────────────────────────────────────────
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('overlay') || e.target.classList.contains('modal-overlay')) {
            window.closeModal(e.target.id);
        }
    });

    document.addEventListener('submit', function(e) {
        var form = e.target;
        var btn = form.querySelector('button[type="submit"]');
        if (btn && !btn.classList.contains('no-loader') && !btn.classList.contains('no-auto-loader')) {
            var originalContent = btn.innerHTML;
            btn.dataset.originalContent = originalContent;
            btn.disabled = true;
            btn.setAttribute('aria-busy', 'true');
            btn.classList.add('btn-loading');

            if (form.method.toUpperCase() === 'GET') return;
            var timeoutKey = 'submit-timeout-' + btn.dataset.submitTimeoutId;
            clearTimeout(window[timeoutKey]);
            window[timeoutKey] = setTimeout(function() {
                if (btn.disabled && document.body.contains(btn)) {
                    btn.disabled = false;
                    btn.removeAttribute('aria-busy');
                    btn.classList.remove('btn-loading');
                    if (btn.dataset.originalContent) {
                        btn.innerHTML = btn.dataset.originalContent;
                    }
                }
            }, 15000);
        }
    });

    // ─── AJAX Loading Helpers ───────────────────────────────────
    window.showAjaxLoading = function(btn) {
        if (!btn) return;
        var original = btn.innerHTML;
        btn.dataset.ajaxOriginal = original;
        btn.disabled = true;
        btn.classList.add('btn-loading');
    };

    window.hideAjaxLoading = function(btn) {
        if (!btn) return;
        btn.disabled = false;
        btn.classList.remove('btn-loading');
        if (btn.dataset.ajaxOriginal) {
            btn.innerHTML = btn.dataset.ajaxOriginal;
        }
    };

    // ─── Flash Messages & Welcome ───────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        var wasShown = sessionStorage.getItem('digitalance_welcome_shown');
        if (!wasShown) {
            window.showToast('Selamat Datang!', 'Semoga harimu menyenangkan dan produktif.', 'welcome');
            sessionStorage.setItem('digitalance_welcome_shown', 'true');
        }

        var flashMessages = window.__FLASH_MESSAGES__ || [];
        flashMessages.forEach(function(item) {
            window.showToast(item.message, item.type);
        });
    });

})();