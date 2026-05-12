<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0f766e',
                        secondary: '#10b981',
                        accent: '#f97316',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Sora', 'sans-serif'],
                        display: ['Sora', 'sans-serif'],
                    },
                    boxShadow: {
                        'teal-sm': '0 4px 14px 0 rgba(15, 118, 110, 0.15)',
                        'teal-md': '0 6px 20px rgba(15, 118, 110, 0.2)',
                    }
                }
            }
        }
    </script>
    <script src="{{ asset('js/utils.js') }}"></script>

    @yield('styles')
    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-900 font-sans h-screen overflow-hidden">
    {{-- Skip to main content link for accessibility --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[9999] focus:px-4 focus:py-2 focus:bg-[#0f766e] focus:text-white focus:rounded-lg focus:font-bold focus:text-sm">
        Skip to main content
    </a>

    <div class="flex h-screen">
        <!-- Sidebar (role-aware) -->
        <x-sidebar />

        <!-- Main -->
        <main id="main-content" tabindex="-1" class="flex-1 px-11 py-7 overflow-y-auto min-w-0 focus:outline-none">
            <!-- Header (role-aware) -->
            <x-header />

            @yield('content')
        </main>
    </div>

    @yield('modals')

    <!-- Toast Container -->
    <div id="toast-container" role="region" aria-label="Notifications" aria-live="polite"></div>

    <!-- Notification Drawer -->
    <x-notification-drawer />

    <script src="{{ asset('js/dashboard/confirm-modal.js') }}"></script>
    <script src="{{ asset('js/dashboard/search.js') }}"></script>
    <script src="{{ asset('js/dashboard/notif-drawer.js') }}"></script>

    <script>
        // Global JS Error Boundary
        window.addEventListener('error', function(e) {
            const msg = e.message || 'Unknown error';
            const src = e.filename || '';
            const line = e.lineno || 0;

            // Ignore errors from third-party scripts
            if (src && !src.includes(window.location.origin) && !src.includes('dashboard')) {
                return;
            }

            // Log to console in development, silently fail in production
            if (typeof console !== 'undefined') {
                console.warn('[JS Error Boundary]', msg, 'at', src + ':' + line);
            }

            // Show toast for critical errors
            if (msg && !msg.includes('ResizeObserver') && !msg.includes('Non-Error')) {
                window.showToast?.('Terjadi kesalahan. Halaman mungkin perlu di-refresh.', 'danger');
            }
        });

        // Global unhandled promise rejection handler
        window.addEventListener('unhandledrejection', function(e) {
            const reason = e.reason;
            if (reason && typeof reason === 'object' && reason.message) {
                if (typeof console !== 'undefined') {
                    console.warn('[Unhandled Promise Rejection]', reason.message);
                }
            }
            e.preventDefault();
        });
    </script>

    <script>
        // Global Toast / Slide-in Notification System
        window.showToast = function (arg1, arg2, arg3) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            let message = '';
            let type = 'success';

            // Handle both (message, type) and (title, message, type) signatures
            if (arg3 !== undefined) {
                // (title, message, type)
                message = arg1 ? `<strong>${arg1}</strong>: ${arg2}` : arg2;
                type = arg3;
            } else if (arg2 !== undefined) {
                // (message, type)
                message = arg1;
                type = arg2;
            } else {
                // (message)
                message = arg1;
            }

            if (type === 'error' || type === 'danger') type = 'danger';
            if (type === 'welcome') type = 'success';

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;

            let icon = 'ri-checkbox-circle-fill';
            if (type === 'danger') icon = 'ri-close-circle-fill';
            else if (type === 'info') icon = 'ri-information-fill';

            toast.innerHTML = `
                <div class="toast-icon"><i class="${icon}"></i></div>
                <div style="flex: 1; line-height: 1.4;">${message}</div>
                <button class="toast-close" onclick="this.parentElement.remove()" aria-label="Tutup">
                    <i class="ri-close-line"></i>
                </button>
            `;

            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'polite');
            container.appendChild(toast);

            // Auto remove after 5s
            setTimeout(() => {
                if (!document.body.contains(toast)) return;
                toast.classList.add('toast-hide');
                setTimeout(() => { if (document.body.contains(toast)) toast.remove() }, 300);
            }, 5000);
        };

        // Standardized Modal System
        window.openModal = function(id) {
            const overlay = document.getElementById(id);
            if (!overlay) return;
            const box = overlay.querySelector('.modal-box') || overlay.querySelector('div > div');
            
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            if (box) {
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            }
        };

        window.closeModal = function(id) {
            const overlay = id ? document.getElementById(id) : document.querySelector('.overlay:not(.opacity-0), .modal-overlay:not(.opacity-0)');
            if (!overlay) return;
            const box = overlay.querySelector('.modal-box') || overlay.querySelector('div > div');
            
            overlay.classList.add('opacity-0', 'pointer-events-none');
            if (box) {
                box.classList.remove('scale-100');
                box.classList.add('scale-95');
            }
        };

        // Global Event Listeners
        document.addEventListener('click', (e) => {
            // Click outside to close modal
            if (e.target.classList.contains('overlay') || e.target.classList.contains('modal-overlay')) {
                window.closeModal(e.target.id);
            }
        });

        document.addEventListener('submit', (e) => {
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.classList.contains('no-loader') && !btn.classList.contains('no-auto-loader')) {
                const originalContent = btn.innerHTML;
                btn.dataset.originalContent = originalContent;
                btn.disabled = true;
                btn.setAttribute('aria-busy', 'true');
                btn.classList.add('btn-loading');

                // Safety timeout - restore if form doesn't navigate
                if (form.method.toUpperCase() === 'GET') return;
                const timeoutKey = 'submit-timeout-' + btn.dataset.submitTimeoutId;
                clearTimeout(window[timeoutKey]);
                window[timeoutKey] = setTimeout(() => {
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

        // AJAX form loading state
        window.showAjaxLoading = function(btn) {
            if (!btn) return;
            const original = btn.innerHTML;
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

        document.addEventListener('DOMContentLoaded', () => {
            const WELCOME_KEY = 'digitalance_welcome_shown';
            const wasShown = sessionStorage.getItem(WELCOME_KEY);

            // Welcome Notification (once per session)
            if (!wasShown) {
                window.showToast('Selamat Datang!', 'Semoga harimu menyenangkan dan produktif.', 'welcome');
                sessionStorage.setItem(WELCOME_KEY, 'true');
            }

            // Laravel Flash Messages
            @if(session('success'))
                window.showToast(@json(session('success')), 'success');
            @endif
            @if(session('error'))
                window.showToast(@json(session('error')), 'danger');
            @endif
            @if(session('warning'))
                window.showToast(@json(session('warning')), 'warning');
            @endif
            @if(session('info'))
                window.showToast(@json(session('info')), 'info');
            @endif
            @if($errors->any())
                window.showToast('Validation Error', @json($errors->first()), 'danger');
            @endif
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>