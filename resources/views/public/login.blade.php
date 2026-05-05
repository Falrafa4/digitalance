@extends('layouts.app')

@section('title', 'Login - Digitalance')

@section('styles')
    <style>
        @keyframes popIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .tag-item {
            animation: popIn 0.15s ease forwards;
        }

        .hero-img {
            transition: transform 1s ease, opacity 0.4s ease;
        }

        .hero-img.fade-out {
            opacity: 0;
        }

        .hero-wrap:hover .hero-img {
            transform: scale(1.05);
        }

        .panel {
            position: absolute;
            inset: 0;
            transition: opacity 0.45s cubic-bezier(0.4, 0, 0.2, 1),
                visibility 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .panel-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .panel-visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        #authContainer {
            position: relative;
            overflow: hidden;
        }

        /* Responsive Overlay */
        .auth-overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 10;
            transition: transform 0.65s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }

        @media (min-width: 768px) {
            .auth-overlay {
                width: 50%;
                transform: translateX(0%);
                display: block;
            }

            .auth-overlay.register-mode {
                transform: translateX(100%);
            }

            #loginPanel {
                width: 50%;
                left: 50%;
            }

            #registerPanel {
                width: 50%;
                left: 0;
            }
        }

        @media (max-width: 767px) {
            .auth-overlay {
                display: none;
            }

            #loginPanel,
            #registerPanel {
                width: 100%;
                left: 0;
            }
        }

        #overlayToggle {
            position: relative;
            z-index: 100 !important;
            pointer-events: auto !important;
        }

        #roleSlider {
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .inp:focus {
            outline: none;
            border-color: #0F766E;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
        }

        .grain {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 999;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        .panel::-webkit-scrollbar {
            width: 3px;
        }

        .panel::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        #skillSuggestions::-webkit-scrollbar {
            width: 3px;
        }

        #skillSuggestions::-webkit-scrollbar-thumb {
            background: #0F766E;
            border-radius: 10px;
        }
    </style>
@endsection

@section('additional-header')
    <div class="grain"></div>
    <div class="fixed inset-0 -z-10"
        style="background:radial-gradient(at 0% 0%,rgba(16,185,129,.06) 0,transparent 50%),radial-gradient(at 100% 100%,rgba(249,115,22,.05) 0,transparent 50%);">
    </div>
@endsection

@section('content')
    <main class="flex-1 flex items-center justify-center px-4 py-12 overflow-hidden min-h-[calc(100vh-100px)]">

        <div id="authContainer" class="w-full relative overflow-hidden flex rounded-[28px] border border-slate-100 bg-white"
            style="max-width:960px; height:min(75vh,660px); min-height:500px; box-shadow:0 24px 48px -10px rgba(15,23,42,.12);">

            {{-- 1. OVERLAY AREA --}}
            <div class="auth-overlay" id="authOverlay">
                <div class="relative h-full w-full">
                    <div class="hero-wrap absolute inset-0 overflow-hidden">
                        <img id="heroImage"
                            src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1000"
                            alt="Workspace" class="hero-img w-full h-full object-cover" />
                        <div class="absolute inset-0 z-10"
                            style="background:linear-gradient(to top,rgba(0,0,0,.88) 0%,rgba(0,0,0,.4) 50%,rgba(0,0,0,.04) 100%);">
                        </div>
                    </div>

                    <div class="absolute inset-0 flex flex-col justify-end p-8 z-20">
                        <div
                            class="inline-flex items-center gap-2 mb-4 w-fit text-emerald-400 font-extrabold text-[0.65rem] uppercase tracking-[0.15em] bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-full border border-white/20">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                            <span>Standar Elite</span>
                        </div>
                        <h2 id="overlayTitle"
                            class="text-white font-extrabold leading-[1.1] mb-3 transition-opacity duration-300"
                            style="font-size:1.7rem;">
                            Jaringan Presisi untuk Solusi Expert
                        </h2>
                        <p id="overlayDesc"
                            class="text-white/80 text-[0.85rem] leading-relaxed mb-6 max-w-sm transition-opacity duration-300">
                            Rasakan koneksi tanpa hambatan antara permintaan industri premium dan output kreatif elite.
                        </p>
                        <button id="overlayToggle" type="button"
                            class="inline-flex items-center gap-2.5 text-white font-bold text-[0.82rem] bg-white/15 border border-white/25 backdrop-blur-sm px-6 py-3 rounded-xl uppercase tracking-wide w-fit cursor-pointer hover:bg-white/25 transition-all duration-300">
                            <span id="toggleText">Daftarkan Akun</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 2. FORM AREA --}}
            <div class="relative w-full h-full" style="pointer-events:none;">

                {{-- LOGIN PANEL --}}
                <div id="loginPanel" class="panel panel-visible overflow-y-auto flex items-center justify-center">
                    <div class="w-full px-8 sm:px-12 py-6" style="pointer-events:auto;">
                        <div class="mb-5">
                            <div
                                class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-teal-700 mb-3 border border-slate-200">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="2" y1="12" x2="22" y2="12" />
                                    <path
                                        d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                </svg>
                            </div>
                            <h2 class="text-[1.7rem] font-extrabold text-slate-900 mb-1">Masuk ke Akun Anda</h2>
                            <p class="text-slate-500 text-[0.82rem]">Silakan masukkan email dan password Anda</p>
                        </div>

                        {{-- Error login --}}
                        @if ($errors->any())
                            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200">
                                @foreach ($errors->all() as $error)
                                    <p class="text-[0.75rem] text-red-600 font-bold flex items-center gap-2">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <circle cx="12" cy="12" r="10" />
                                            <line x1="12" y1="8" x2="12" y2="12" />
                                            <line x1="12" y1="16" x2="12.01" y2="16" />
                                        </svg>
                                        {{ $error }}
                                    </p>
                                @endforeach
                            </div>
                        @endif

                        <form id="loginForm" method="POST" action="{{ route('login-process') }}"
                            class="flex flex-col gap-3">
                            @csrf
                            <div>
                                <label
                                    class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Alamat
                                    Email</label>
                                <div class="relative">
                                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                        width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path
                                            d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <polyline points="22,6 12,13 2,6" />
                                    </svg>
                                    <input type="email" name="email" placeholder="nama@email.com"
                                        value="{{ old('email') }}" required
                                        class="inp w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[0.88rem] text-slate-900 transition-all" />
                                </div>
                            </div>

                            <div id="registerPasswordWrapper">
                                <label
                                    class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Password</label>
                                <div class="relative">
                                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                        width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    <input id="registerPasswordInput" type="password" name="password" placeholder="••••••••"
                                        class="inp w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[0.86rem] transition-all" />
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full py-3 bg-slate-900 text-white font-bold rounded-xl text-[0.88rem] mt-2 flex items-center justify-center gap-2 hover:bg-black hover:-translate-y-0.5 transition-all shadow-md cursor-pointer">
                                Masuk
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <polyline points="9 18 15 12 9 6" />
                                </svg>
                            </button>

                            <div class="mt-5 text-center md:hidden">
                                <a href="#"
                                    class="mobile-toggle text-[0.8rem] font-bold text-teal-600 hover:text-teal-700 underline">Belum
                                    punya akun? Daftar</a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- REGISTER PANEL --}}
                <div id="registerPanel" class="panel panel-hidden overflow-y-auto flex items-center justify-center">
                    <div class="w-full px-8 sm:px-12 py-6" style="pointer-events:auto;">
                        <div class="mb-4">
                            <div
                                class="inline-flex items-center gap-1.5 mb-1.5 text-teal-700 text-[0.6rem] font-extrabold uppercase tracking-widest">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    <path d="m9 12 2 2 4-4" />
                                </svg>
                                <span>Protokol Aman</span>
                            </div>
                            <h2 class="text-[1.7rem] font-extrabold text-slate-900 mb-0.5">Daftar ke Digitalance</h2>
                            <p class="text-slate-500 text-[0.82rem]">Bergabunglah dengan jaringan elite digital.</p>
                        </div>

                        {{-- ROLE TOGGLE --}}
                        <div class="relative inline-flex p-1 bg-slate-100 rounded-full border border-slate-200 mb-4"
                            id="roleToggleContainer">
                            <div id="roleSlider"
                                class="absolute top-1 left-1 bg-white rounded-full shadow-sm transition-transform duration-300"
                                style="height:calc(100% - 8px); width:calc(50% - 4px);"></div>
                            <button type="button" id="btnClient"
                                class="relative z-10 px-5 py-1.5 min-w-[90px] text-[0.78rem] font-extrabold text-teal-700 cursor-pointer rounded-full transition-colors duration-300">Klien</button>
                            <button type="button" id="btnFreelancer"
                                class="relative z-10 px-5 py-1.5 min-w-[90px] text-[0.78rem] font-semibold text-slate-400 cursor-pointer rounded-full transition-colors duration-300">Freelancer</button>
                        </div>

                        <form id="registerForm" method="POST" action="{{ route('register-process') }}"
                            data-action-client="{{ route('register-process') }}"
                            data-action-freelancer="{{ route('register-freelancer-process') }}"
                            class="flex flex-col gap-2.5">
                            @csrf

                            {{-- CLIENT FIELDS --}}
                            <div id="clientFields">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <div>
                                        <label
                                            class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Nama
                                            Depan</label>
                                        <div class="relative">
                                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                                width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                                <circle cx="12" cy="7" r="4" />
                                            </svg>
                                            <input type="text" name="name" placeholder="John" value="{{ old('name') }}"
                                                class="inp w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[0.86rem] transition-all" />
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Telepon
                                            (WA)</label>
                                        <div class="relative">
                                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                                width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path
                                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                            </svg>
                                            <input type="text" name="phone" placeholder="0812..." value="{{ old('phone') }}"
                                                class="inp w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[0.86rem] transition-all" />
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2.5">
                                    <label
                                        class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Email</label>
                                    <div class="relative">
                                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                            width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path
                                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                            <polyline points="22,6 12,13 2,6" />
                                        </svg>
                                        <input type="email" name="email" placeholder="john@digitalance.io"
                                            value="{{ old('email') }}"
                                            class="inp w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[0.86rem] transition-all" />
                                    </div>
                                </div>
                            </div>

                            {{-- FREELANCER FIELDS --}}
                            <div id="freelancerFields" class="hidden">
                                <div>
                                    <label
                                        class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Identitas
                                        Siswa (NIS)</label>
                                    <div class="relative">
                                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                            width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M4 4h16v16H4z" />
                                            <path d="M8 8h8" />
                                            <path d="M8 12h8" />
                                            <path d="M8 16h6" />
                                        </svg>
                                        <input id="studentSelect" name="student_display" list="studentList"
                                            class="inp w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[0.86rem] transition-all"
                                            placeholder="Ketik nama / NIS..." autocomplete="off" />

                                        <datalist id="studentList">
                                            @foreach(($students ?? []) as $s)
                                                <option value="{{ $s->name }} ({{ $s->nis }})" data-id="{{ $s->id }}"
                                                    data-nis="{{ $s->nis }}" data-email="{{ $s->email }}"></option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <input type="hidden" id="studentIdInput" name="student_id"
                                        value="{{ old('student_id') }}" />
                                    <input type="hidden" id="nisInput" name="nis" value="{{ old('nis') }}" />
                                    @error('student_id')
                                        <p class="text-[0.72rem] text-red-600 font-bold mt-1 flex items-center gap-2">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <circle cx="12" cy="12" r="10" />
                                                <line x1="12" y1="8" x2="12" y2="12" />
                                                <line x1="12" y1="16" x2="12.01" y2="16" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="mt-2.5">
                                    <label
                                        class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">
                                        Email Siswa
                                    </label>
                                    <div class="relative">
                                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                            width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path
                                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                            <polyline points="22,6 12,13 2,6" />
                                        </svg>
                                        <input type="text" id="studentEmail" value="" placeholder="Email siswa" readonly tabindex="-1"
                                            class="inp w-full pl-10 pr-3 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-[0.86rem] transition-all text-slate-600" />
                                    </div>
                                    <p class="text-[0.62rem] text-slate-400 mt-1 ml-1">
                                        Gunakan email ini untuk login setelah registrasi.
                                    </p>
                                </div>

                                <div class="mt-2.5">
                                    <label
                                        class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Keahlian
                                        Utama</label>
                                    <div class="relative w-full">
                                        <div id="tagsContainer"
                                            class="bg-slate-50 border border-slate-200 rounded-xl px-2 py-1 flex flex-wrap gap-1 min-h-[40px] cursor-text transition-all duration-200 focus-within:border-teal-600 relative">
                                            <input id="skillInput" type="text" autocomplete="off"
                                                class="border-none bg-transparent outline-none flex-1 min-w-[80px] text-[0.86rem] py-0.5"
                                                placeholder="Ketik lalu Enter..." />
                                        </div>
                                        <ul id="skillSuggestions"
                                            class="absolute top-full left-0 right-0 z-50 bg-white border border-slate-200 rounded-xl mt-1 shadow-xl hidden max-h-40 overflow-y-auto text-sm font-medium text-slate-700">
                                        </ul>
                                    </div>
                                    <input type="hidden" id="hiddenSkillsInput" name="skills" value="[]" />
                                    <p id="tagLimitMsg" class="text-[0.6rem] text-slate-400 text-right mt-1">0/5 Keahlian
                                    </p>
                                </div>
                            </div>

                            {{-- SHARED PASSWORD --}}
                            <div>
                                <label
                                    class="block text-[0.6rem] font-extrabold text-slate-400 uppercase tracking-wide mb-1.5 ml-1">Kode
                                    Sandi</label>
                                <div class="relative">
                                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"
                                        width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                    <input type="password" name="password" placeholder="Isi dengan password kuat" required
                                        class="inp w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[0.86rem] transition-all" />
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full py-3 bg-slate-900 text-white font-bold rounded-xl text-[0.88rem] mt-1 flex items-center justify-center gap-2 hover:bg-black hover:-translate-y-0.5 transition-all shadow-md cursor-pointer">
                                Daftar
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <polyline points="9 18 15 12 9 6" />
                                </svg>
                            </button>

                            <div class="mt-4 text-center md:hidden">
                                <a href="#"
                                    class="mobile-toggle text-[0.8rem] font-bold text-teal-600 hover:text-teal-700 underline">Sudah
                                    punya akun? Masuk</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        window.serviceCategories = @js($categories ?? ['Web Design']);
        window.skomdaStudents = @js($students ?? []);
    </script>
    <script src="{{ asset('js/sign-in.js') }}"></script>
@endsection