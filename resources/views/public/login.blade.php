@extends('layouts.app')

@section('title', 'Masuk - Digitalance')

@section('styles')
    <!-- Menghubungkan secara rapi ke file CSS eksternal -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
    <!-- Grain overlay -->
    <div class="grain-overlay"></div>
    
    <!-- Flash messages -->
    <x-flash />

    <main class="min-h-screen bg-[#f8fafc] flex items-start justify-center p-0 md:p-6 lg:p-8">
        <div class="w-full max-w-[840px] md:h-[590px] bg-white md:rounded-[32px] shadow-[0_24px_70px_rgba(15,118,110,0.07)] border border-slate-100 flex flex-col md:flex-row relative" id="authContainer">
            
            <!-- 1. PANEL OVERLAY (PREMIUM DARK GRADIENT - TANPA LOGO & ICON PETIR) -->
            <div class="auth-overlay bg-slate-950 text-white" id="authOverlay">
                <div class="absolute inset-0 z-0 bg-gradient-to-br from-slate-950/60 via-slate-900/50 to-slate-950/60"></div>
                
                <!-- Pembungkus Gambar Slideshow -->
                <div class="hero-wrap absolute inset-0 z-0 opacity-35">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1471&auto=format&fit=crop" 
                         class="hero-img active" id="heroImg1" alt="Digitalance Workspace">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1470&auto=format&fit=crop" 
                         class="hero-img absolute inset-0 opacity-0" id="heroImg2" alt="Digitalance Network">
                </div>

                <!-- Konten Teks dan Navigasi Dinamis (Dibersihkan dari Icon Petir & Logo Atas) -->
                <div class="absolute inset-0 z-10 flex flex-col justify-between p-8 md:p-10 pointer-events-auto">
                    <!-- Spacing Spacer pengganti logo atas agar konten teks tetap berada di tengah secara proporsional -->
                    <div class="hidden md:block h-6"></div>

                    <!-- Caption dinamis berganti otomatis via JS -->
                    <div class="max-w-xs my-auto transform transition-all duration-500" id="overlayTextContent">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-emerald-300 text-[10px] font-semibold tracking-wide uppercase mb-4 backdrop-blur-md">
                            Selamat Datang di Digitalance!
                        </span>
                        <h2 class="font-display text-xl md:text-2xl font-extrabold tracking-tight mb-3 leading-snug" id="overlayTitle">
                            Eksplorasi Talent Terbaik Skomda di Sini.
                        </h2>
                        <p class="text-white/70 text-xs leading-relaxed" id="overlayDesc">
                            Temukan freelancer siswa berkompeten untuk menyelesaikan projek digital Anda dengan kualitas profesional.
                        </p>
                    </div>

                    <!-- Hak Cipta (Hanya tampil di Desktop) -->
                    <div class="text-[10px] text-white/30 hidden md:block">
                        &copy; {{ date('Y') }} Digitalance. All rights reserved.
                    </div>
                </div>
            </div>

            <!-- 2. PANEL FORM MASUK (LOGIN) -->
            <div class="form-panel login-panel active flex flex-col justify-center items-center px-6 md:px-10 lg:px-12" id="loginPanel">
                <div class="w-full max-w-sm space-y-5">
                    <!-- TOGGLE ATAS UNTUK MINIMIZE SCROLLING -->
                    <div class="flex justify-center md:justify-start mb-2">
                        <div class="inline-flex p-1 bg-slate-100 rounded-xl w-full max-w-[200px]">
                            <button type="button" class="switch-to-login-btn flex-1 py-1.5 text-xs font-bold rounded-lg transition-all text-center bg-white text-slate-900 shadow-sm">Masuk</button>
                            <button type="button" class="switch-to-register-btn flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all text-center text-slate-500 hover:text-slate-800">Daftar</button>
                        </div>
                    </div>

                    <div class="text-center md:text-left">
                        <h1 class="font-display text-xl font-extrabold text-slate-900 tracking-tight">Selamat Datang</h1>
                        <p class="mt-1 text-xs text-slate-500">Masuk ke dashboard Digitalance Anda</p>
                    </div>

                    <!-- Alert khusus Validasi error Login Laravel -->
                    @if ($errors->has('email') && session('login_error'))
                        <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-xl">
                            <div class="flex items-start">
                                <!-- Warning SVG Icon -->
                                <svg width="16" height="16" class="w-4 h-4 text-red-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-xs text-red-700 font-semibold">{{ $errors->first('email') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('login-process') }}" method="POST" class="space-y-4" id="loginForm">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-950 focus:border-transparent text-xs transition-all placeholder:text-slate-400"
                                placeholder="nama@email.com">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="block text-xs font-semibold text-slate-700 m-0">Kata Sandi</label>
                                <button type="button" id="forgotPasswordBtn" class="text-xs font-bold text-teal-700 hover:underline bg-transparent border-none p-0 focus:outline-none">Lupa sandi?</button>
                            </div>
                            <div class="relative">
                                <input type="password" name="password" id="loginPassword" required
                                    class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-950 focus:border-transparent text-xs transition-all placeholder:text-slate-400"
                                    placeholder="••••••••">
                                <button type="button" id="toggleLoginPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none flex items-center justify-center">
                                    <!-- Default: Mata Terbuka -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi Kata Sandi</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="loginPasswordConfirmation" required
                                    class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-950 focus:border-transparent text-xs transition-all placeholder:text-slate-400"
                                    placeholder="Ulangi kata sandi">
                                <button type="button" id="toggleLoginPasswordConfirmation" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none flex items-center justify-center">
                                    <!-- Default: Mata Terbuka -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-3 px-5 rounded-xl text-white font-bold bg-gradient-to-r from-slate-900 to-zinc-800 hover:shadow-lg hover:shadow-slate-900/10 active:scale-[0.98] transition-all text-xs">
                            Masuk Sekarang
                        </button>
                    </form>
                </div>
            </div>

            <!-- 3. PANEL FORM DAFTAR (REGISTER) -->
            <div class="form-panel register-panel inactive flex flex-col justify-center items-center px-6 md:px-10 lg:px-12" id="registerPanel">
                <div class="w-full max-w-sm space-y-4">
                    <!-- TOGGLE ATAS UNTUK MINIMIZE SCROLLING -->
                    <div class="flex justify-center md:justify-start mb-2">
                        <div class="inline-flex p-1 bg-slate-100 rounded-xl w-full max-w-[200px]">
                            <button type="button" class="switch-to-login-btn flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all text-center text-slate-500 hover:text-slate-800">Masuk</button>
                            <button type="button" class="switch-to-register-btn flex-1 py-1.5 text-xs font-bold rounded-lg transition-all text-center bg-white text-slate-900 shadow-sm">Daftar</button>
                        </div>
                    </div>

                    <div class="text-center md:text-left">
                        <h1 class="font-display text-xl font-extrabold text-slate-900 tracking-tight">Buat Akun</h1>
                        <p class="mt-1 text-xs text-slate-500">Pilih jenis akun Digitalance Anda</p>
                    </div>

                    <!-- Alert khusus Validasi error Registrasi Laravel -->
                    @if ($errors->any() && !session('login_error'))
                        <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-xl">
                            <div class="flex items-start">
                                <svg width="16" height="16" class="w-4 h-4 text-red-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <p class="text-xs text-red-700 font-bold mb-1">Pendaftaran gagal:</p>
                                    <ul class="list-disc list-inside text-[11px] text-red-600 space-y-0.5 p-0 m-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tab Pilihan Peran (Client / Freelancer) -->
                    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-2xl" id="roleSelector">
                        <button type="button" data-role="client" class="role-tab py-2 text-xs font-bold rounded-xl transition-all bg-white text-slate-900 shadow-sm flex items-center justify-center gap-1.5">
                            <svg width="14" height="14" class="w-3.5 h-3.5 text-slate-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Client
                        </button>
                        <button type="button" data-role="freelancer" class="role-tab py-2 text-xs font-bold rounded-xl transition-all text-slate-500 hover:text-slate-800 flex items-center justify-center gap-1.5">
                            <svg width="14" height="14" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Freelancer
                        </button>
                    </div>

                    <!-- Scroll Form Container untuk Mencegah Overflow pada Ketinggian 620px -->
                    <div class="overflow-y-auto max-h-[340px] pr-1 -mr-2">
                        <form action="{{ route('register-client') }}" method="POST" class="space-y-3" id="registerForm">
                            @csrf
                            <input type="hidden" name="role" id="roleInput" value="client">

                            <!-- INPUT BIDANG KHUSUS UNTUK CLIENT -->
                            <div id="clientFields" class="space-y-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Lengkap / Perusahaan</label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-950 text-xs transition-all"
                                        placeholder="John Doe">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}"
                                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-950 text-xs transition-all"
                                        placeholder="08123456789">
                                </div>
                            </div>

                            <!-- INPUT BIDANG KHUSUS UNTUK FREELANCER (SISWA SKOMDA) -->
                            <div id="freelancerFields" class="space-y-3 hidden">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Data Siswa Skomda</label>
                                    <div class="relative">
                                        <button type="button" id="studentSelectBtn" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-left text-xs text-slate-400 flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-slate-950">
                                            <span id="selectedStudentLabel">-- Cari nama atau NISN Anda --</span>
                                            <!-- Chevron Icon -->
                                            <svg width="16" height="16" class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <input type="hidden" name="student_id" id="studentIdInput" value="{{ old('student_id') }}">
                                        
                                        <!-- Dropdown Pencarian Siswa -->
                                        <div id="studentDropdown" class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-100 rounded-2xl shadow-xl p-3 space-y-2 hidden">
                                            <input type="text" id="studentSearch" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-slate-950" placeholder="Ketik nama untuk mencari...">
                                            <div id="studentList" class="max-h-36 overflow-y-auto space-y-1 text-xs">
                                                <!-- List siswa di-inject otomatis lewat JS -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Seleksi Skill/Keahlian -->
                                <div id="skillsWrapper" class="space-y-2 hidden">
                                    <div class="flex justify-between items-center">
                                        <label class="block text-[11px] font-semibold text-slate-700">Keahlian Anda (Maks 5)</label>
                                        <span class="text-[9px] text-slate-400 font-medium" id="skillsCountText">0/5 Terpilih</span>
                                    </div>

                                    <div id="skillsContainer" class="flex flex-wrap gap-1 p-2 bg-slate-50 border border-slate-200 rounded-xl min-h-[40px]">
                                        <!-- Skill tag aktif akan disisipkan di sini -->
                                    </div>
                                    
                                    <!-- Input Keahlian Kustom Manual -->
                                    <div class="flex gap-1">
                                        <input type="text" id="customSkillInput" class="flex-1 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-slate-900 placeholder:text-slate-400" placeholder="Ketik keahlian kustom...">
                                        <button type="button" id="addCustomSkillBtn" class="px-3 py-1.5 bg-slate-900 border border-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-all flex items-center gap-1">
                                            Tambah
                                        </button>
                                    </div>

                                    <div id="availableSkills" class="flex flex-wrap gap-1 mt-1 max-h-16 overflow-y-auto p-1 border-t border-slate-100 pt-1.5">
                                        <!-- Opsi kategori keahlian -->
                                    </div>
                                </div>
                            </div>

                            <!-- EMAIL DAN PASSWORD (DI-SHARE ANTARA CLIENT & FREELANCER) -->
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Alamat Email</label>
                                <div class="relative">
                                    <input type="email" name="email" id="registerEmail" value="{{ old('email') }}" required
                                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-950 text-xs transition-all placeholder:text-slate-400"
                                        placeholder="ex@email.com">
                                    <!-- Tombol Salin Email -->
                                    <button type="button" id="copyEmailBtn" class="absolute right-3 top-1/2 -translate-y-1/2 text-teal-700 hover:text-teal-900 text-[10px] font-bold hidden focus:outline-none">
                                        Salin
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kata Sandi</label>
                                <div class="relative">
                                    <input type="password" name="password" id="registerPassword" required
                                        class="w-full pl-4 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-950 text-xs transition-all"
                                        placeholder="Minimal 8 karakter">
                                    <button type="button" id="toggleRegisterPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none flex items-center justify-center">
                                        <!-- Default: Mata Terbuka -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </div>
                                
                                <!-- PASSWORD STRENGTH WITH CRITERIA CHECKLIST (REVISI DETAIL VERIFIKASI) -->
                                <div id="passwordStrengthWrapper" class="mt-2 space-y-2 hidden bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <div class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden flex gap-1">
                                        <div id="strengthBar1" class="h-full w-1/3 bg-slate-300 transition-all rounded-full"></div>
                                        <div id="strengthBar2" class="h-full w-1/3 bg-slate-300 transition-all rounded-full"></div>
                                        <div id="strengthBar3" class="h-full w-1/3 bg-slate-300 transition-all rounded-full"></div>
                                    </div>
                                    <p id="strengthText" class="text-[10px] font-bold text-slate-500">Kekuatan Sandi: Terlalu Pendek</p>
                                    
                                    <!-- Detil Parameter yang Diperlukan (Tanpa Emoji) -->
                                    <div class="grid grid-cols-2 gap-x-2 gap-y-1 pt-1.5 border-t border-slate-200/50 text-[9px]">
                                        <div id="req-length" class="flex items-center gap-1 text-slate-400 font-medium transition-colors">
                                            <span class="icon inline-block text-[8px]">●</span> Min. 8 karakter
                                        </div>
                                        <div id="req-case" class="flex items-center gap-1 text-slate-400 font-medium transition-colors">
                                            <span class="icon inline-block text-[8px]">●</span> Huruf besar & kecil
                                        </div>
                                        <div id="req-number" class="flex items-center gap-1 text-slate-400 font-medium transition-colors">
                                            <span class="icon inline-block text-[8px]">●</span> Angka (0-9)
                                        </div>
                                        <div id="req-special" class="flex items-center gap-1 text-slate-400 font-medium transition-colors">
                                            <span class="icon inline-block text-[8px]">●</span> Simbol khusus (!@#)
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="registerPasswordConfirmation" required
                                        class="w-full pl-4 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-950 text-xs transition-all"
                                        placeholder="Ulangi kata sandi">
                                    <button type="button" id="toggleRegisterPasswordConfirmation" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none flex items-center justify-center">
                                        <!-- Default: Mata Terbuka -->
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full py-3 px-5 rounded-xl text-white font-bold bg-gradient-to-r from-slate-900 to-zinc-800 hover:shadow-lg transition-all text-xs mt-2">
                                Daftar Akun
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="forgotPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm hidden">
        <div class="relative w-full max-w-sm bg-white rounded-2xl p-6 shadow-2xl border border-slate-100 transform transition-all scale-100 duration-200">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-1.5">
                    <!-- Lock Icon SVG -->
                    <svg width="20" height="20" class="w-5 h-5 text-slate-800 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Lupa Kata Sandi?
                </h3>
                <button type="button" id="closeForgotPasswordBtn" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                    <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <p class="text-xs text-slate-600 leading-relaxed">
                    Untuk alasan keamanan data siswa Skomda, pemulihan akun serta perubahan kata sandi wajib diproses secara langsung oleh administrator sistem.
                </p>
                <!-- KOTAK INFORMASI DENGAN IKON PROPORSIONAL (REVISI DESIGN OVERFLOW) -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <!-- Info Icon dengan dimensi terproteksi tinggi/lebar agar tidak meledak di CSS -->
                        <svg width="20" height="20" class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-[11px] text-slate-600 leading-relaxed">
                        <strong class="text-slate-900 block mb-1">Langkah Pemulihan:</strong>
                        Hubungi Unit IT Skomda atau Admin Digitalance melalui tautan WhatsApp untuk verifikasi NISN atau data identitas Anda.
                    </div>
                </div>
            </div>
            
            <div class="mt-5 flex gap-2.5">
                <button type="button" id="cancelForgotPasswordBtn" class="flex-1 py-2 px-3 rounded-xl border border-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-all">
                    Tutup
                </button>
                <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Digitalance,%20saya%20lupa%20kata%20sandi%20akun%20saya" target="_blank" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white text-center font-semibold text-xs hover:bg-slate-800 transition-all shadow-md shadow-slate-950/10 no-underline flex justify-center items-center gap-1.5">
                    <!-- WhatsApp Icon SVG -->
                    <svg width="14" height="14" class="w-3.5 h-3.5 fill-current flex-shrink-0" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.262 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.665.989 3.3 1.503 4.94 1.505 5.548 0 10.064-4.512 10.068-10.066.002-2.69-1.043-5.216-2.943-7.114C16.756 1.58 14.236.536 11.99.536c-5.556 0-10.072 4.514-10.076 10.067-.001 1.887.49 3.723 1.422 5.344L1.758 22.21l6.33-1.657c-1.12.63-1.4 1.05-.14-1.4z"/>
                    </svg>
                    Hubungi Admin
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@php
    $oldRole = old('student_id') ? 'freelancer' : (old('name') ? 'client' : '');
    $panelShowMode = session('login_error') ? 'login' : ((session('register_error') || $errors->any()) ? 'register' : '');
    $loginPageData = [
        'serviceCategories' => $categories ?? [],
        'skomdaStudents' => $students ?? [],
        'hasRegistrationErrors' => $errors->any(),
        'registrationErrors' => $errors->getMessages(),
        'oldRole' => $oldRole,
        'panelShowMode' => $panelShowMode,
    ];
@endphp
<script id="loginPageData" type="application/json">
    @json($loginPageData)
</script>
<script src="{{ asset('js/sign-in.js') }}"></script>

<!-- Script JavaScript Khusus untuk fungsionalitas Show/Hide Password -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const eyeOpenSvg = `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        `;
        
        const eyeCloseSvg = `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
        `;

        function setupPasswordToggle(buttonId, inputId) {
            const btn = document.getElementById(buttonId);
            const input = document.getElementById(inputId);
            if (btn && input) {
                btn.addEventListener('click', function() {
                    if (input.type === 'password') {
                        input.type = 'text';
                        btn.innerHTML = eyeCloseSvg;
                    } else {
                        input.type = 'password';
                        btn.innerHTML = eyeOpenSvg;
                    }
                });
            }
        }

        // Jalankan toggle untuk form login dan register
        setupPasswordToggle('toggleLoginPassword', 'loginPassword');
        setupPasswordToggle('toggleLoginPasswordConfirmation', 'loginPasswordConfirmation');
        setupPasswordToggle('toggleRegisterPassword', 'registerPassword');
        setupPasswordToggle('toggleRegisterPasswordConfirmation', 'registerPasswordConfirmation');
    });
</script>
@endsection
