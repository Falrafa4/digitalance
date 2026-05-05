@extends('layouts.dashboard')
@section('title', 'Freelancer Dashboard | Digitalance')

@section('content')
    <div class="animate-fadeUp">
        {{-- HERO / GREETING --}}
        <section class="mb-9">
            <h1 class="font-display text-[2.6rem] sm:text-[3.1rem] font-extrabold text-slate-900 leading-tight">
                Hi, {{ Auth::user()->name ?? 'Freelancer' }}!
                <span class="inline-block align-middle">👋</span>
            </h1>
            <p class="text-slate-500 text-[1.02rem] mt-2">
                Here's what's happening with your jobs today.
            </p>
        </section>

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
                        <p class="text-[28px] font-extrabold text-slate-900 leading-tight mt-1"
                            data-stat="activeOrders">—</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="w-[54px] h-[54px] rounded-[16px] bg-blue-50 flex items-center justify-center text-blue-700">
                        <i class="ri-service-line text-[22px]"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">SERVICES</p>
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
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">AVAILABLE BALANCE</p>
                        <p class="text-[22px] sm:text-[24px] font-extrabold text-slate-900 leading-tight mt-1"
                            data-stat="balance">—</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- FILTER / SEARCH BAR --}}
        <section class="flex items-center justify-between gap-4 mb-5 flex-wrap animate-fadeUp-delay-2">
            <div class="flex gap-2 flex-wrap" id="freelancer-filter-tabs">
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150 active"
                    data-filter="all">
                    All
                </button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="orders">
                    Orders
                </button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="opportunities">
                    Opportunities
                </button>
            </div>

            <div class="relative">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
                <input type="text" id="freelancer-search" placeholder="Search title, client, status…"
                    class="pl-9 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
            </div>
        </section>

        {{-- CONTENT GRID --}}
        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6 animate-fadeUp-delay-3">
            {{-- LEFT: Latest Orders --}}
            <div class="xl:col-span-2 min-w-0">
                <div class="flex items-end justify-between mb-4 gap-3 flex-wrap">
                    <div>
                        <h2 class="font-display text-[1.55rem] font-extrabold text-slate-900">Latest Orders</h2>
                        <p class="text-slate-500 text-[0.95rem] mt-1">Track your most recent order activity.</p>
                    </div>

                    <a href="{{ route('freelancer.orders.index') }}"
                        class="px-4 py-2 rounded-[11px] border-[1.5px] border-slate-200 bg-white text-slate-700 font-bold text-[12.5px]
                                hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
                        View All
                    </a>
                </div>

                <div class="bg-white border border-slate-200 rounded-[18px] overflow-hidden">
                    <div class="divide-y divide-slate-100" id="latest-order-list"></div>
                </div>
            </div>

            {{-- RIGHT: Job Opportunities --}}
            <div class="min-w-0">
                <div class="flex items-end justify-between mb-4 gap-3 flex-wrap">
                    <div>
                        <h2 class="font-display text-[1.55rem] font-extrabold text-slate-900">Job Opportunities</h2>
                        <p class="text-slate-500 text-[0.95rem] mt-1">New jobs matching your skills.</p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-[18px] p-5">
                    <div class="grid gap-3" id="job-opp-list"></div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        // Controller should pass: $dashboardData (preferred) or $data
        // We also inject some route links so JS doesn't hardcode URLs.
        window.__FREELANCER_DASHBOARD__ = Object.assign({
            links: {
                ordersIndex: @json(route('freelancer.orders.index')),
                orderShowPrefix: @json(rtrim(url('/freelancer/orders'), '/') . '/'),
            }
        }, @json($dashboardData ?? $data ?? []));
    </script>
    <script src="{{ asset('js/dashboard/freelancer/dashboard.js') }}"></script>
@endsection