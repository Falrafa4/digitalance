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
        document.addEventListener('DOMContentLoaded', () => {
            if (!sessionStorage.getItem('welcomeShown')) {
                const welcome = document.createElement('div');
                welcome.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[9999] bg-slate-900/95 text-white px-8 py-5 rounded-[24px] shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center gap-5 animate-fadeUp backdrop-blur-xl border border-white/10 transition-all duration-500';
                welcome.innerHTML = `
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-full flex items-center justify-center text-white text-[28px] shadow-lg">
                        <i class="ri-hand-heart-fill"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-extrabold text-[1.3rem] leading-tight mb-0.5">Selamat Datang!</h3>
                        <p class="text-slate-300 text-[13px]">Semoga harimu menyenangkan dan produktif.</p>
                    </div>
                `;
                document.body.appendChild(welcome);
                sessionStorage.setItem('welcomeShown', 'true');
                setTimeout(() => {
                    welcome.style.opacity = '0';
                    welcome.style.transform = 'translate(-50%, -60%)';
                    setTimeout(() => welcome.remove(), 500);
                }, 3500);
            }
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>