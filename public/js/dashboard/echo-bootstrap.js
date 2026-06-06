(function(){
    // Reverb speaks the Pusher protocol, so this dashboard bootstrap uses
    // the CDN Echo build with Reverb runtime values from Blade meta tags.

    if (window.Echo && typeof window.Echo.private === 'function') {
        return;
    }

    var reverbKey = document.querySelector('meta[name="reverb-key"]')?.content || '';
    var reverbHost = document.querySelector('meta[name="reverb-host"]')?.content || window.location.hostname;
    var reverbPort = parseInt(document.querySelector('meta[name="reverb-port"]')?.content || '', 10);
    var reverbScheme = document.querySelector('meta[name="reverb-scheme"]')?.content || window.location.protocol.replace(':', '');
    var useTLS = reverbScheme === 'https';

    if (!reverbPort || Number.isNaN(reverbPort)) {
        reverbPort = useTLS ? 443 : 80;
    }

    var EchoConstructor = typeof window.Echo === 'function' ? window.Echo : (typeof Echo === 'function' ? Echo : null);

    if (!EchoConstructor || typeof Pusher === 'undefined' || !reverbKey) {
        return;
    }

    window.Pusher = Pusher;
    window.Echo = new EchoConstructor({
        broadcaster: 'pusher',
        key: reverbKey,
        cluster: 'mt1',
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: useTLS,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        }
    });
})();
