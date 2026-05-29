/**
 * Digitalance Dashboard Global JS
 * Extracted from layouts/dashboard.blade.php - all shared dashboard functionality
 * Safe to load on every dashboard page.
 */

(function() {
    'use strict';

    // ─── Error Boundary ───────────────────────────────────────
    window.addEventListener('error', function(e) {
                var msg = (e.message || e.reason?.message || 'Kesalahan tidak diketahui').toLowerCase();
        var src = e.filename || '';

        // Ignore ResizeObserver loop limit errors (harmless browser warnings)
        if (msg.includes('resizeobserver') || msg.includes('loop limit')) return;
        
        // Ignore extension-related errors
        if (src.includes('extension://') || src.includes('moz-extension://')) return;

        if (src && !src.includes(window.location.origin) && !src.includes('dashboard')) {
            return;
        }

        if (typeof console !== 'undefined') {
            console.warn('[JS Error Boundary]', msg, 'at', src);
        }

        if (msg && !msg.includes('non-error')) {
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

// ─── Client-side Pager ──────────────────────────────────────
    document.querySelectorAll('[data-client-pager]').forEach(function (container) {
        var list = container.querySelector('[data-pager-list]');
        var items = list ? Array.from(list.querySelectorAll('[data-pager-item]')) : [];
        var pageSize = parseInt(container.dataset.pageSize) || 8;
        var currentPage = 1;
        var totalPages = Math.max(1, Math.ceil(items.length / pageSize));

        function showPage(page) {
            currentPage = Math.max(1, Math.min(page, totalPages));
            var start = (currentPage - 1) * pageSize;
            var end = start + pageSize;
            items.forEach(function (item, i) {
                item.style.display = (i >= start && i < end) ? '' : 'none';
            });
            updateControls();
        }

        function updateControls() {
            var info = container.querySelector('[data-pager-info]');
            if (info) {
                var start = items.length > 0 ? (currentPage - 1) * pageSize + 1 : 0;
                var end = Math.min(currentPage * pageSize, items.length);
                info.textContent = items.length > 0 ? 'Menampilkan ' + start + '-' + end + ' dari ' + items.length : 'Tidak ada data';
            }
            var prev = container.querySelector('[data-pager-prev]');
            var next = container.querySelector('[data-pager-next]');
            if (prev) prev.disabled = currentPage <= 1;
            if (next) next.disabled = currentPage >= totalPages;
            var numbers = container.querySelector('[data-pager-numbers]');
            if (numbers) {
                numbers.innerHTML = '';
                for (var i = 1; i <= totalPages; i++) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-9 h-9 rounded-[10px] font-bold text-[12px] transition-all ' +
                        (i === currentPage ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:border-[#0f766e] hover:text-[#0f766e]');
                    btn.textContent = i;
                    btn.onclick = (function (p) { return function () { showPage(p); }; })(i);
                    numbers.appendChild(btn);
                }
            }
        }

        container.querySelector('[data-pager-prev]')?.addEventListener('click', function () { showPage(currentPage - 1); });
        container.querySelector('[data-pager-next]')?.addEventListener('click', function () { showPage(currentPage + 1); });
        showPage(1);
    });

})();