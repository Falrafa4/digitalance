(function(){
    // Lightweight Echo + Pusher bootstrap using CDN-loaded libs.
    // This file assumes `Echo` and `Pusher` are available globally (via CDN),
    // and initializes window.Echo with app credentials embedded at runtime by Blade.

    if (window.Echo) return;

    var pusherKey = document.querySelector('meta[name="pusher-key"]')?.content || '';
    var pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.content || '';
    var pusherHost = document.querySelector('meta[name="pusher-host"]')?.content || '';
    var useTLS = (document.querySelector('meta[name="pusher-scheme"]')?.content || 'https') === 'https';

    if (!window.Pusher || !window.Echo) {
        // Try to initialize Echo if Echo is available globally
        if (typeof Echo !== 'undefined' && typeof Pusher !== 'undefined') {
            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: pusherCluster || undefined,
                wsHost: pusherHost || undefined,
                wsPort: (location.protocol === 'https:') ? 443 : 80,
                forceTLS: useTLS,
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }
            });
        }
    }
})();