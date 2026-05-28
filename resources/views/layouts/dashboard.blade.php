<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard')</title>

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
    <script src="{{ asset('js/utils.js') }}"></script>

    @yield('styles')
    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-900 font-sans h-screen overflow-hidden">
@php
    $notifUser = Auth::guard('administrator')->user()
        ?? Auth::guard('client')->user()
        ?? Auth::guard('freelancer')->user();
    $notifRole = Auth::guard('administrator')->check() ? 'admin' : (Auth::guard('client')->check() ? 'client' : (Auth::guard('freelancer')->check() ? 'freelancer' : null));

    \App\Models\Notification::where('created_at', '<', now()->subDays(30))
        ->where('is_kept', false)
        ->delete();

    $notifNotifications = $notifRole
        ? \App\Models\Notification::where('role', $notifRole)
            ->where('user_id', $notifUser->id)
            ->latest()
            ->take(30)
            ->get()
        : collect();

    $notifUnreadCount = $notifNotifications->where('is_read', false)->count();
@endphp
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

    <!-- Flash Component -->
    <x-flash />

    <!-- Toast Container -->
    <div id="toast-container" role="region" aria-label="Notifications" aria-live="polite"></div>

    <!-- Notification Drawer -->
    <x-notification-drawer />

    <script src="{{ asset('js/dashboard/confirm-modal.js') }}"></script>
    <script src="{{ asset('js/dashboard/search.js') }}"></script>
    <script src="{{ asset('js/dashboard/shared/notification-drawer.js') }}"></script>
    <script src="{{ asset('js/dashboard/global.js') }}"></script>

    {{-- Flash messages injected by controller or set inline --}}
    <script>
        window.__FLASH_MESSAGES__ = [];
        @if(session('success')) window.__FLASH_MESSAGES__.push({message: @json(session('success')), type: 'success'}); @endif
        @if(session('error')) window.__FLASH_MESSAGES__.push({message: @json(session('error')), type: 'danger'}); @endif
        @if(session('warning')) window.__FLASH_MESSAGES__.push({message: @json(session('warning')), type: 'warning'}); @endif
        @if(session('info')) window.__FLASH_MESSAGES__.push({message: @json(session('info')), type: 'info'}); @endif
        @if($errors->any()) window.__FLASH_MESSAGES__.push({message: @json($errors->first()), type: 'danger'}); @endif
    </script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>