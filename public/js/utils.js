/**
 * Digitalance Global Utilities
 * Single source of truth for all shared JS utilities.
 * Loaded on every page (public + dashboard).
 */
window.DigitalanceUtils = {

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml: function(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    },

    /**
     * Focus trap utility for accessibility (modals, drawers)
     */
    focusTrap: function(element) {
        if (!element) return;

        var focusableElements = element.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        if (focusableElements.length === 0) return;

        var firstFocusable = focusableElements[0];
        var lastFocusable = focusableElements[focusableElements.length - 1];

        element.addEventListener('keydown', function(e) {
            var isTabPressed = e.key === 'Tab' || e.keyCode === 9;
            if (!isTabPressed) return;

            if (e.shiftKey) {
                if (document.activeElement === firstFocusable) {
                    lastFocusable.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastFocusable) {
                    firstFocusable.focus();
                    e.preventDefault();
                }
            }
        });

        firstFocusable.focus();
    },

    /**
     * Debounce function for search and scroll events
     */
    debounce: function(func, wait) {
        var timeout;
        return function executedFunction() {
            var args = arguments;
            var later = function() {
                clearTimeout(timeout);
                func.apply(null, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Format currency to IDR
     */
    formatCurrency: function(value) {
        if (value === null || value === undefined || value === '') return '—';
        if (typeof value === 'string') return value;
        try {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        } catch (e) {
            return String(value);
        }
    },

    /**
     * Get CSRF token from meta tag or hidden input
     */
    getCsrfToken: function() {
        return document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="_token"]')?.value
            || '';
    },

    /**
     * Safe text conversion
     */
    safeText: function(v) {
        if (v === null || v === undefined) return '';
        return this.escapeHtml(String(v));
    },

    /**
     * Async API request wrapper
     */
    apiRequest: function(url, options) {
        var method = (options && options.method) || 'POST';
        var body = (options && options.body) || null;
        var headers = {
            'X-CSRF-TOKEN': this.getCsrfToken(),
            'Accept': 'application/json'
        };

        var payload = body;
        if (body && typeof body === 'object' && !(body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
            payload = JSON.stringify(body);
        }

        return fetch(url, { method: method, headers: headers, body: payload })
            .then(function(res) {
                var ct = res.headers.get('content-type') || '';
                if (ct.includes('application/json')) {
                    return res.json().then(function(data) {
                        if (!res.ok) throw new Error(data?.message || 'Request gagal.');
                        return data;
                    });
                }
                if (!res.ok) throw new Error('Request gagal (' + res.status + ').');
                return null;
            });
    },

    /**
     * DOM ready helper
     */
    ready: function(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }
};