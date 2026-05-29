@php
    $isAdmin = auth('administrator')->check();
    $isFreelancer = auth('freelancer')->check();
    $isClient = auth('client')->check();

    $active = 'bg-[#0f766e] text-white shadow-teal-md';
    $inactive = 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]';

    // Route mapping (tetap tidak mengubah variabel existing)
    $settingsRoute = null;
    $accountRoute = null;
    $projectsRoute = null;

    if ($isAdmin) {
        $settingsRoute = 'admin.settings';
        $accountRoute = 'admin.profile';
    } elseif ($isClient) {
        // Kalau kamu belum punya settings untuk client, fallback ke profile
        $settingsRoute = Route::has('client.settings') ? 'client.settings' : 'client.profile';
        $accountRoute = 'client.profile';
        $projectsRoute = Route::has('client.projects.index') ? 'client.projects.index' : 'client.orders.index';
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
    <nav class="nav-scroll flex flex-col gap-0.5 flex-1 overflow-y-auto" aria-label="Navigasi utama">

        {{-- ADMIN --}}
        @if($isAdmin)
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('admin.dashboard') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : '' }}">
                <i class="ri-dashboard-fill text-[17px]" aria-hidden="true"></i> Dasbor
            </a>

            <a href="{{ route('admin.clients.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('admin.clients.*', 'admin.freelancers.*', 'admin.skomda-students.*', 'admin.user') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('admin.clients.*', 'admin.freelancers.*', 'admin.skomda-students.*', 'admin.user') ? 'page' : '' }}">
                <i class="ri-user-line text-[17px]" aria-hidden="true"></i> Pengguna
            </a>

            <a href="{{ route('admin.admins.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('admin.admins.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('admin.admins.*') ? 'page' : '' }}">
                <i class="ri-user-star-line text-[17px]" aria-hidden="true"></i> Administrator
            </a>

            <a href="{{ route('admin.orders.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('admin.orders.*') && request('status') !== 'in_progress' ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('admin.orders.*') && request('status') !== 'in_progress' ? 'page' : '' }}">
                <i class="ri-file-list-3-line text-[17px]" aria-hidden="true"></i> Pesanan
            </a>

            <a href="{{ route('admin.reviews.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('admin.reviews.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('admin.reviews.*') ? 'page' : '' }}">
                <i class="ri-star-line text-[17px]" aria-hidden="true"></i> Ulasan
            </a>

            <a href="{{ route('admin.services.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('admin.services.*', 'admin.service-categories.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('admin.services.*', 'admin.service-categories.*') ? 'page' : '' }}">
                <i class="ri-tools-line text-[17px]" aria-hidden="true"></i> Layanan
            </a>

            <a href="{{ route('admin.transactions.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('admin.transactions.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('admin.transactions.*') ? 'page' : '' }}">
                <i class="ri-bank-card-line text-[17px]" aria-hidden="true"></i> Transaksi
            </a>

            <a href="{{ route('admin.portofolios.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('admin.portofolios.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('admin.portofolios.*') ? 'page' : '' }}">
                <i class="ri-folder-user-line text-[17px]" aria-hidden="true"></i> Portofolio
            </a>

            <a href="{{ route('admin.offers.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('admin.offers.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('admin.offers.*') ? 'page' : '' }}">
                <i class="ri-price-tag-3-line text-[17px]" aria-hidden="true"></i> Penawaran
            </a>

            <a href="{{ route('admin.results.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('admin.results.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('admin.results.*') ? 'page' : '' }}">
                <i class="ri-task-line text-[17px]" aria-hidden="true"></i> Hasil
            </a>

            {{-- CLIENT --}}
        @elseif($isClient)
            <a href="{{ route('client.dashboard') }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                         {{ request()->routeIs('client.dashboard') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.dashboard') ? 'page' : '' }}">
                <i class="ri-dashboard-fill text-[17px]" aria-hidden="true"></i> Dasbor
            </a>

            <a href="{{ route('client.services.index') }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                         {{ request()->routeIs('client.services.*') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.services.*') ? 'page' : '' }}">
                <i class="ri-tools-line text-[17px]" aria-hidden="true"></i> Katalog Jasa
            </a>

            <a href="{{ route('client.orders.index') }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                         {{ request()->routeIs('client.orders.*') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.orders.*') ? 'page' : '' }}">
                <i class="ri-file-list-3-line text-[17px]" aria-hidden="true"></i> Pesanan
            </a>

            <a href="{{ route('client.talents.index') }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                         {{ request()->routeIs('client.talents.*') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.talents.*') ? 'page' : '' }}">
                <i class="ri-user-search-line text-[17px]" aria-hidden="true"></i> Cari Talenta
            </a>

            <a href="{{ route($projectsRoute) }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                         {{ request()->routeIs('client.projects.*') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.projects.*') ? 'page' : '' }}">
                <i class="ri-briefcase-4-line text-[17px]" aria-hidden="true"></i> Proyek Saya
            </a>

            <a href="{{ route('client.results.index') }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                         {{ request()->routeIs('client.results.*') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.results.*') ? 'page' : '' }}">
                <i class="ri-task-line text-[17px]" aria-hidden="true"></i> Hasil Kerja
            </a>

            <a href="{{ route('client.loker.index') }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                         {{ request()->routeIs('client.loker.*') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.loker.*') ? 'page' : '' }}">
                <i class="ri-briefcase-2-line text-[17px]" aria-hidden="true"></i> Lowongan Kerja
            </a>

            <a href="{{ route('client.messages.index') }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                         {{ request()->routeIs('client.messages.*') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.messages.*') ? 'page' : '' }}">
                <i class="ri-message-3-line text-[17px]" aria-hidden="true"></i> Pesan
            </a>

            <a href="{{ route('client.offers.index') }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                               {{ request()->routeIs('client.offers.*') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.offers.*') ? 'page' : '' }}">
                <i class="ri-price-tag-3-line text-[17px]" aria-hidden="true"></i> Penawaran
            </a>

            <a href="{{ route('client.payments.index') }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                         {{ request()->routeIs('client.payments.*') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.payments.*') ? 'page' : '' }}">
                <i class="ri-bank-card-line text-[17px]" aria-hidden="true"></i> Pembayaran
            </a>

            <a href="{{ route('client.history.index') }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                         {{ request()->routeIs('client.history.*') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.history.*') ? 'page' : '' }}">
                <i class="ri-history-line text-[17px]" aria-hidden="true"></i> Riwayat
            </a>

            {{-- FREELANCER --}}
        @elseif($isFreelancer)
            <a href="{{ route('freelancer.dashboard') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('freelancer.dashboard') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('freelancer.dashboard') ? 'page' : '' }}">
                <i class="ri-dashboard-fill text-[17px]" aria-hidden="true"></i> Dasbor
            </a>

            <a href="{{ route('freelancer.services.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('freelancer.services.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('freelancer.services.*') ? 'page' : '' }}">
                <i class="ri-tools-line text-[17px]" aria-hidden="true"></i> Layanan
            </a>

            <a href="{{ route('freelancer.orders.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('freelancer.orders.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('freelancer.orders.*') ? 'page' : '' }}">
                <i class="ri-file-list-3-line text-[17px]" aria-hidden="true"></i> Pesanan
            </a>

            <a href="{{ route('freelancer.negotiations.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('freelancer.negotiations.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('freelancer.negotiations.*') ? 'page' : '' }}">
                <i class="ri-message-3-line text-[17px]" aria-hidden="true"></i> Pesan
            </a>

            <a href="{{ route('freelancer.offers.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('freelancer.offers.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('freelancer.offers.*') ? 'page' : '' }}">
                <i class="ri-price-tag-3-line text-[17px]" aria-hidden="true"></i> Penawaran
            </a>

            <a href="{{ route('freelancer.reviews.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('freelancer.reviews.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('freelancer.reviews.*') ? 'page' : '' }}">
                <i class="ri-star-line text-[17px]" aria-hidden="true"></i> Ulasan
            </a>

            <a href="{{ route('freelancer.results.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('freelancer.results.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('freelancer.results.*') ? 'page' : '' }}">
                <i class="ri-task-line text-[17px]" aria-hidden="true"></i> Hasil
            </a>

            <a href="{{ route('freelancer.transactions.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('freelancer.transactions.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('freelancer.transactions.*') ? 'page' : '' }}">
                <i class="ri-bank-card-line text-[17px]" aria-hidden="true"></i> Transaksi
            </a>

            <a href="{{ route('freelancer.portofolios.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('freelancer.portofolios.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('freelancer.portofolios.*') ? 'page' : '' }}">
                <i class="ri-folder-user-line text-[17px]" aria-hidden="true"></i> Portofolio
            </a>

            <a href="{{ route('freelancer.loker.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                    {{ request()->routeIs('freelancer.loker.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('freelancer.loker.*') ? 'page' : '' }}">
                <i class="ri-briefcase-2-line text-[17px]" aria-hidden="true"></i> Lowongan Kerja
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
                                        {{ request()->routeIs($settingsRoute) ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                    aria-label="Settings">
                    <i class="ri-settings-3-line text-[17px]" aria-hidden="true"></i> Pengaturan
                </a>
            @endif

            @if($accountRoute && Route::has($accountRoute))
                <a href="{{ route($accountRoute) }}"
                    class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                        {{ request()->routeIs($accountRoute, $accountRoute . '*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                    aria-label="Account">
                    <i class="ri-user-line text-[17px]" aria-hidden="true"></i> Akun
                </a>
            @endif

            <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">
                @csrf
                <input type="hidden" name="redirect" value="{{ route('login') }}">
            </form>

            <button type="button"
                onclick="event.preventDefault(); customConfirm('Yakin ingin logout?').then(res => { if(res) document.getElementById('logout-form').submit(); })"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] text-red-500 font-semibold text-[13.5px] w-full text-left transition-all duration-200 hover:bg-red-50 hover:text-red-600 border-none bg-transparent cursor-pointer"
                aria-label="Logout">
                <i class="ri-logout-box-line text-[17px]" aria-hidden="true"></i> Keluar
            </button>

        </nav>

        <p class="text-[10.5px] text-slate-400 text-center mt-5 leading-relaxed">
            &copy; {{ date('Y') }} Digitalance.<br />All rights reserved.
        </p>
    </div>
</aside>