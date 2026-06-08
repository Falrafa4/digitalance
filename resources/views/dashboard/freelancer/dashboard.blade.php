@extends('layouts.dashboard')
@section('title', 'Dasbor Freelancer | Digitalance')

@section('content')
    <div class="animate-fadeUp">
        {{-- HERO / GREETING --}}
        <section class="mb-9">
            <h1 class="font-display text-[2.6rem] sm:text-[3.1rem] font-extrabold text-slate-900 leading-tight">
                Hi,
                {{ optional(Auth::guard('freelancer')->user()?->skomda_student)->name ?? Auth::guard('freelancer')->user()?->email ?? 'Freelancer' }}!
                <span class="inline-block align-middle">👋</span>
            </h1>
            <p class="text-slate-500 text-[1.02rem] mt-2">
                Berikut informasi pekerjaanmu hari ini.
            </p>
        </section>

        {{-- QUICK ALERT FOR PENDING ORDERS --}}
        @php
            $dashboardSource = $dashboardData ?? $data ?? [];
            $latestOrders = $dashboardSource['latestOrders'] ?? [];
            $pendingOrder = collect($latestOrders)->where('status', 'Pending')->first();
        @endphp

        @if($pendingOrder)
    <div class="mb-8 p-4 sm:p-5 rounded-[22px] bg-teal-50/60 border border-teal-100 flex flex-col sm:flex-row items-center justify-between gap-4 animate-fadeUp shadow-sm">
        <div class="flex items-center gap-4 w-full sm:w-auto">
            <div class="w-12 h-12 shrink-0 rounded-2xl bg-teal-600 text-white flex items-center justify-center shadow-lg shadow-teal-100">
                <i class="ri-notification-3-line text-xl"></i>
            </div>
            <div>
                <h4 class="text-slate-800 font-extrabold text-[15px] leading-snug">Ada pesanan baru menunggu respons kamu!</h4>
                <p class="text-slate-600/80 text-[13px] font-medium mt-1">Segera respon untuk menjaga performa dan kepercayaan klien.</p>
            </div>
        </div>
        <a href="{{ route('freelancer.orders.show', $pendingOrder['id']) }}"
            class="w-full sm:w-auto px-6 py-3.5 bg-[#0f766e] text-white rounded-xl font-bold text-[13px] hover:bg-[#0a5e58] transition-all shadow-md shadow-teal-100 text-center shrink-0">
            Detail Order <i class="ri-arrow-right-line ml-1.5"></i>
        </a>
    </div>
@endif

        {{-- STAT CARDS --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-10 animate-fadeUp-delay-1"
            id="freelancer-stats">
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div
                        class="w-[54px] h-[54px] rounded-[16px] bg-emerald-50 flex items-center justify-center text-[#0f766e]">
                        <i class="ri-file-list-3-line text-[22px]"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">ACTIVE ORDERS</p>
                        <p class="text-[28px] font-extrabold text-slate-900 leading-tight mt-1" data-stat="activeOrders">—
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="w-[54px] h-[54px] rounded-[16px] bg-blue-50 flex items-center justify-center text-blue-700">
                        <i class="ri-service-line text-[22px]"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">LAYANAN</p>
                        <p class="text-[28px] font-extrabold text-slate-900 leading-tight mt-1" data-stat="services">—</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div
                        class="w-[54px] h-[54px] rounded-[16px] bg-amber-50 flex items-center justify-center text-amber-700">
                        <i class="ri-star-smile-line text-[22px]"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">AVG RATING</p>
                        <p class="text-[28px] font-extrabold text-slate-900 leading-tight mt-1" data-stat="avgRating">—</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div
                        class="w-[54px] h-[54px] rounded-[16px] bg-emerald-50 flex items-center justify-center text-[#0f766e]">
                        <i class="ri-wallet-3-line text-[22px]"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">AVAILABLE BALANCE
                        </p>
                        <p class="text-[22px] sm:text-[24px] font-extrabold text-slate-900 leading-tight mt-1"
                            data-stat="balance">—</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- LOADING SKELETON --}}
        <template id="freelancer-stats-skeleton">
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="skeleton w-[54px] h-[54px] rounded-[16px]"></div>
                    <div class="flex-1">
                        <div class="skeleton h-3 w-20 mb-2"></div>
                        <div class="skeleton h-8 w-16"></div>
                    </div>
                </div>
            </div>
        </template>

        {{-- FILTER / SEARCH BAR --}}
        <section class="flex items-center justify-between gap-4 mb-5 flex-wrap animate-fadeUp-delay-2">
            <div class="flex gap-2 flex-wrap" id="freelancer-filter-tabs">
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150 active"
                    data-filter="all">
                    Semua
                </button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="orders">
                    Pesanan
                </button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="opportunities">
                    Lowongan
                </button>
            </div>

            <div class="relative">
                <i
                    class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
                <input type="text" id="freelancer-search" placeholder="Cari judul, klien, status…"
                    class="pl-9 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
            </div>
        </section>

        {{-- CONTENT GRID --}}
        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6 animate-fadeUp-delay-3">
            {{-- LEFT: Latest Orders + Status Change Alert --}}
            <div class="xl:col-span-2 min-w-0">
                <div class="flex items-end justify-between mb-4 gap-3 flex-wrap">
                    <div>
                        <h2 class="font-display text-[1.55rem] font-extrabold text-slate-900">Pesanan Terbaru</h2>
                        <p class="text-slate-500 text-[0.95rem] mt-1">Lacak aktivitas pesanan terbarumu.</p>
                    </div>

                    <a href="{{ route('freelancer.orders.index') }}" class="px-4 py-2 rounded-[11px] border-[1.5px] border-slate-200 bg-white text-slate-700 font-bold text-[12.5px]
                                                hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
                        Lihat Semua
                    </a>
                </div>

                @if(!empty($dashboardData['ordersWithStatusChange']) && count($dashboardData['ordersWithStatusChange']) > 0)
                    <div class="mb-4 space-y-3">
                        @foreach($dashboardData['ordersWithStatusChange'] as $changedOrder)
                            <a href="{{ route('freelancer.orders.show', $changedOrder['id']) }}"
                                class="block p-4 rounded-xl border {{ $changedOrder['status'] == 'Revision' ? 'bg-amber-50 border-amber-200' : 'bg-emerald-50 border-emerald-200' }} hover:shadow-md transition-all">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-lg {{ $changedOrder['status'] == 'Revision' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' }} flex items-center justify-center">
                                            <i
                                                class="{{ $changedOrder['status'] == 'Revision' ? 'ri-refresh-line' : 'ri-checkbox-circle-line' }}"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 text-sm">{{ $changedOrder['title'] }}</p>
                                            <p class="text-xs text-slate-500">
                                                @if($changedOrder['status'] == 'Revision')
                                                    <span class="text-amber-700 font-semibold">Klien meminta revisi</span>
                                                @else
                                                    <span class="text-emerald-700 font-semibold">Klien menerima hasil</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase {{ $changedOrder['status'] == 'Revision' ? 'bg-amber-200 text-amber-800' : 'bg-emerald-200 text-emerald-800' }}">
                                        {{ $changedOrder['status'] }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="bg-white border border-slate-200 rounded-[18px] overflow-hidden">
                    <div class="divide-y divide-slate-100" id="latest-order-list"></div>
                </div>
            </div>

            {{-- RIGHT: Job Opportunities --}}
            <div class="min-w-0">
                <div class="flex items-end justify-between mb-4 gap-3 flex-wrap">
                    <div>
                        <h2 class="font-display text-[1.55rem] font-extrabold text-slate-900">Lowongan Pekerjaan</h2>
                        <p class="text-slate-500 text-[0.95rem] mt-1">Pekerjaan baru yang sesuai skillmu.</p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-[18px] p-5">
                    <div class="grid gap-3" id="job-opp-list"></div>
                </div>
            </div>
        </section>
    </div>
@endsection

@php
    $freelancer = Auth::guard('freelancer')->user();
    $isNewFreelancer = false;
    $studentName = optional($freelancer->skomda_student)->name ?? 'Freelancer';
@endphp
@section('scripts')
    <script>
        (function () {
            try {
                window.__FREELANCER_DASHBOARD__ = Object.assign({
                    links: {
                        ordersIndex: {!! json_encode(route('freelancer.orders.index')) !!},
                        orderShowPrefix: {!! json_encode(rtrim(url('/freelancer/orders'), '/') . '/') !!}
                    }
                }, {!! json_encode($dashboardSource) !!});
            } catch (e) {
                window.__FREELANCER_DASHBOARD__ = {};
            }

            window.__FREELANCER_STATUS__ = {!! json_encode($freelancer->status ?? null) !!};
            window.__SHOW_WELCOME__ = @json($showWelcomePopup ?? false);
        })();

        document.addEventListener('DOMContentLoaded', function () {
            if (!window.__SHOW_WELCOME__) return;
            if (localStorage.getItem('fl_welcome_dismissed')) return;

            setTimeout(function () {
                var overlay = document.getElementById('welcome-overlay');
                var box = document.getElementById('welcome-box');
                if (overlay && box) {
                    overlay.classList.remove('opacity-0', 'pointer-events-none');
                    overlay.classList.add('opacity-100', 'pointer-events-auto');
                    box.classList.remove('scale-95');
                    box.classList.add('scale-100');
                    document.body.style.overflow = 'hidden';
                }
            }, 400);
        });

        function dismissWelcome() {
            localStorage.setItem('fl_welcome_dismissed', '1');
            var overlay = document.getElementById('welcome-overlay');
            var box = document.getElementById('welcome-box');
            if (overlay) {
                overlay.classList.remove('opacity-100', 'pointer-events-auto');
                overlay.classList.add('opacity-0', 'pointer-events-none');
            }
            if (box) {
                box.classList.remove('scale-100');
                box.classList.add('scale-95');
            }
            document.body.style.overflow = '';
        }
    </script>
    <script src="{{ asset('js/dashboard/freelancer/dashboard.js') }}"></script>
@endsection

@section('modals')
    @if($showWelcomePopup ?? false)
    <div id="welcome-overlay" class="fixed inset-0 z-[200] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" onclick="dismissWelcome()">
        <div id="welcome-box" class="bg-white rounded-[24px] w-full max-w-[460px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
            <div class="px-8 pt-8 pb-6 text-center">
                <div class="w-[72px] h-[72px] mx-auto mb-5 rounded-full bg-teal-50 flex items-center justify-center text-[#0f766e] shadow-inner">
                    <i class="ri-compass-3-line text-3xl"></i>
                </div>
                <span class="inline-block px-3 py-1 bg-teal-50 border border-teal-100 text-[#0f766e] text-[11px] font-black uppercase tracking-wider rounded-full mb-3">
                    🚀 Digitalance AI
                </span>
                <h3 class="text-[1.3rem] font-black text-slate-900 mb-1">Selamat Datang, {{ $studentName }}!</h3>
                <p class="text-lg font-bold text-slate-700 mb-4">Jelajahi Peta Karir Impianmu ✨</p>
                <p class="text-[13.5px] text-slate-500 leading-relaxed max-w-sm mx-auto">
                    Tim AI Digitalance telah menyiapkan rekomendasi jalur spesialisasi khusus berdasarkan profil, jurusan, dan portofolio kamu. Yuk, lihat peta karir pribadimu dan ajukan verifikasi akun!
                </p>
            </div>
            <div class="flex gap-3 px-8 pb-8">
                <button onclick="dismissWelcome()" class="flex-1 py-3.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-[13px] hover:bg-slate-200 transition-all">
                    Nanti Saja
                </button>
                <a href="{{ route('freelancer.career-mapping') }}" class="flex-1 py-3.5 rounded-xl bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] shadow-lg shadow-teal-200 transition-all text-center flex items-center justify-center gap-2">
                    Jelajahi Pemetaan Karir AI
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
    </div>
    @endif
@endsection