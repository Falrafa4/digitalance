@extends('layouts.dashboard')
@section('title', 'Dasbor Klien | Digitalance')

@section('content')
    <div class="animate-fadeUp">

        {{-- HERO / GREETING --}}
        <section class="mb-9">
            <h1 class="font-display text-[2.6rem] sm:text-[3.1rem] font-extrabold text-slate-900 leading-tight">
                Hi, {{ $user->name }}!
                <span class="inline-block align-middle">👋</span>
            </h1>
            <p class="text-slate-500 text-[1.02rem] mt-2">
                Berikut informasi proyekmu hari ini.
            </p>
        </section>

        {{-- STAT CARDS (4 box) --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-10 animate-fadeUp-delay-1">

            {{-- TOTAL ORDERS --}}
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div
                        class="w-[54px] h-[54px] rounded-[16px] bg-emerald-50 flex items-center justify-center text-[#0f766e]">
                        <i class="ri-file-list-3-line text-[22px]" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">TOTAL PESANAN</p>
                        <p class="text-[28px] font-extrabold text-slate-900 leading-tight mt-1" data-client-stat="total"
                            data-default="{{ $statsData['total'] }}">
                            {{ $statsData['total'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ACTIVE PROJECTS --}}
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="w-[54px] h-[54px] rounded-[16px] bg-blue-50 flex items-center justify-center text-blue-700">
                        <i class="ri-timer-line text-[22px]" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">PROYEK AKTIF</p>
                        <p class="text-[28px] font-extrabold text-slate-900 leading-tight mt-1" data-client-stat="active"
                            data-default="{{ $statsData['active'] }}">
                            {{ $statsData['active'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- COMPLETED --}}
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div
                        class="w-[54px] h-[54px] rounded-[16px] bg-amber-50 flex items-center justify-center text-amber-700">
                        <i class="ri-medal-line text-[22px]" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">SELESAI</p>
                        <p class="text-[28px] font-extrabold text-slate-900 leading-tight mt-1" data-client-stat="completed"
                            data-default="{{ $statsData['completed'] }}">
                            {{ $statsData['completed'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- TOTAL SPENT --}}
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div
                        class="w-[54px] h-[54px] rounded-[16px] bg-emerald-50 flex items-center justify-center text-[#0f766e]">
                        <i class="ri-wallet-3-line text-[22px]" aria-hidden="true"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">TOTAL PENGELUARAN
                        </p>
                        <p class="text-[22px] sm:text-[24px] font-extrabold text-slate-900 leading-tight mt-1"
                            data-client-stat="totalSpent"
                            data-default="Rp {{ number_format((float) $statsData['totalSpent'], 0, ',', '.') }}">
                            Rp {{ number_format((float) $statsData['totalSpent'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- LOWER GRID (kiri besar, kanan kecil) --}}
        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6 animate-fadeUp-delay-2">

            {{-- LEFT: My Projects --}}
            <div class="xl:col-span-2">
                <div class="flex items-end justify-between mb-4">
                    <div>
                        <h2 class="font-display text-[1.55rem] font-extrabold text-slate-900">Proyek Saya</h2>
                        <p class="text-slate-500 text-[0.95rem] mt-1">Pantau ringkasan order terakhir kamu.</p>
                    </div>
                    <a href="{{ route('client.orders.index') }}"
                        class="px-4 py-2 rounded-[11px] border-[1.5px] border-slate-200 bg-white text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
                        Lihat Semua
                    </a>
                </div>

                <div class="bg-white border border-slate-200 rounded-[18px] overflow-hidden" id="projects-container">
                    @if($projects->count())
                        <div class="divide-y divide-slate-100" id="project-grid">
                            @foreach($projects as $o)
                                <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-slate-900 font-extrabold text-[14.5px] truncate">
                                            {{ $o->service->title ?? $o->service->name ?? 'Layanan' }}
                                        </p>
                                        <p class="text-slate-500 text-[13px] mt-1 line-clamp-1">
                                            {{ $o->brief }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-2 mt-3">
                                            <span
                                                class="px-3 py-1 rounded-full text-[12px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $o->status }}
                                            </span>
                                            <span
                                                class="px-3 py-1 rounded-full text-[12px] font-bold bg-white text-slate-600 border border-slate-200">
                                                Deadline:
                                                {{ $o->deadline ? \Carbon\Carbon::parse($o->deadline)->format('d M Y') : '-' }}
                                            </span>
                                            <span
                                                class="px-3 py-1 rounded-full text-[12px] font-bold bg-white text-slate-600 border border-slate-200">
                                                Rp {{ number_format((float) ($o->agreed_price ?? 0), 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 sm:flex-col sm:items-end">
                                        <a href="{{ route('client.orders.show', $o->id) }}"
                                            class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">
                                            Rincian
                                        </a>
                                        @if($o->service_id && $o->service)
                                            <a href="{{ route('client.services.show', $o->service_id) }}"
                                                class="px-4 py-2.5 rounded-[12px] bg-white border-[1.5px] border-slate-200 text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
                                                Lihat Layanan
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-10">
                            <div class="border-2 border-dashed border-slate-200 rounded-[18px] p-10 text-center">
                                <div class="text-slate-300 text-[44px] mb-3">
                                    <i class="ri-inbox-2-line" aria-hidden="true"></i>
                                </div>
                                <p class="text-slate-900 font-extrabold text-[1.25rem]">Belum Ada Proyek</p>
                                <p class="text-slate-500 mt-2">Mulai order pertamamu dari katalog jasa.</p>
                                <a href="{{ route('client.services.index') }}"
                                    class="inline-flex items-center justify-center mt-5 px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
                                    Jelajah Katalog
                                    <i class="ri-arrow-right-line ml-2" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT: System Alerts --}}
            <div>
                <div class="flex items-end justify-between mb-4">
                    <div>
                        <h2 class="font-display text-[1.55rem] font-extrabold text-slate-900">Informasi Sistem</h2>
                        <p class="text-slate-500 text-[0.95rem] mt-1">Info sistem untuk akunmu.</p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-[18px] p-5">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-[48px] h-[48px] rounded-[16px] bg-emerald-50 flex items-center justify-center text-[#0f766e]">
                            <i class="ri-shield-check-line text-[22px]" aria-hidden="true"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-extrabold text-slate-900 text-[14.5px]">Semua Sistem Normal</p>
                            <p class="text-slate-500 text-[13px] mt-1">Tidak ada masalah terdeteksi.</p>
                        </div>
                    </div>
                    <div class="mt-5 p-4 rounded-[16px] bg-slate-50 border border-slate-100">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">Tips Cepat</p>
                        <p class="text-slate-600 text-[13px] mt-1">
                            Untuk mempercepat proses, isi brief yang jelas dan tentukan deadline yang realistis.
                        </p>
                    </div>
                </div>

                <div class="mt-6 bg-white border border-slate-200 rounded-[18px] p-5">
                    <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">Aksi Cepat</p>
                    <div class="mt-4 flex flex-col gap-2.5">
                        <a href="{{ route('client.talents.index') }}"
                            class="flex items-center justify-between px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 hover:border-[#0f766e] transition-all">
                            <span class="font-bold text-[13px] text-slate-800">Cari Talenta</span>
                            <i class="ri-arrow-right-line text-slate-400" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('client.orders.index') }}"
                            class="flex items-center justify-between px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 hover:border-[#0f766e] transition-all">
                            <span class="font-bold text-[13px] text-slate-800">Pesanan</span>
                            <i class="ri-arrow-right-line text-slate-400" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('client.profile') }}"
                            class="flex items-center justify-between px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 hover:border-[#0f766e] transition-all">
                            <span class="font-bold text-[13px] text-slate-800">Akun</span>
                            <i class="ri-arrow-right-line text-slate-400" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        window.__PAGE__ = {
            projects: @json($projectsData),
            stats: @json($statsData),
        };
        window.__SHOW_WELCOME__ = @json($showWelcomePopup ?? false);

        document.addEventListener('DOMContentLoaded', function () {
            if (!window.__SHOW_WELCOME__) return;
            if (localStorage.getItem('cl_welcome_dismissed')) return;

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
            localStorage.setItem('cl_welcome_dismissed', '1');
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
    <script src="{{ asset('js/dashboard/client/dashboard.js') }}" defer></script>
@endsection

@section('modals')
    @if($showWelcomePopup ?? false)
    <div id="welcome-overlay" class="fixed inset-0 z-[200] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" onclick="dismissWelcome()">
        <div id="welcome-box" class="bg-white rounded-[24px] w-full max-w-[460px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" onclick="event.stopPropagation()">
            <div class="px-8 pt-8 pb-6 text-center">
                <div class="w-[72px] h-[72px] mx-auto mb-5 rounded-full bg-teal-50 flex items-center justify-center text-[#0f766e] shadow-inner">
                    <i class="ri-magic-line text-3xl"></i>
                </div>
                <span class="inline-block px-3 py-1 bg-teal-50 border border-teal-100 text-[#0f766e] text-[11px] font-black uppercase tracking-wider rounded-full mb-3">
                    ✨ Digitalance AI
                </span>
                <h3 class="text-[1.3rem] font-black text-slate-900 mb-1">Selamat Datang, {{ $user->name }}!</h3>
                <p class="text-lg font-bold text-slate-700 mb-4">Temukan Talent Terbaik dengan AI 🚀</p>
                <p class="text-[13.5px] text-slate-500 leading-relaxed max-w-sm mx-auto">
                    Gunakan Digitalance AI untuk mencocokkan freelancer dengan kebutuhan proyekmu. Cukup pilih lowongan atau jelaskan kebutuhanmu, AI akan merekomendasikan talent yang tepat!
                </p>
            </div>
            <div class="flex gap-3 px-8 pb-8">
                <button onclick="dismissWelcome()" class="flex-1 py-3.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-[13px] hover:bg-slate-200 transition-all">
                    Nanti Saja
                </button>
                <a href="{{ route('client.ai-recommendations') }}" class="flex-1 py-3.5 rounded-xl bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] shadow-lg shadow-teal-200 transition-all text-center flex items-center justify-center gap-2">
                    Coba Rekomendasi AI
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
    </div>
    @endif
@endsection