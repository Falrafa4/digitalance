/**
 * Digitalance Dashboard Shared Utilities
 * DEPRECATED: Use window.DigitalanceUtils from utils.js instead.
 * This file exists for backward compatibility only.
 * Will be removed in a future update.
 */

window.DashboardUtils = window.DigitalanceUtils;

window.DashboardUtils.formatRupiah = function(value) {
    return window.DigitalanceUtils.formatRupiah(value);
};

window.DashboardUtils.safeText = function(v) {
    if (v === null || v === undefined) return '';
    return this.escapeHtml(String(v));
};

window.DashboardUtils.getCsrfToken = function() {
    return document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value
        || '';
};

window.DashboardUtils.apiRequest = function(url, options) {
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
};

window.DashboardUtils.openModal = function(overlayId) {
    var overlay = document.getElementById(overlayId);
    if (!overlay) return;
    overlay.classList.remove('opacity-0', 'pointer-events-none');
    var box = overlay.querySelector('.modal-box, aside, [role="dialog"]');
    if (box) box.classList.remove('scale-95');
};

window.DashboardUtils.closeModal = function(overlayId) {
    var overlay = document.getElementById(overlayId);
    if (!overlay) return;
    overlay.classList.add('opacity-0', 'pointer-events-none');
    var box = overlay.querySelector('.modal-box, aside, [role="dialog"]');
    if (box) box.classList.add('scale-95');
};
