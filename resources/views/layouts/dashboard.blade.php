<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dasbor')</title>

    <x-fonts />
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
    <x-dashboard-css />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            if (!sidebar || !toggle || !overlay) return;

            toggle.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });

        });
    </script>
    <script src="{{ asset('js/utils.js') }}"></script>
    <script src="{{ asset('js/dashboard/shared/notification-drawer.js') }}"></script>
    <script src="{{ asset('js/dashboard/shared/flash.js') }}"></script>

    {{-- Alpine (used by various dashboard pages for interactive UI) --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- Pusher + Echo bootstrap for realtime chat (uses env meta tags) --}}
    <meta name="pusher-key" content="{{ env('PUSHER_APP_KEY') }}">
    <meta name="pusher-cluster" content="{{ env('PUSHER_APP_CLUSTER') }}">
    <meta name="pusher-host" content="{{ env('PUSHER_HOST') }}">
    <meta name="pusher-scheme" content="{{ env('PUSHER_SCHEME', 'https') }}">
    <script src="https://js.pusher.com/8.5.0/pusher.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.0/dist/echo.iife.js" defer></script>
    <script src="{{ asset('js/dashboard/echo-bootstrap.js') }}" defer></script>

    @yield('styles')
    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-900 font-sans h-screen overflow-hidden">
    {{-- Skip to main content link for accessibility --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[9999] focus:px-4 focus:py-2 focus:bg-[#0f766e] focus:text-white focus:rounded-lg focus:font-bold focus:text-sm">
        Lewati ke konten utama
    </a>

    <div class="flex h-screen overflow-hidden">
        <button id="sidebarToggle"
            class="lg:hidden fixed top-4 left-4 z-50 p-3 rounded-xl border border-slate-200 bg-white shadow-md">
            <i class="ri-menu-line text-xl"></i>
        </button>

        <!-- Mobile Overlay -->
        <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-30 lg:hidden">
        </div>

        <!-- Sidebar (role-aware) -->
        <x-sidebar />

        <!-- Main -->
        <main id="main-content"
            class="flex-1 px-4 md:px-6 lg:px-11 py-5 lg:py-7 overflow-y-auto min-w-0 focus:outline-none">
     <!-- Header (role-aware) -->
     <x-header :notif-unread-count="$notifUnreadCount" />
     <x-flash />

     @yield('content')
        </main>
    </div>

    @yield('modals')

     <!-- Toast Container -->
     <div id="toast-container" role="region" aria-label="Notifications" aria-live="polite"></div>

     @yield('scripts')
     @stack('scripts')
</body>

</html>