/**
 * Digitalance Dashboard Shared Utilities
 * Extracted from duplicate implementations across admin/ and freelancer/ JS files.
 * Konsisten: satu source of truth.
 */

window.DashboardUtils = {

  $: function(id) {
    return document.getElementById(id);
  },

  $$: function(sel) {
    return Array.from(document.querySelectorAll(sel));
  },

  formatRupiah: function(number) {
    if (number === null || number === undefined || number === '') return '—';
    if (typeof number === 'string') return number;
    try {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(number);
    } catch (e) {
      return String(number);
    }
  },

  safeText: function(v) {
    if (v === null || v === undefined) return '';
    if (window.DigitalanceUtils?.escapeHtml) {
      return window.DigitalanceUtils.escapeHtml(String(v));
    }
    return String(v)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  },

  getCsrfToken: function() {
    return document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value
        || '';
  },

  async apiRequest(url, options) {
    const { method = 'POST', body = null } = options || {};
    const headers = {
      'X-CSRF-TOKEN': this.getCsrfToken(),
      'Accept': 'application/json'
    };

    let payload = body;
    if (body && typeof body === 'object' && !(body instanceof FormData)) {
      headers['Content-Type'] = 'application/json';
      payload = JSON.stringify(body);
    }

    const res = await fetch(url, { method, headers, body: payload });

    let data = null;
    const ct = res.headers.get('content-type') || '';
    if (ct.includes('application/json')) {
      try { data = await res.json(); } catch (e) {}
    }

    if (!res.ok) throw new Error(data?.message || `Request gagal (${res.status}).`);
    return data;
  },

  openModal: function(id) {
    const el = this.$(id);
    if (el) {
      el.classList.remove('opacity-0', 'pointer-events-none');
      el.style.opacity = '1';
      el.style.pointerEvents = 'all';
    }
  },

  closeModal: function(id) {
    const el = this.$(id);
    if (el) {
      el.classList.add('opacity-0', 'pointer-events-none');
      el.style.opacity = '0';
      el.style.pointerEvents = 'none';
    }
  },

  setupOverlayListeners: function(overlayClass) {
    overlayClass = overlayClass || '.overlay';
    document.querySelectorAll(overlayClass).forEach(ov => {
      ov.addEventListener('click', e => {
        if (e.target === ov) this.closeModal(ov.id);
      });
    });
  },

  showToast: function(msg, type) {
    if (window.showToast) window.showToast(msg, type);
    else alert(msg);
  },

  setActiveTab: function(tabEl, containerSel) {
    const container = containerEl || tabEl.closest('[class*="filter"]') || document;
    const tabs = (containerSel ? container.querySelectorAll(containerSel) : this.$$('#' + (tabEl.closest('[id]')?.id || '') + ' .filter-tab, [class*="filter-tabs"] .filter-tab'));
    const allTabs = this.$$(containerSel || '.filter-tab');
    allTabs.forEach(t => {
      t.classList.remove('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]', 'shadow-teal-sm');
      t.classList.add('border-slate-200', 'bg-white', 'text-slate-500');
    });
    tabEl.classList.add('active', 'bg-[#0f766e]', 'text-white', 'border-[#0f766e]', 'shadow-teal-sm');
    tabEl.classList.remove('border-slate-200', 'bg-white', 'text-slate-500');
  },

  ready: function(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }
};

window.$ = window.DashboardUtils.$;
window.$$ = window.DashboardUtils.$$;
window.openModal = window.DashboardUtils.openModal;
window.closeModal = window.DashboardUtils.closeModal;
