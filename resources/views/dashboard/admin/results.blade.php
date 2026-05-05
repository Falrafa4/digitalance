@extends('layouts.dashboard')
@section('title', 'Results | Digitalance')

@section('content')
<div class="animate-fadeUp">
    <!-- Header -->
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Results</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Pantau hasil pekerjaan yang dikirimkan oleh freelancer.</p>
        </div>
    </div>

    <!-- Statistics (ID harus sesuai dengan JS) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-[14px] mb-6">
        <div class="bg-white px-6 py-5 rounded-[18px] border border-slate-200 hover:border-[#10B981] transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Paid</span>
            <span class="font-display text-[2rem] font-extrabold text-emerald-600" id="stat-paid">0</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-[18px] border border-slate-200 hover:border-amber-500 transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Pending</span>
            <span class="font-display text-[2rem] font-extrabold text-amber-500" id="stat-pending">0</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-[18px] border border-slate-200 hover:border-blue-500 transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">In Progress</span>
            <span class="font-display text-[2rem] font-extrabold text-blue-500" id="stat-in_progress">0</span>
        </div>
        <div class="bg-white px-6 py-5 rounded-[18px] border border-slate-200 hover:border-red-500 transition-all duration-300">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Cancelled</span>
            <span class="font-display text-[2rem] font-extrabold text-red-500" id="stat-cancelled">0</span>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
        <div class="flex gap-2 flex-wrap" id="filter-tabs">
            <!-- Pastikan value data-filter sesuai dengan key di database -->
            <button class="filter-tab active px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150" data-filter="all">Semua</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="paid">Paid</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="pending">Pending</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="in_progress">In Progress</button>
            <button class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]" data-filter="cancelled">Cancelled</button>
        </div>
        <div class="relative">
            <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
            <input type="text" id="result-search" placeholder="Cari order ID..." class="pl-9 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[18px] border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">ID</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Order ID</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">File</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Tanggal</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="result-tbody">
                    <!-- Data rendered by JS -->
                </tbody>
            </table>
        </div>
        <div id="result-empty" class="hidden text-center py-16 px-5">
            <i class="ri-folder-check-line text-[44px] text-slate-300 mb-3 block"></i>
            <h3 class="font-display text-[1.15rem] text-slate-900 mb-1.5 font-bold">Tidak ada hasil ditemukan</h3>
            <p class="text-slate-400 text-[13.5px]">Coba ubah filter atau kata kunci pencarian.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // PENTING: Definisi data harus ada script tag dan di atas file js
    window.__RESULTS_PAGE__ = {
        data: @json($results ?? [])
    };
</script>
<script src="{{ asset('js/dashboard/admin/results.js') }}"></script>
@endsection