@extends('layouts.app')

@section('title', 'Digitalance - Platform Freelance SKOMDA')

@section('content')
    <!-- HERO -->
    <section class="pt-24 pb-12 sm:pb-16 bg-slate-100 min-h-[90vh] flex items-center overflow-hidden" id="home">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center w-full">
            <div class="flex flex-col gap-6 items-start text-left max-lg:items-center max-lg:text-center">
                <div
                    class="hero-anim inline-flex items-center gap-2 bg-emerald-500/10 text-emerald-600 px-4 py-2 rounded-full text-xs sm:text-sm font-bold max-w-full">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0">
                        <path
                            d="M12 16.5c-3.728 0-6.75-2.97-6.75-6.75S8.272 3 12 3s6.75 3.03 6.75 6.75-3.022 6.75-6.75 6.75z" />
                        <path
                            d="M12 21.75c-4.694 0-8.5-3.51-8.5-7.75 0-.5 4.203-1.273 9.663-1.273 5.46 0 9.663.773 9.663 1.273 0 4.24-3.806 7.75-8.5 7.75z" />
                    </svg>
                    <span class="truncate">Platform Freelance Terbaik untuk Siswa SMK Telkom Sidoarjo</span>
                </div>

                <h1
                    class="hero-anim font-display text-[clamp(2rem,5vw,3.8rem)] font-extrabold leading-[1.13] tracking-tight text-slate-900">
                    Digitalance:
                    <span class="gradient-text">Expert SKOMDA</span><br />
                    <span id="typingTarget" class="typing-cursor">Freelance Solutions</span>
                </h1>

                <p class="hero-anim text-base sm:text-lg text-slate-500 leading-relaxed max-w-xl">
                    Menghubungkan industri kreatif dengan talenta muda
                    terbaik dari SKOMDA. Karya berkualitas, harga
                    terjangkau, dedikasi tanpa batas.
                </p>

                <div class="hero-anim glass-card rounded-3xl p-5 sm:p-6 w-full">
                    <div class="flex gap-1 bg-slate-100 p-1 rounded-full w-fit mb-4">
                        <button class="role-btn active px-4 sm:px-6 py-2.5 sm:py-3 rounded-full font-bold text-xs sm:text-sm cursor-pointer border-none"
                            data-role="client">
                            Klien
                        </button>
                        <button
                            class="role-btn px-4 sm:px-6 py-2.5 sm:py-3 rounded-full font-bold text-xs sm:text-sm cursor-pointer border-none bg-transparent text-slate-500"
                            data-role="freelancer">
                            Freelancer
                        </button>
                    </div>
                    
                    <form action="{{ route('services.index') }}" method="GET" class="relative mb-4">
                        <input type="text" name="q" id="heroSearch" placeholder="Cari jasa: Web Design, Video Editing…"
                            class="w-full px-4 sm:px-6 py-4 pr-24 sm:pr-36 border-2 border-slate-200 rounded-2xl text-sm sm:text-base text-slate-900 bg-white transition-all focus:outline-none focus:border-primary" />
                        <button type="submit"
                            class="absolute right-1.5 sm:right-2 top-1.5 sm:top-2 bottom-1.5 sm:bottom-2 bg-primary text-white border-none px-4 sm:px-5 rounded-xl font-bold cursor-pointer flex items-center gap-1.5 hover:bg-teal-800 transition-all text-xs sm:text-sm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" class="shrink-0">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                            <span>Cari</span>
                        </button>
                    </form>
                    
                    <div class="flex flex-wrap gap-2 items-center">
                        <span class="text-xs font-semibold text-slate-400">Rekomendasi:</span>
                        <a href="{{ route('services.index', ['q' => 'Desain Web']) }}"
                            class="search-tag text-[10px] sm:text-xs font-bold px-3 py-1.5 sm:py-2 bg-orange-500/10 text-orange-500 rounded-lg cursor-pointer hover:bg-orange-500/20 transition-all no-underline">#Desain Web</a>
                        <a href="{{ route('services.index', ['q' => 'Desain Logo']) }}"
                            class="search-tag text-[10px] sm:text-xs font-bold px-3 py-1.5 sm:py-2 bg-orange-500/10 text-orange-500 rounded-lg cursor-pointer hover:bg-orange-500/20 transition-all no-underline">#Desain Logo</a>
                        <a href="{{ route('services.index', ['q' => 'Editor Video']) }}"
                            class="search-tag text-[10px] sm:text-xs font-bold px-3 py-1.5 sm:py-2 bg-orange-500/10 text-orange-500 rounded-lg cursor-pointer hover:bg-orange-500/20 transition-all no-underline">#Editor Video</a>
                        <a href="{{ route('services.index', ['q' => 'UI/UX']) }}"
                            class="search-tag text-[10px] sm:text-xs font-bold px-3 py-1.5 sm:py-2 bg-orange-500/10 text-orange-500 rounded-lg cursor-pointer hover:bg-orange-500/20 transition-all no-underline">#UI/UX</a>
                    </div>
                </div>
            </div>

            <div class="relative max-lg:order-first w-full flex justify-center">
                <div class="animate-float relative w-full max-w-md lg:max-w-none">
                    <div class="blob-1 absolute w-32 sm:w-44 h-32 sm:h-44 bg-emerald-400/20 rounded-full blur-3xl -top-4 -left-4 z-0"></div>
                    <div class="blob-2 absolute w-48 sm:w-64 h-48 sm:h-64 bg-teal-700/20 rounded-full blur-3xl -bottom-4 -right-4 z-0"></div>
                    
                    <div class="relative z-10 rounded-[1.5rem] sm:rounded-[2rem] p-2 sm:p-3 bg-white/50 rotate-2 shadow-2xl overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1200"
                            alt="Freelance Collaboration" decoding="async"
                            class="w-full h-[260px] sm:h-[360px] lg:h-[440px] object-cover rounded-[1.2rem] sm:rounded-[1.5rem]" />
                        
                        <div
                            class="absolute bottom-6 left-6 sm:bottom-8 sm:left-8 bg-slate-900/88 backdrop-blur-xl border border-white/10 px-4 py-3 sm:px-5 sm:py-4 rounded-xl sm:rounded-2xl text-white">
                            <p class="text-xl sm:text-2xl font-bold leading-none mb-1">
                                450+
                            </p>
                            <p class="text-[10px] sm:text-xs opacity-75 font-medium whitespace-nowrap">
                                Siswa Terverifikasi
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS (Layout Grid 2-2) -->
    <section class="bg-white py-12 border-t border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 grid grid-cols-2 gap-6 sm:gap-10 stagger">
            <div class="reveal flex flex-col items-center text-center gap-3 group cursor-default">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-slate-100 rounded-2xl flex items-center justify-center text-primary transition-all group-hover:bg-primary group-hover:text-white group-hover:scale-110">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <p class="stat-number font-display text-[1.75rem] sm:text-[2rem] font-extrabold leading-none"
                    data-target="{{ $usersCount ?? 0 }}" data-suffix="+">
                    0
                </p>
                <div>
                    <p class="font-bold text-sm sm:text-[0.95rem]">Pengguna Terdaftar</p>
                    <p class="text-[11px] sm:text-xs text-slate-500 font-medium">Komunitas aktif</p>
                </div>
            </div>

            <div class="reveal flex flex-col items-center text-center gap-3 group cursor-default">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-slate-100 rounded-2xl flex items-center justify-center text-primary transition-all group-hover:bg-primary group-hover:text-white group-hover:scale-110">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                </div>
                <p class="stat-number font-display text-[1.75rem] sm:text-[2rem] font-extrabold leading-none"
                    data-target="{{ $projectsCompleted ?? 0 }}" data-suffix="+">
                    0
                </p>
                <div>
                    <p class="font-bold text-sm sm:text-[0.95rem]">Proyek Selesai</p>
                    <p class="text-[11px] sm:text-xs text-slate-500 font-medium">Berhasil diselesaikan</p>
                </div>
            </div>

            <div class="reveal flex flex-col items-center text-center gap-3 group cursor-default">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-slate-100 rounded-2xl flex items-center justify-center text-primary transition-all group-hover:bg-primary group-hover:text-white group-hover:scale-110">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <p class="stat-number font-display text-[1.5rem] sm:text-[1.75rem] lg:text-[2rem] font-extrabold leading-none" data-currency="rupiah"
                    data-target="{{ $totalTurnover ?? 0 }}">
                    0
                </p>
                <div>
                    <p class="font-bold text-sm sm:text-[0.95rem]">Total Penghasilan</p>
                    <p class="text-[11px] sm:text-xs text-slate-500 font-medium">Terbayar ke freelancer</p>
                </div>
            </div>

            <div class="reveal flex flex-col items-center text-center gap-3 group cursor-default">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 bg-slate-100 rounded-2xl flex items-center justify-center text-primary transition-all group-hover:bg-primary group-hover:text-white group-hover:scale-110">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
                <p class="stat-number font-display text-[1.75rem] sm:text-[2rem] font-extrabold leading-none"
                    data-target="{{ $avgRatingFormatted ?? '0.0' }}" data-suffix="/5" data-decimal="1">
                    0
                </p>
                <div>
                    <p class="font-bold text-sm sm:text-[0.95rem]">Rating Rata-rata</p>
                    <p class="text-[11px] sm:text-xs text-slate-500 font-medium">Kepuasan klien</p>
                </div>
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE US -->
    <section class="py-16 sm:py-24" id="services">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div class="max-w-lg w-full mx-auto lg:mx-0 reveal">
                    <div
                        class="inline-block bg-orange-500/10 text-orange-500 px-4 py-2 rounded-full text-[10px] sm:text-xs font-extrabold uppercase tracking-widest mb-4">
                        Mengapa Digitalance
                    </div>
                    <h2 class="font-display text-[clamp(1.6rem,4vw,2.5rem)] font-extrabold leading-snug mb-4">
                        Platform Freelance<br /><span class="gradient-text">Khusus SKOMDA</span>
                    </h2>
                    <p class="text-sm sm:text-base lg:text-lg text-slate-500 leading-relaxed mb-8">
                        Kami memahami kebutuhan unik siswa/i SKOMDA dan
                        industri yang mencari talenta muda berbakat.
                    </p>

                    <div class="flex flex-col gap-3">
                        <div
                            class="accordion-item active bg-slate-50 border border-transparent rounded-2xl p-4 sm:p-5 cursor-pointer transition-all">
                            <div class="accordion-header flex items-center gap-3 sm:gap-4">
                                <div
                                    class="w-9 h-9 sm:w-10 sm:h-10 bg-teal-700/10 rounded-xl flex items-center justify-center text-primary shrink-0">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                        <polyline points="22 4 12 14.01 9 11.01" />
                                    </svg>
                                </div>
                                <span class="flex-1 font-bold text-sm sm:text-[1.02rem]">Siswa SKOMDA Terverifikasi</span>
                                <svg class="accordion-chevron w-5 h-5 text-slate-400 shrink-0" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </div>
                            <div class="accordion-content pl-12 sm:pl-14 text-slate-500 font-medium leading-relaxed text-xs sm:text-sm">
                                Semua freelancer terverifikasi sebagai
                                siswa/i aktif SKOMDA dengan portfolio yang
                                telah divalidasi. Jaminan kualitas dari
                                institusi terpercaya.
                            </div>
                        </div>
                        
                        <div
                            class="accordion-item bg-slate-50 border border-transparent rounded-2xl p-4 sm:p-5 cursor-pointer transition-all">
                            <div class="accordion-header flex items-center gap-3 sm:gap-4">
                                <div
                                    class="w-9 h-9 sm:w-10 sm:h-10 bg-teal-700/10 rounded-xl flex items-center justify-center text-primary shrink-0">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    </svg>
                                </div>
                                <span class="flex-1 font-bold text-sm sm:text-[1.02rem]">Pembayaran Aman &amp; Escrow</span>
                                <svg class="accordion-chevron w-5 h-5 text-slate-400 shrink-0" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </div>
                            <div class="accordion-content pl-12 sm:pl-14 text-slate-500 font-medium leading-relaxed text-xs sm:text-sm">
                                Sistem pembayaran aman dengan escrow
                                protection. Dana client terlindungi sampai
                                project selesai sesuai agreement.
                            </div>
                        </div>
                        
                        <div
                            class="accordion-item bg-slate-50 border border-transparent rounded-2xl p-4 sm:p-5 cursor-pointer transition-all">
                            <div class="accordion-header flex items-center gap-3 sm:gap-4">
                                <div
                                    class="w-9 h-9 sm:w-10 sm:h-10 bg-teal-700/10 rounded-xl flex items-center justify-center text-primary shrink-0">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                    </svg>
                                </div>
                                <span class="flex-1 font-bold text-sm sm:text-[1.02rem]">Dukungan &amp; Pendampingan 24/7</span>
                                <svg class="accordion-chevron w-5 h-5 text-slate-400 shrink-0" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </div>
                            <div class="accordion-content pl-12 sm:pl-14 text-slate-500 font-medium leading-relaxed text-xs sm:text-sm">
                                Tim support siap membantu 24/7. Plus
                                mentoring untuk freelancer pemula dari
                                senior SKOMDA yang berpengalaman.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center items-center w-full">
                    <div class="flex flex-col gap-6 sm:gap-8 w-full max-w-[460px]">
                        <div
                            class="animate-float reveal glass-card rounded-3xl p-6 sm:p-8 flex flex-col gap-4 border border-emerald-400/20">
                            <div class="grid grid-cols-2 gap-3 w-fit">
                                <div class="w-10 h-10 sm:w-11 sm:h-11 bg-emerald-500/10 rounded-xl"></div>
                                <div class="w-10 h-10 sm:w-11 sm:h-11 bg-emerald-500/10 rounded-xl"></div>
                                <div class="w-10 h-10 sm:w-11 sm:h-11 bg-emerald-500/10 rounded-xl"></div>
                                <div class="w-10 h-10 sm:w-11 sm:h-11 bg-primary rounded-xl"></div>
                            </div>
                            <div>
                                <h3 class="text-lg sm:text-xl font-extrabold mb-1">
                                    Pencocokan Cerdas
                                </h3>
                                <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">
                                    Sistem berbasis AI untuk mencocokkan klien
                                    dengan freelancer terbaik.
                                </p>
                            </div>
                        </div>
                        
                        <div
                            class="animate-float-delay reveal glass-card rounded-3xl p-6 sm:p-8 flex flex-col gap-4 border border-emerald-400/20">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center">
                                <svg width="80" height="80" viewBox="0 0 80 80" class="w-16 h-16 sm:w-20 sm:h-20">
                                    <circle cx="40" cy="40" r="32" fill="none" stroke="#e5e7eb" stroke-width="5" />
                                    <circle id="progressRing" cx="40" cy="40" r="32" fill="none" stroke="url(#pgGrad)"
                                        stroke-width="5" class="progress-ring-circle" transform="rotate(-90 40 40)" />
                                    <defs>
                                        <linearGradient id="pgGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#0F766E" />
                                            <stop offset="100%" stop-color="#10B981" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg sm:text-xl font-extrabold mb-1">
                                    Pantau Progres
                                </h3>
                                <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">
                                    Pantau progres proyek secara real-time
                                    dengan pelacakan pencapaian.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="py-16 sm:py-20 bg-slate-900 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-10 sm:mb-12 reveal">
                <div
                    class="inline-block bg-orange-500/10 text-orange-400 px-4 py-2 rounded-full text-[10px] sm:text-xs font-extrabold uppercase tracking-widest mb-4">
                    Cara Kerja
                </div>
                <h2 class="font-display text-[clamp(1.6rem,4vw,2.5rem)] font-extrabold leading-snug">
                    Mulai Journey Freelance<br /><span class="gradient-text">Dalam 3 Langkah</span>
                </h2>
            </div>

            <div class="flex justify-center mb-10 sm:mb-12">
                <div class="flex gap-1 bg-white/10 backdrop-blur-xl p-1 sm:p-1.5 rounded-full border border-white/10 max-w-full overflow-x-auto">
                    <button
                        class="switch-btn flex items-center gap-2 border-none px-4 sm:px-8 py-2.5 sm:py-3 rounded-full font-bold text-xs sm:text-sm cursor-pointer whitespace-nowrap"
                        data-target="client-flow" id="switchClient">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Untuk Client
                    </button>
                    <button
                        class="switch-btn flex items-center gap-2 border-none px-4 sm:px-8 py-2.5 sm:py-3 rounded-full font-bold text-xs sm:text-sm cursor-pointer whitespace-nowrap"
                        data-target="freelancer-flow" id="switchFreelancer">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                        </svg>
                        Untuk Freelancer
                    </button>
                </div>
            </div>

            <div class="flow-content active" id="client-flow">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 sm:mb-12 stagger">
                    <div
                        class="reveal bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 transition-all hover:bg-white/[0.09] hover:-translate-y-1">
                        <div
                            class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center text-lg font-black text-emerald-400 mb-5">
                            01
                        </div>
                        <div class="text-emerald-400 mb-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                        </div>
                        <h3 class="font-extrabold text-base mb-3">
                            Buat Proyek
                        </h3>
                        <p class="text-white/65 leading-relaxed text-xs sm:text-sm">
                            Deskripsikan proyek kamu dengan detail.
                            Tentukan budget dan timeline yang sesuai.
                        </p>
                    </div>
                    
                    <div
                        class="reveal bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 transition-all hover:bg-white/[0.09] hover:-translate-y-1">
                        <div
                            class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center text-lg font-black text-emerald-400 mb-5">
                            02
                        </div>
                        <div class="text-emerald-400 mb-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <polyline points="16 11 18 13 22 9" />
                            </svg>
                        </div>
                        <h3 class="font-extrabold text-base mb-3">
                            Tinjau Proposal
                        </h3>
                        <p class="text-white/65 leading-relaxed text-xs sm:text-sm">
                            Terima proposal dari freelancer SKOMDA
                            berbakat. Tinjau portofolio dan pilih yang
                            terbaik.
                        </p>
                    </div>
                    
                    <div
                        class="reveal bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 transition-all hover:bg-white/[0.09] hover:-translate-y-1">
                        <div
                            class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center text-lg font-black text-emerald-400 mb-5">
                            03
                        </div>
                        <div class="text-emerald-400 mb-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </div>
                        <h3 class="font-extrabold text-base mb-3">
                            Dapatkan Hasil Berkualitas
                        </h3>
                        <p class="text-white/65 leading-relaxed text-xs sm:text-sm">
                            Bekerja sama dengan freelancer, pantau progres,
                            dan terima hasil berkualitas tinggi.
                        </p>
                    </div>
                </div>
                
                <div class="flex justify-center w-full px-4">
                    <a href="{{ route('login', ['mode' => 'register', 'role' => 'client']) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-emerald-500 text-white border-none px-6 sm:px-8 py-3.5 sm:py-4 rounded-2xl font-black text-sm sm:text-base cursor-pointer transition-all shadow-[0_12px_32px_rgba(16,185,129,.25)] hover:bg-emerald-600 hover:-translate-y-0.5 no-underline">
                        <span>Buat Proyek</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="flow-content" id="freelancer-flow">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 sm:mb-12 stagger">
                    <div
                        class="reveal bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 transition-all hover:bg-white/[0.09] hover:-translate-y-1">
                        <div
                            class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center text-lg font-black text-emerald-400 mb-5">
                            01
                        </div>
                        <div class="text-emerald-400 mb-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                        <h3 class="font-extrabold text-base mb-3">
                            Buat Profil
                        </h3>
                        <p class="text-white/65 leading-relaxed text-xs sm:text-sm">
                            Buat profil menarik dengan portofolio terbaik
                            kamu. Tampilkan keahlian dan pengalaman.
                        </p>
                    </div>
                    
                    <div
                        class="reveal bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 transition-all hover:bg-white/[0.09] hover:-translate-y-1">
                        <div
                            class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center text-lg font-black text-emerald-400 mb-5">
                            02
                        </div>
                        <div class="text-emerald-400 mb-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                        </div>
                        <h3 class="font-extrabold text-base mb-3">
                            Cari Proyek
                        </h3>
                        <p class="text-white/65 leading-relaxed text-xs sm:text-sm">
                            Telusuri proyek yang cocok dengan keahlian kamu.
                            Kirim proposal yang menarik.
                        </p>
                    </div>
                    
                    <div
                        class="reveal bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 transition-all hover:bg-white/[0.09] hover:-translate-y-1">
                        <div
                            class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center text-lg font-black text-emerald-400 mb-5">
                            03
                        </div>
                        <div class="text-emerald-400 mb-4">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                            </svg>
                        </div>
                        <h3 class="font-extrabold text-base mb-3">
                            Hasilkan Uang
                        </h3>
                        <p class="text-white/65 leading-relaxed text-xs sm:text-sm">
                            Kerjakan proyek, kirim hasil terbaik, dan
                            dapatkan penghasilan langsung ke rekeningmu.
                        </p>
                    </div>
                </div>
                
                <div class="flex justify-center w-full px-4">
                    <a href="{{ route('login', ['mode' => 'register', 'role' => 'freelancer']) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-emerald-500 text-white border-none px-6 sm:px-8 py-3.5 sm:py-4 rounded-2xl font-black text-sm sm:text-base cursor-pointer transition-all shadow-[0_12px_32px_rgba(16,185,129,.25)] hover:bg-emerald-600 hover:-translate-y-0.5 no-underline">
                        <span>Mulai Freelance</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS (Hanya Bintang 5 & Maksimal Top 6) -->
    <section class="py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-10 sm:mb-12 reveal">
                <div
                    class="inline-block bg-orange-500/10 text-orange-500 px-4 py-2 rounded-full text-[10px] sm:text-xs font-extrabold uppercase tracking-widest mb-4">
                    Testimoni
                </div>
                <h2 class="font-display text-[clamp(1.6rem,4vw,2.5rem)] font-extrabold">
                    Apa Kata <span class="gradient-text">Mereka?</span>
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 stagger">
                @php
                    // Menyaring ulasan dengan rating hanya bernilai 5, kemudian mengambil 6 data teratas
                    $filteredTestimonials = ($testimonials ?? collect())
                        ->filter(function($review) {
                            return ($review->rating ?? 0) == 5;
                        })
                        ->take(6);
                @endphp

                @foreach($filteredTestimonials as $review)
                    @php
                        $author = optional($review->order->client)->name
                            ?? optional(optional($review->order->service)->freelancer->skomda_student)->name
                            ?? 'Pengguna';
                        $role = optional($review->order->client)->name ? 'Klien' : 'Terverifikasi';
                        $position = optional($review->order->client)->company ?? optional($review->order->service->freelancer->skomda_student)->major ?? '';

                        // Tentukan foto profil: prefer client -> freelancer, fallback ke placeholder
                        $client = $review->order->client ?? null;
                        $freelancer = optional($review->order->service)->freelancer ?? null;
                        $photo = $client->profile_photo ?? ($freelancer->profile_photo ?? null);
                        $photoUrl = $photo ? asset('storage/' . $photo) : asset('storage/profiles/placeholder.webp');
                    @endphp

                    <div
                        class="reveal bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 shadow-md transition-all hover:-translate-y-2 hover:shadow-xl flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-4 gap-2">
                                <img src="{{ $photoUrl }}" alt="{{ $author }}"
                                    class="w-10 h-10 sm:w-12 sm:h-12 object-cover rounded-2xl shrink-0" />
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-xs sm:text-sm truncate">{{ $author }}</p>
                                    @if($position)
                                        <p class="text-[10px] sm:text-xs text-slate-400 font-bold truncate">{{ $position }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-0.5 sm:gap-1 shrink-0">
                                    <svg width="12" height="12" fill="#F97316" viewBox="0 0 24 24" class="sm:w-3.5 sm:h-3.5">
                                        <polygon
                                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                    </svg>
                                    <span class="text-xs sm:text-sm font-bold">{{ number_format($review->rating ?? 0, 1) }}</span>
                                </div>
                            </div>
                            <p class="italic font-medium text-slate-600 leading-relaxed mb-5 text-xs sm:text-sm">{{ $review->comment }}</p>
                        </div>
                        <div>
                            <span class="t-badge text-[10px] sm:text-xs">{{ $role }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-16 sm:py-20 bg-white" id="faq">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-10 sm:mb-12 reveal">
                <div
                    class="inline-block bg-orange-500/10 text-orange-500 px-4 py-2 rounded-full text-[10px] sm:text-xs font-extrabold uppercase tracking-widest mb-4">
                    FAQ
                </div>
                <h2 class="font-display text-[clamp(1.6rem,4vw,2.5rem)] font-extrabold">
                    Pertanyaan yang <span class="gradient-text">Sering Ditanyai</span>
                </h2>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-6 gap-y-4">
                <div class="flex flex-col gap-4">
                    <div
                        class="faq-item reveal bg-slate-100 border border-transparent rounded-2xl overflow-hidden cursor-pointer transition-all">
                        <div class="faq-question flex items-center justify-between p-4 sm:p-5 gap-3">
                            <h3 class="font-extrabold text-sm sm:text-[0.97rem] text-slate-900 pr-2">
                                Siapa yang bisa jadi freelancer di Digitalance?
                            </h3>
                            <svg class="faq-icon w-5 h-5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </div>
                        <div class="faq-answer px-4 sm:px-5 text-slate-500 font-medium leading-relaxed text-xs sm:text-sm">
                            Hanya siswa/i aktif SKOMDA yang terverifikasi.
                            Kamu perlu mengunggah ID siswa dan portofolio untuk
                            persetujuan.
                        </div>
                    </div>
                    
                    <div
                        class="faq-item reveal bg-slate-100 border border-transparent rounded-2xl overflow-hidden cursor-pointer transition-all">
                        <div class="faq-question flex items-center justify-between p-4 sm:p-5 gap-3">
                            <h3 class="font-extrabold text-sm sm:text-[0.97rem] text-slate-900 pr-2">
                                Bagaimana sistem pembayaran bekerja?
                            </h3>
                            <svg class="faq-icon w-5 h-5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </div>
                        <div class="faq-answer px-4 sm:px-5 text-slate-500 font-medium leading-relaxed text-xs sm:text-sm">
                            Kami menggunakan sistem escrow. Dana klien
                            disimpan aman dan dirilis ke freelancer setelah
                            proyek selesai dan disetujui.
                        </div>
                    </div>
                    
                    <div
                        class="faq-item reveal bg-slate-100 border border-transparent rounded-2xl overflow-hidden cursor-pointer transition-all">
                        <div class="faq-question flex items-center justify-between p-4 sm:p-5 gap-3">
                            <h3 class="font-extrabold text-sm sm:text-[0.97rem] text-slate-900 pr-2">
                                Berapa fee yang dikenakan platform?
                            </h3>
                            <svg class="faq-icon w-5 h-5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </div>
                        <div class="faq-answer px-4 sm:px-5 text-slate-500 font-medium leading-relaxed text-xs sm:text-sm">
                            Biaya platform adalah 10% dari total nilai proyek
                            untuk freelancer, dan gratis untuk klien.
                            Transparan, tanpa biaya tersembunyi.
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-4">
                    <div
                        class="faq-item reveal bg-slate-100 border border-transparent rounded-2xl overflow-hidden cursor-pointer transition-all">
                        <div class="faq-question flex items-center justify-between p-4 sm:p-5 gap-3">
                            <h3 class="font-extrabold text-sm sm:text-[0.97rem] text-slate-900 pr-2">
                                Apakah client harus dari Indonesia?
                            </h3>
                            <svg class="faq-icon w-5 h-5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </div>
                        <div class="faq-answer px-4 sm:px-5 text-slate-500 font-medium leading-relaxed text-xs sm:text-sm">
                            Tidak! Klien bisa dari mana saja, baik
                            perorangan maupun perusahaan. Kami terbuka untuk
                            klien internasional.
                        </div>
                    </div>
                    
                    <div
                        class="faq-item reveal bg-slate-100 border border-transparent rounded-2xl overflow-hidden cursor-pointer transition-all">
                        <div class="faq-question flex items-center justify-between p-4 sm:p-5 gap-3">
                            <h3 class="font-extrabold text-sm sm:text-[0.97rem] text-slate-900 pr-2">
                                Bagaimana jika terjadi sengketa?
                            </h3>
                            <svg class="faq-icon w-5 h-5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </div>
                        <div class="faq-answer px-4 sm:px-5 text-slate-500 font-medium leading-relaxed text-xs sm:text-sm">
                            Tim dukungan kami akan memediasi sengketa. Dengan
                            sistem escrow, dana tetap aman sampai masalah
                            selesai.
                        </div>
                    </div>
                    
                    <div
                        class="faq-item reveal bg-slate-100 border border-transparent rounded-2xl overflow-hidden cursor-pointer transition-all">
                        <div class="faq-question flex items-center justify-between p-4 sm:p-5 gap-3">
                            <h3 class="font-extrabold text-sm sm:text-[0.97rem] text-slate-900 pr-2">
                                Berapa lama proses penarikan dana?
                            </h3>
                            <svg class="faq-icon w-5 h-5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </div>
                        <div class="faq-answer px-4 sm:px-5 text-slate-500 font-medium leading-relaxed text-xs sm:text-sm">
                            Penarikan dana diproses maksimal 3 hari kerja ke
                            rekening bank kamu. Cepat, aman, dan andal.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-12 sm:py-16" id="join">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="reveal relative rounded-[1.5rem] sm:rounded-[2rem] px-6 py-12 sm:p-12 md:p-16 text-center overflow-hidden shadow-[0_20px_60px_rgba(15,118,110,.28)]"
                style="
                    background: linear-gradient(
                        135deg,
                        #0f766e 0%,
                        #10b981 100%
                    );
                ">
                <div class="absolute inset-0 opacity-20 pointer-events-none">
                    <div class="absolute w-60 h-60 sm:w-80 sm:h-80 rounded-full blur-3xl bg-white -top-20 -left-20"></div>
                    <div class="absolute w-72 h-72 sm:w-96 sm:h-96 rounded-full blur-3xl bg-teal-900 -bottom-20 -right-20"></div>
                </div>
                
                <div class="relative z-10">
                    <h2
                        class="font-display text-[clamp(1.6rem,4.5vw,3rem)] font-black leading-snug text-white max-w-3xl mx-auto mb-6">
                        Siap Memulai Perjalanan Freelance?<br /><span class="text-white/80">Gabung Sekarang!</span>
                    </h2>
                    <p class="text-sm sm:text-base lg:text-lg text-white/80 font-medium leading-relaxed max-w-2xl mx-auto mb-8 sm:mb-10">
                        Bergabunglah dengan ratusan siswa/i SKOMDA yang sudah sukses
                        mendapatkan proyek dan penghasilan dari
                        Digitalance.
                    </p>
                    
                    <div class="flex gap-4 justify-center flex-wrap w-full max-w-md mx-auto sm:max-w-none px-2">
                        <!-- Bergabung sebagai Freelancer -->
                        <a href="{{ route('login', ['mode' => 'register', 'role' => 'freelancer']) }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-white text-primary border-none px-6 sm:px-8 py-3.5 sm:py-4 rounded-2xl font-black text-xs sm:text-sm md:text-base cursor-pointer transition-all shadow-[0_8px_24px_rgba(0,0,0,.18)] hover:bg-slate-50 hover:-translate-y-0.5 no-underline">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" class="shrink-0">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                            </svg>
                            <span>Bergabung sebagai Freelancer</span>
                        </a>

                        <!-- Cari Talenta (Klien) -->
                        <a href="{{ route('login', ['mode' => 'register', 'role' => 'client']) }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-slate-900 text-white border-none px-6 sm:px-8 py-3.5 sm:py-4 rounded-2xl font-black text-xs sm:text-sm md:text-base cursor-pointer transition-all shadow-[0_8px_24px_rgba(0,0,0,.18)] hover:bg-black hover:-translate-y-0.5 no-underline">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" class="shrink-0">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            <span>Cari Talenta</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($showLogoutModal ?? false)
        <div id="logout-confirm-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-[24px] w-full max-w-md p-6 sm:p-8 shadow-2xl transform transition-all">
                <div class="text-center">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-5">
                        <i class="ri-logout-box-line text-2xl sm:text-3xl text-amber-500"></i>
                    </div>
                    <h3 class="font-display text-lg sm:text-xl font-extrabold text-slate-900 mb-2">Kembali ke halaman utama?</h3>
                    <p class="text-slate-500 text-xs sm:text-sm leading-relaxed mb-6">
                        Kamu saat ini sedang masuk sebagai
                        <span class="font-bold text-slate-700">
                            @if(($userRole ?? '') === 'admin') Administrator
                            @elseif(($userRole ?? '') === 'client') Klien
                            @elseif(($userRole ?? '') === 'freelancer') Freelancer
                            @else Pengguna
                            @endif
                        </span>.
                        <br>Kembali ke halaman utama akan membuat Anda keluar, lanjutkan atau tidak?
                    </p>
                    <div class="flex gap-3">
                        <button
                            onclick="window.location.href='@if(($userRole ?? '') === 'admin') {{ route('admin.dashboard') }} @elseif(($userRole ?? '') === 'client') {{ route('client.dashboard') }} @elseif(($userRole ?? '') === 'freelancer') {{ route('freelancer.dashboard') }} @else {{ route('home') }} @endif'"
                            class="flex-1 py-2.5 sm:py-3 rounded-[14px] bg-slate-100 text-slate-700 font-bold text-xs sm:text-sm hover:bg-slate-200 transition-all">
                            Batal & Dasbor
                        </button>
                        <form action="{{ route('logout') }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit"
                                class="w-full py-2.5 sm:py-3 rounded-[14px] bg-amber-500 text-white font-bold text-xs sm:text-sm hover:bg-amber-600 transition-all shadow-lg shadow-amber-200">
                                Ya, Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection