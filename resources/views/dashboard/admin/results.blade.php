@extends('layouts.dashboard')
@section('title', 'Results | Digitalance')

@section('content')
    <div class="content-scroll flex-1 px-4 sm:px-8 py-7 overflow-y-auto">

        @section('Header')
            <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
                <div>
                    <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Results</h1>
                    <p class="text-slate-500 text-[0.95rem] mt-1">Pantau hasil pekerjaan yang dikirimkan oleh freelancer.</p>
                </div>
                <span
                    class="inline-flex items-center gap-2 px-[18px] py-[10px] bg-blue-50 text-blue-600 font-bold text-[13px] rounded-[12px] border border-blue-200">
                    <i class="ri-eye-line"></i> Read Only
                </span>
            </div>
        @endsection

        @section('Statistics')
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-[14px] mb-6 animate-fadeUp-1">
                <div
                    class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
                    <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Total Hasil</span>
                    <span class="font-display text-[2rem] font-extrabold text-slate-900" id="stat-total">—</span>
                </div>
                <div
                    class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
                    <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Diterima</span>
                    <span class="font-display text-[2rem] font-extrabold text-emerald-600" id="stat-accepted">—</span>
                </div>
                <div
                    class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
                    <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Revisi</span>
                    <span class="font-display text-[2rem] font-extrabold text-amber-500" id="stat-revision">—</span>
                </div>
                <div
                    class="bg-white px-6 py-5 rounded-3xl border border-slate-200 hover:border-[#10B981] transition-all duration-300">
                    <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Pending</span>
                    <span class="font-display text-[2rem] font-extrabold text-slate-400" id="stat-pending">—</span>
                </div>
            </div>
        @endsection

        @section('Filter & Search')
            <div class="flex items-center justify-between gap-4 mb-6 flex-wrap animate-fadeUp-2">
                <div class="flex gap-2 flex-wrap" id="filter-tabs">
                    <button
                        class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-[#0f766e] bg-[#0f766e] text-white font-bold text-[12.5px] shadow-teal-sm cursor-pointer transition-all duration-150"
                        data-filter="all">Semua</button>
                    <button
                        class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                        data-filter="pending">Pending</button>
                    <button
                        class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                        data-filter="accepted">Diterima</button>
                    <button
                        class="filter-tab px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] cursor-pointer transition-all duration-150 hover:border-[#0f766e] hover:text-[#0f766e]"
                        data-filter="revision">Revisi</button>
                </div>
                <div class="relative">
                    <i
                        class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
                    <input type="text" id="result-search" placeholder="Cari order ID atau freelancer…"
                        class="pl-9 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                </div>
            </div>
        @endsection

        @section('Table')
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden animate-fadeUp-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">ID</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Order</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Freelancer
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">File Hasil
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Catatan
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Status
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Dikirim
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.6px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="result-tbody"></tbody>
                    </table>
                </div>
                <div id="result-empty" class="hidden text-center py-16 px-5">
                    <i class="ri-folder-check-line text-[44px] text-slate-300 mb-3 block"></i>
                    <h3 class="font-display text-[1.15rem] text-slate-900 mb-1.5 font-bold">Tidak ada hasil ditemukan</h3>
                    <p class="text-slate-400 text-[13.5px]">Coba ubah filter atau kata kunci pencarian.</p>
                </div>
            </div>
        @endsection

    </div>
@endsection

@section('modals')
    @section('Modal Detail')
        <div class="overlay fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200"
            id="modal-detail">
            <div
                class="modal-box bg-white rounded-3xl w-full max-w-[540px] max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between px-[26px] py-[22px] border-b border-slate-100 flex-shrink-0">
                    <span class="font-display text-[1.1rem] font-extrabold text-slate-900">Detail Hasil</span>
                    <button onclick="closeModal('modal-detail')"
                        class="w-[34px] h-[34px] bg-slate-100 rounded-[9px] flex items-center justify-center text-[18px] text-slate-500 cursor-pointer border-none hover:bg-red-50 hover:text-red-500 transition-all duration-150"><i
                            class="ri-close-line"></i></button>
                </div>
                <div class="px-[26px] py-[22px] overflow-y-auto flex-1" id="modal-detail-content"></div>
                <div class="flex gap-2.5 px-[26px] py-[16px] border-t border-slate-100 bg-slate-50 flex-shrink-0">
                    <button onclick="closeModal('modal-detail')"
                        class="flex-1 py-[11px] rounded-[11px] bg-slate-100 text-slate-500 font-bold text-[13px] cursor-pointer border-none hover:bg-slate-200 transition-all duration-150">Tutup</button>
                </div>
            </div>
        </div>
    @endsection

    @section('Toast')
        <div id="toast-container" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-2.5 pointer-events-none">
            <style>
                .toast {
                    pointer-events: all;
                    animation: slideInRight .25s ease both;
                }

                @keyframes slideInRight {
                    from {
                        opacity: 0;
                        transform: translateX(40px);
                    }

                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
            </style>
        </div>
    @endsection
@endsection

@section('scripts')
    <script>
        window.__RESULTS_PAGE__ = {
            data: @json($results)
        };
    </script>
    <script src="{{ asset('js/dashboard/admin/results.js') }}"></script>
@endsection