<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
    @yield('styles')
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
                        teal: {
                            deep: '#0f766e'
                        },
                        orange: '#f97316',
                    },
                    borderRadius: {
                        '2xl': '16px',
                        '3xl': '24px',
                    },
                    keyframes: {
                        fadeUp: {
                            from: {
                                opacity: '0',
                                transform: 'translateY(16px)'
                            },
                            to: {
                                opacity: '1',
                                transform: 'translateY(0)'
                            },
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
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main -->
        <main class="flex-1 px-11 py-7 overflow-y-auto min-w-0">
            <!-- Header -->
            <x-header />

            @yield('content')
        </main>
    </div>

    @yield('modals')

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- Notification Drawer -->
    <x-notification-drawer />

    <script src="{{ asset('js/dashboard/search.js') }}"></script>
    <script src="{{ asset('js/dashboard/notif-drawer.js') }}"></script>

    @yield('scripts')
</body>

</html>