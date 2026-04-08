@php
    $isAdmin = Auth::guard('administrator')->check();
    $isClient = Auth::guard('client')->check();
    $isFreelancer = Auth::guard('freelancer')->check();

    // Route mapping (tetap tidak mengubah variabel existing)
    $settingsRoute = null;
    $accountRoute = null;

    if ($isAdmin) {
        $settingsRoute = 'admin.settings';
        $accountRoute = 'admin.profile';
    } elseif ($isClient) {
        // Kalau kamu belum punya settings untuk client, fallback ke profile
        $settingsRoute = Route::has('client.settings') ? 'client.settings' : 'client.profile';
        $accountRoute = 'client.profile';
    } elseif ($isFreelancer) {
        $settingsRoute = Route::has('freelancer.settings') ? 'freelancer.settings' : 'freelancer.profile';
        $accountRoute = 'freelancer.profile';
    }
@endphp

<aside class="w-[260px] min-w-[260px] bg-white border-r border-slate-200 flex flex-col px-5 py-9 h-screen">

    <!-- Logo -->
    <div class="flex justify-center mb-11">
        <div class="flex items-center gap-2.5">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="32" height="32" rx="10" fill="url(#logo-gradient)" />
                <path d="M16 7L25 11.5V20.5L16 25L7 20.5V11.5L16 7Z" fill="white" />
                <defs>
                    <linearGradient id="logo-gradient" x1="0" y1="0" x2="32" y2="32">
                        <stop stop-color="#0F766E" />
                        <stop offset="1" stop-color="#10B981" />
                    </linearGradient>
                </defs>
            </svg>
            <span class="font-display text-[1.4rem] font-extrabold text-[#0f766e]">Digitalance</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="nav-scroll flex flex-col gap-0.5 flex-1 overflow-y-auto">

        {{-- ADMIN --}}
        @if($isAdmin)
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.dashboard') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-dashboard-fill text-[17px]"></i> Dashboard
            </a>

            <a href="{{ route('admin.clients.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.clients.*', 'admin.freelancers.*', 'admin.skomda-students.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-user-line text-[17px]"></i> Client
            </a>

            <a href="{{ route('admin.admins.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.admins.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-user-star-line text-[17px]"></i> Admin
            </a>

            <a href="{{ route('admin.orders.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.orders.*') && request('status') !== 'in_progress' ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-file-list-3-line text-[17px]"></i> Orders
            </a>

            <a href="{{ route('admin.reviews.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.reviews.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-star-line text-[17px]"></i> Reviews
            </a>

            <a href="{{ route('admin.services.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.services.*', 'admin.service-categories.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-tools-line text-[17px]"></i> Services
            </a>

            <a href="{{ route('admin.transactions.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.transactions.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-bank-card-line text-[17px]"></i> Transactions
            </a>

            <a href="{{ route('admin.portofolios.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.portofolios.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-folder-user-line text-[17px]"></i> Portofolios
            </a>

            <a href="{{ route('admin.offers.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.offers.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-price-tag-3-line text-[17px]"></i> Offers
            </a>

            <a href="{{ route('admin.results.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('admin.results.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-task-line text-[17px]"></i> Results
            </a>
        @endif

        {{-- CLIENT --}}
@if($isClient)
    <a href="{{ route('client.dashboard') }}"
        class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
        {{ request()->routeIs('client.dashboard') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-dashboard-fill text-[17px]"></i> Dashboard
    </a>

    <a href="{{ route('client.services.index') }}"
        class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
        {{ request()->routeIs('client.services.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-tools-line text-[17px]"></i> Katalog Jasa
    </a>

    <a href="{{ route('client.orders.index') }}"
        class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
        {{ request()->routeIs('client.orders.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-file-list-3-line text-[17px]"></i> Orders
    </a>

    {{-- Link dummy dulu (belum ada route di web.php kamu) --}}
    <a href="{{ url('/client/find-talent') }}"
        class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
        {{ request()->is('client/find-talent') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-user-search-line text-[17px]"></i> Find Talent
    </a>

    <a href="{{ url('/client/projects') }}"
        class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
        {{ request()->is('client/projects*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-briefcase-4-line text-[17px]"></i> My Projects
    </a>

    <a href="{{ url('/client/messages') }}"
        class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
        {{ request()->is('client/messages*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-chat-3-line text-[17px]"></i> Messages
    </a>

    <a href="{{ url('/client/payment') }}"
        class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
        {{ request()->is('client/payment*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-bank-card-line text-[17px]"></i> Payment
    </a>

    <a href="{{ url('/client/history') }}"
        class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
        {{ request()->is('client/history*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
        <i class="ri-time-line text-[17px]"></i> History
    </a>
@endif

        {{-- FREELANCER --}}
        @if($isFreelancer)
            <a href="{{ route('freelancer.dashboard') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('freelancer.dashboard') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-dashboard-fill text-[17px]"></i> Dashboard
            </a>

            <a href="{{ route('freelancer.services.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('freelancer.services.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-tools-line text-[17px]"></i> Services
            </a>

            <a href="{{ route('freelancer.orders.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('freelancer.orders.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-file-list-3-line text-[17px]"></i> Orders
            </a>

            <a href="{{ route('freelancer.reviews.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('freelancer.reviews.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-star-line text-[17px]"></i> Reviews
            </a>

            <a href="{{ route('freelancer.transactions.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                {{ request()->routeIs('freelancer.transactions.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                <i class="ri-bank-card-line text-[17px]"></i> Transactions
            </a>
        @endif

    </nav>

    <!-- Footer -->
    <div class="mt-auto">
        <div class="h-px bg-slate-200 my-3.5"></div>

        <nav class="flex flex-col gap-0.5">

            @if($settingsRoute && Route::has($settingsRoute))
                <a href="{{ route($settingsRoute) }}"
                    class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                    {{ request()->routeIs($settingsRoute) ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                    <i class="ri-settings-3-line text-[17px]"></i> Settings
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200 text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]">
                    <i class="ri-settings-3-line text-[17px]"></i> Settings
                </a>
            @endif

            @if($accountRoute && Route::has($accountRoute))
                <a href="{{ route($accountRoute) }}"
                    class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                    {{ request()->routeIs($accountRoute, $accountRoute.'*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}">
                    <i class="ri-user-line text-[17px]"></i> Account
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200 text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]">
                    <i class="ri-user-line text-[17px]"></i> Account
                </a>
            @endif

            <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">
                @csrf
                <input type="hidden" name="redirect" value="{{ route('login') }}">
            </form>

            <button
                type="button"
                onclick="event.preventDefault(); if(confirm('Yakin ingin logout?')) document.getElementById('logout-form').submit();"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] text-red-500 font-semibold text-[13.5px] w-full text-left transition-all duration-200 hover:bg-red-50 hover:text-red-600 border-none bg-transparent cursor-pointer">
                <i class="ri-logout-box-line text-[17px]"></i> Logout
            </button>

        </nav>

        <p class="text-[10.5px] text-slate-400 text-center mt-5 leading-relaxed">
            &copy; 2026 Digitalance.<br />All rights reserved.
        </p>
    </div>
</aside>