@extends('layouts.dashboard')
@section('title', 'Orders | Digitalance')

@section('content')
    <div class="content-scroll flex-1 px-8 py-7 overflow-y-auto">

        <!-- Page Header -->
        <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
            <div>
                <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Orders</h1>
                <p class="text-slate-500 text-[0.95rem] mt-1">
                    Pantau dan kelola order yang masuk ke layanan kamu.
                </p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="flex items-center justify-between gap-4 mb-4 flex-wrap animate-fadeUp-2">
            <div class="flex gap-2 flex-wrap" id="filter-tabs">
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150 active"
                    data-filter="all">All</button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="Pending">Pending</button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="In Progress">In Progress</button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="Completed">Completed</button>
                <button
                    class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                    data-filter="Revision">Revision</button>
            </div>

            <div class="relative">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
                <input type="text" id="order-search" placeholder="Cari order id, client, service, status…"
                    class="pl-9 pr-4 py-[9px] w-[300px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
            </div>
        </div>

        <!-- Orders List -->
        <div class="grid gap-3 pb-6 animate-fadeUp-3" id="order-list"></div>
    </div>
@endsection

@section('scripts')
    <script>
        window.__FREELANCER_ORDERS__ = {
            orders: @json($orders ?? []),
            links: {
                // If you later add a dedicated show route/page for freelancer orders, set it here.
                showPrefix: @json(rtrim(url('/freelancer/orders'), '/') . '/'),
            }
        };
    </script>
    <script src="{{ asset('js/dashboard/freelancer/orders.js') }}"></script>
@endsection

