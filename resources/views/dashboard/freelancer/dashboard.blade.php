@extends('layouts.dashboard')
@section('title', 'Freelancer Dashboard | Digitalance')

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
            <div
                class="mb-8 p-4.5 rounded-[22px] bg-indigo-50 border border-indigo-100 flex flex-col sm:flex-row items-center justify-between gap-4 animate-fadeUp shadow-sm">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-200">
                        <i class="ri-notification-3-line text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-indigo-950 font-extrabold text-[15px]">Ada pesanan baru menunggu respons kamu!</h4>
                        <p class="text-indigo-700/70 text-[13px] font-medium mt-0.5">Segera respon untuk menjaga performa dan
                            kepercayaan klien.</p>
                    </div>
                </div>
                <a href="{{ route('freelancer.orders.show', $pendingOrder['id']) }}"
                    class="w-full sm:w-auto px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-[13px] hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100 text-center">
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
                                                    <span class="text-amber-700 font-semibold">Client meminta revisi</span>
                                                @else
                                                    <span class="text-emerald-700 font-semibold">Client menerima hasil</span>
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
    $isNewFreelancer = $freelancer && $freelancer->status === 'Approved' && now()->diffInHours($freelancer->created_at) < 48;
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

            window.__SHOW_ONBOARDING__ = {!! json_encode($isNewFreelancer) !!};
        })();
    </script>
    <script src="{{ asset('js/dashboard/freelancer/dashboard.js') }}"></script>
@endsection

@section('modals')
    {{-- Onboarding Modal for New Freelancers --}}
    <div id="onboarding-overlay"
        class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 {{ !$isNewFreelancer ? 'hidden' : '' }}">
        <div class="bg-white rounded-[28px] w-full max-w-lg shadow-2xl overflow-hidden">
            <div class="relative h-20 bg-gradient-to-r from-[#0f766e] to-[#10b981] flex items-center px-8">
                <div class="flex-1">
                    <h2 class="text-white font-black text-xl">Selamat Datang,
                        {{ $freelancer->skomda_student->name ?? 'Freelancer' }}! 🎉</h2>
                    <p class="text-white/80 text-[11px] font-bold uppercase tracking-wider">Panduan Memulai Freelancer</p>
                </div>
            </div>
            <div class="p-8 max-h-[60vh] overflow-y-auto">
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-teal-50 text-[#0f766e] flex items-center justify-center flex-shrink-0 font-black text-sm">
                            1</div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm mb-1">Buat Layanan (Service)</h3>
                            <p class="text-slate-500 text-xs leading-relaxed">Buat jasa yang kamu tawarkan. Isi deskripsi,
                                harga, dan kategori dengan jelas. Admin akan mereview sebelum ditampilkan ke publik.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 font-black text-sm">
                            2</div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm mb-1">Respon Order dengan Cepat</h3>
                            <p class="text-slate-500 text-xs leading-relaxed">Saat ada order masuk, segera ACC atau tolak.
                                Jika ACC, kirim penawaran harga. Respon cepat meningkatkan reputasimu.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 font-black text-sm">
                            3</div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm mb-1">Kerjakan & Kirim Hasil</h3>
                            <p class="text-slate-500 text-xs leading-relaxed">Setelah order Paid, kerjakan dan upload hasil
                                kerja via tombol "Kirim Hasil". Client akan review sebelum order selesai.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 font-black text-sm">
                            4</div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm mb-1">Penting: Aturan Wajib</h3>
                            <p class="text-slate-500 text-xs leading-relaxed">Jangan menelantarkan order. Selalu komunikasi
                                via chat jika ada kendala. Pelanggaran bisa berakibat suspend akun.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <p class="text-[11px] text-amber-800 font-bold leading-relaxed">
                        <i class="ri-information-line mr-1"></i>
                        Dengan menutup panduan ini, kamu menyetujui seluruh aturan dan ketentuan platform Digitalance.
                    </p>
                </div>
            </div>
            <div class="p-6 bg-slate-50 border-t border-slate-100 flex gap-3">
                <a href="{{ route('freelancer.services.create') }}"
                    class="flex-1 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-100 transition-all text-center">
                    Buat Service Pertama
                </a>
                <button onclick="closeOnboarding()"
                    class="flex-1 py-3 bg-[#0f766e] text-white font-bold rounded-xl text-sm hover:bg-[#0a5e58] transition-all shadow-lg shadow-teal-sm">
                    Saya Mengerti, Mulai!
                </button>
            </div>
        </div>
    </div>
@endsection