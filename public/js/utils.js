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
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }
};
