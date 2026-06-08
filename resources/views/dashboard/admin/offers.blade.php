@extends('layouts.dashboard')
@section('title', 'Negosiasi | Digitalance')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/offers.css') }}">
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Negosiasi</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Pantau percakapan negosiasi antara klien dan freelancer.</p>
        </div>
        <div class="relative">
            <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[15px]"></i>
            <input type="text" id="nego-search" placeholder="Cari pesan, ID order..."
                class="pl-10 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] transition-all" />
        </div>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-200 overflow-hidden animate-fadeUp-2">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="nego-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Nego ID</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Order Ref</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Pengirim</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Isi Pesan</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Waktu</th>
                    </tr>
                </thead>
                <tbody id="nego-tbody" class="divide-y divide-slate-50">
                </tbody>
            </table>
        </div>

        <div id="nego-pagination"
            class="flex items-center justify-between px-6 py-4 bg-slate-50 border-t border-slate-100">
            <div class="text-sm text-slate-600">
                Menampilkan <span id="nego-showing-start">0</span> sampai <span id="nego-showing-end">0</span> dari
                <span id="nego-total">0</span> hasil
            </div>
            <div class="flex gap-2">
                <button id="nego-prev-btn"
                    class="px-3 py-1 text-sm bg-white border border-slate-200 rounded hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Sebelumnya</button>
                <button id="nego-next-btn"
                    class="px-3 py-1 text-sm bg-white border border-slate-200 rounded hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Selanjutnya</button>
            </div>
        </div>

        <div id="nego-empty" class="py-20 text-center hidden">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-discuss-line text-3xl text-slate-300"></i>
            </div>
            <h3 class="text-slate-700 font-bold text-lg">Belum ada negosiasi</h3>
            <p class="text-slate-400">Log percakapan negosiasi belum terekam.</p>
        </div>
    </div>
@endsection

@section('modals')
    <div id="modal-nego-overlay"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[99] hidden flex items-center justify-center transition-all duration-300">
        <div id="modal-nego-box"
            class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 overflow-hidden transform transition-all">
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var negotiationsData = @json($negotiations);
        window.__OFFERS_PAGE__ = { negotiations: negotiationsData };
    </script>
    <script src="{{ asset('js/dashboard/admin/offers.js') }}"></script>
@endsection