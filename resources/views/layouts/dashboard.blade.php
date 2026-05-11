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
    @vite(['resources/js/app.js'])

    @yield('styles')
    @stack('styles')

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        display: ['Sora', 'sans-serif'],
                    },
                    colors: {
                        primary: '#0F766E',
                        'primary-light': '#10B981',
                        teal: { deep: '#0f766e' },
                        orange: '#f97316',
                    },
                    borderRadius: { '2xl': '16px', '3xl': '24px' },
                    keyframes: {
                        fadeUp: {
                            from: { opacity: '0', transform: 'translateY(16px)' },
                            to: { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                    animation: {
                        fadeUp: 'fadeUp 0.6s ease both',
                        'fadeUp-delay-1': 'fadeUp 0.6s 0.1s ease both',
                        'fadeUp-delay-2': 'fadeUp 0.6s 0.2s ease both',
                        'fadeUp-delay-3': 'fadeUp 0.6s 0.3s ease both',
                    },
                    boxShadow: {
                        'teal-md': '0 6px 18px rgba(15,118,110,0.22)',
                        'teal-lg': '0 10px 25px rgba(15,118,110,0.25)',
                        'teal-xl': '0 15px 30px rgba(15,118,110,0.35)',
                        'green-sm': '0 4px 12px rgba(16,185,129,0.3)',
                        'red-sm': '0 4px 12px rgba(239,68,68,0.3)',
                    },
                },
            },
        };
    </script>
</head>

<body class="bg-slate-50 text-slate-900 font-sans h-screen overflow-hidden">
    <div class="flex h-screen">
        <!-- Sidebar (role-aware) -->
        <x-sidebar />

        <!-- Main -->
        <main class="flex-1 px-11 py-7 overflow-y-auto min-w-0">
            <!-- Header (role-aware) -->
            <x-header />

            @yield('content')
        </main>
    </div>

    @yield('modals')

    <!-- Toast Container -->
    <div id="toast-container"></div>

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
            if (btn && !btn.classList.contains('no-loader')) {
                btn.classList.add('btn-loading');
                btn.disabled = true;
                
                // Safety timeout in case of failed AJAX or something
                setTimeout(() => {
                    if (btn.disabled) {
                        btn.disabled = false;
                        btn.classList.remove('btn-loading');
                    }
                }, 10000);
            }
        });

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
                window.showToast('{!! session('success') !!}', 'success');
            @endif
            @if(session('error'))
                window.showToast('{!! session('error') !!}', 'danger');
            @endif
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>