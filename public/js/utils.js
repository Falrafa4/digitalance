/**
 * Global Utilities for Digitalance
 */

window.DigitalanceUtils = {
    /**
     * Escape HTML to prevent XSS
     * @param {string} str
     * @returns {string}
     */
    escapeHtml: function(str) {
        if (str === null || str === undefined) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    /**
     * Format currency to IDR
     * @param {number} amount
     * @returns {string}
     */
    formatIdr: function(amount) {
        if (amount === null || amount === undefined || isNaN(amount)) return '-';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    },

    /**
     * Format currency to IDR (short form)
     * @param {number} amount
     * @returns {string}
     */
    formatIdrShort: function(amount) {
        if (amount === null || amount === undefined || isNaN(amount)) return '-';
        const num = Number(amount);
        if (num >= 1000000) {
            return 'Rp ' + (num / 1000000).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + 'jt';
        }
        if (num >= 1000) {
            return 'Rp ' + (num / 1000).toLocaleString('id-ID', { minimumFractionDigits: 0 }) + 'rb';
        }
        return 'Rp ' + num.toLocaleString('id-ID');
    },

    /**
     * Format date to Indonesian locale
     * @param {string|Date} date
     * @param {string} format d M Y | d M Y H:i | full | relative
     * @returns {string}
     */
    formatDate: function(date, format = 'd M Y') {
        if (!date) return '-';
        try {
            const d = new Date(date);
            if (isNaN(d.getTime())) return '-';

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const monthsFull = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            const pad = (n) => String(n).padStart(2, '0');
            const day = d.getDate();
            const month = months[d.getMonth()];
            const monthFull = monthsFull[d.getMonth()];
            const year = d.getFullYear();
            const hours = pad(d.getHours());
            const minutes = pad(d.getMinutes());

            switch (format) {
                case 'd M Y H:i':
                    return `${day} ${month} ${year}, ${hours}:${minutes}`;
                case 'full':
                    return `${day} ${monthFull} ${year}, ${hours}:${minutes}`;
                case 'relative':
                    const now = new Date();
                    const diff = Math.floor((now - d) / 1000);
                    if (diff < 60) return 'Baru saja';
                    if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
                    if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
                    if (diff < 604800) return Math.floor(diff / 86400) + ' hari lalu';
                    return `${day} ${month} ${year}`;
                default:
                    return `${day} ${month} ${year}`;
            }
        } catch (e) {
            return '-';
        }
    },

    /**
     * Debounce function
     * @param {Function} func
     * @param {number} wait
     * @returns {Function}
     */
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Copy text to clipboard
     * @param {string} text
     * @returns {Promise<boolean>}
     */
    copyToClipboard: async function(text) {
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(text);
                return true;
            }
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            return true;
        } catch (err) {
            return false;
        }
    },

    /**
     * Focus Trap for Modals
     * @param {HTMLElement} element
     */
    focusTrap: function(element) {
        const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
        const firstFocusableElement = element.querySelectorAll(focusableElements)[0];
        const focusableContent = element.querySelectorAll(focusableElements);
        const lastFocusableElement = focusableContent[focusableContent.length - 1];

        element.addEventListener('keydown', function(e) {
            let isTabPressed = e.key === 'Tab' || e.keyCode === 9;

            if (!isTabPressed) {
                return;
            }

            if (e.shiftKey) {
                if (document.activeElement === firstFocusableElement) {
                    lastFocusableElement.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastFocusableElement) {
                    firstFocusableElement.focus();
                    e.preventDefault();
                }
            }
        });

        if (firstFocusableElement) firstFocusableElement.focus();
    }
};

// Global helper for toast notifications if not already defined
window.showToast = window.showToast || function(message, type = 'success') {
    if (window.DigitalanceNotifications) {
        window.DigitalanceNotifications.show(message, type);
    } else {
        console.log(`[Toast ${type}]: ${message}`);
        // Fallback or emit event
        const event = new CustomEvent('show-toast', { detail: { message, type } });
        window.dispatchEvent(event);
    }
};

