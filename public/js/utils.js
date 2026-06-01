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
     * Parse a Rupiah-formatted value into a plain integer.
     */
    parseRupiahValue: function(value) {
        if (value === null || value === undefined || value === '') return '';

        if (typeof value === 'number') {
            return Number.isFinite(value) ? value : '';
        }

        var raw = String(value).trim();
        if (!raw) return '';

        var compact = raw.replace(/\s/g, '').replace(/[^0-9.,-]/g, '');

        if (compact.indexOf(',') !== -1) {
            var indonesian = compact.replace(/\./g, '').replace(/,/g, '.');
            var parsedIndonesian = Number(indonesian.replace(/[^0-9.-]/g, ''));
            return Number.isFinite(parsedIndonesian) ? parsedIndonesian : '';
        }

        var dotCount = (compact.match(/\./g) || []).length;
        if (dotCount === 1) {
            var dotParts = compact.split('.');
            var decimalPart = dotParts[1] || '';

            // Database values often arrive as "2222222.00"; treat those as plain rupiah integers.
            if (/^\d{1,2}$/.test(decimalPart)) {
                var decimalValue = Number(dotParts[0] + '.' + decimalPart);
                return Number.isFinite(decimalValue) ? decimalValue : '';
            }
        }

        // Treat dot-separated currency input as a thousand-grouped rupiah value.
        var thousandsSeparated = Number(compact.replace(/\./g, '').replace(/[^0-9.-]/g, ''));
        return Number.isFinite(thousandsSeparated) ? thousandsSeparated : '';
    },

    /**
     * Format an amount as Indonesian Rupiah without decimals.
     */
    formatRupiah: function(value) {
        var num = this.parseRupiahValue(value);
        if (num === '') return 'Rp0';

        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(num);
    },

    /**
     * Format currency to IDR
     */
    formatCurrency: function(value) {
        var num = this.parseRupiahValue(value);
        if (num === '') return '—';

        try {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(num);
        } catch (e) {
            return String(num);
        }
    },

    /**
     * Format an amount for use inside an editable Rupiah input.
     */
    formatRupiahInput: function(value) {
        var num = this.parseRupiahValue(value);
        if (num === '') return '';

        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(num);
    },

    /**
     * Initialize automatic Rupiah formatting for inputs.
     */
    initRupiahInputs: function(root) {
        var scope = root || document;
        var fields = scope.querySelectorAll('input[data-rupiah-input]');

        fields.forEach(function(input) {
            if (input.dataset.rupiahBound === '1') return;
            input.dataset.rupiahBound = '1';

            var formatValue = function() {
                input.value = window.DigitalanceUtils.formatRupiahInput(input.value);
            };

            formatValue();

            input.addEventListener('input', formatValue);
            input.addEventListener('blur', formatValue);

            var form = input.form;
            if (!form || form.dataset.rupiahSubmitBound === '1') return;

            form.dataset.rupiahSubmitBound = '1';
            var sanitizeFields = function() {
                form.querySelectorAll('input[data-rupiah-input]').forEach(function(field) {
                    var parsed = window.DigitalanceUtils.parseRupiahValue(field.value);
                    field.value = parsed === '' ? '' : String(parsed);
                });
            };

            form.addEventListener('submit', sanitizeFields);

            // Some scripts call form.submit() directly, bypassing submit event listeners.
            // Also sanitize when submit buttons are clicked.
            Array.from(form.querySelectorAll('button[type="submit"], input[type="submit"]')).forEach(function(btn) {
                btn.addEventListener('click', function () {
                    sanitizeFields();
                });
            });
        });
    },

    /**
     * Open modal by overlay ID
     */
    openModal: function(overlayId) {
        var overlay = document.getElementById(overlayId);
        if (!overlay) return;
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        var box = overlay.querySelector('.modal-box, aside, [role="dialog"]');
        if (box) box.classList.remove('scale-95');
    },

    /**
     * Close modal by overlay ID
     */
    closeModal: function(overlayId) {
        var overlay = document.getElementById(overlayId);
        if (!overlay) return;
        overlay.classList.add('opacity-0', 'pointer-events-none');
        var box = overlay.querySelector('.modal-box, aside, [role="dialog"]');
        if (box) box.classList.add('scale-95');
    },

    /**
     * Attach generic overlay click and Escape key modal handlers once.
     */
    setupOverlayListeners: function() {
        if (this._overlayListenersReady) return;
        this._overlayListenersReady = true;

        document.addEventListener('click', function(e) {
            var overlay = e.target.closest('.overlay, .modal-overlay');
            if (!overlay || e.target !== overlay) return;
            window.DigitalanceUtils.closeModal(overlay.id);
        });

        document.addEventListener('keydown', function(e) {
            if (e.key !== 'Escape') return;

            var openOverlay = Array.from(document.querySelectorAll('.overlay, .modal-overlay'))
                .reverse()
                .find(function(overlay) {
                    return !overlay.classList.contains('opacity-0')
                        && !overlay.classList.contains('pointer-events-none');
                });

            if (openOverlay) {
                window.DigitalanceUtils.closeModal(openOverlay.id);
            }
        });
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
     * DOM selector helper (single element)
     */
    $: function(id) {
        return document.getElementById(id);
    },

    /**
     * DOM selector helper (multiple elements)
     */
    $$: function(sel) {
        return document.querySelectorAll(sel);
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

window.DigitalanceUtils.ready(function() {
    window.DigitalanceUtils.initRupiahInputs();
});

// Backward compatibility alias
window.DashboardUtils = window.DigitalanceUtils;
