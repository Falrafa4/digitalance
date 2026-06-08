@php
    $isAdmin = auth('administrator')->check();
    $isFreelancer = auth('freelancer')->check();
    $isClient = auth('client')->check();

    $active = 'bg-[#0f766e] text-white shadow-teal-md';
    $inactive = 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]';

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

<aside id="sidebar" class="
    fixed lg:static
    top-0 left-0
    z-[60]
    w-[260px]
    min-w-[260px]
    h-screen
    bg-white
    border-r
    border-slate-200
    flex
    flex-col
    px-5
    py-9
    transform
    -translate-x-full
    lg:translate-x-0
    transition-transform
    duration-300
">

    <!-- Logo & Mobile Close Button -->
    <div class="flex items-center justify-between lg:justify-center mb-4">
        <div class="flex items-center justify-between lg:justify-center mb-4">
            {{-- Tag <a> ini yang bikin logonya bisa diklik ke Home --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 transition-opacity hover:opacity-80">

                    {{-- Ganti tag SVG dengan IMG. Pastikan path image-nya benar di folder public --}}
                    <img src="{{ asset('image.png') }}" alt="Logo Digitalance"
                        class="h-12 w-auto object-contain" />
                </a>
        </div>

        <button type="button"
        onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); document.getElementById('sidebarOverlay').classList.add('hidden');"
        class="lg:hidden p-1.5 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all duration-200"
        aria-label="Tutup sidebar">
        <i class="ri-close-line text-2xl"></i>
    </button>
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

            <a href="{{ route('admin.loker.index') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                            {{ request()->routeIs('admin.loker.*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('admin.loker.*') ? 'page' : '' }}">
                <i class="ri-briefcase-2-line text-[17px]" aria-hidden="true"></i> Lowongan
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
                <i class="ri-price-tag-3-line text-[17px]" aria-hidden="true"></i> Negosiasi
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

            <a href="{{ route('client.ai-recommendations') }}" class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                 {{ request()->routeIs('client.ai-recommendations*') ? $active : $inactive }}"
                aria-current="{{ request()->routeIs('client.ai-recommendations*') ? 'page' : '' }}">
                <i class="ri-magic-line text-[17px]" aria-hidden="true"></i> Rekomendasi AI
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

            <a href="{{ route('freelancer.career-mapping') }}"
                class="flex items-center gap-[11px] px-[14px] py-[11px] rounded-[11px] font-semibold text-[13.5px] transition-all duration-200
                                            {{ request()->routeIs('freelancer.career-mapping*') ? 'bg-[#0f766e] text-white shadow-teal-md' : 'text-slate-500 hover:bg-slate-100 hover:text-[#0f766e]' }}"
                aria-current="{{ request()->routeIs('freelancer.career-mapping*') ? 'page' : '' }}">
                <i class="ri-compass-3-line text-[17px]" aria-hidden="true"></i> Pemetaan Karir
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
