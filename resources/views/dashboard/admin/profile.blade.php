@extends('layouts.dashboard')
@section('title', 'Profil Admin | Digitalance')

@section('content')
    <div class="content-scroll flex-1 px-4 sm:px-8 py-7 overflow-y-auto">

        {{-- Page Header --}}
        <div class="mb-8 animate-fadeUp">
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Profil Admin</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Kelola informasi dasar dan keamanan akun administrator kamu.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Sidebar Profile --}}
            <div class="lg:col-span-1">
                <div
                    class="bg-white rounded-3xl border border-slate-200 p-6 flex flex-col items-center text-center animate-fadeUp">
                    <div class="relative mb-4">
                        <img id="avatar-preview"
                            src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0f766e&color=fff&size=128"
                            class="w-24 h-24 rounded-3xl object-cover border-4 border-white shadow-teal-md" />
                    </div>
                    <h3 class="font-display font-extrabold text-[1.15rem] text-slate-900">{{ Auth::user()->name }}</h3>
                    <p class="text-[13px] text-slate-400 mt-0.5">{{ Auth::user()->email }}</p>
                    <span
                        class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 bg-[#f0fdf9] text-[#0f766e] text-[11px] font-bold rounded-full">
                        <i class="ri-shield-user-line"></i> Administrator
                    </span>
                </div>
            </div>

            {{-- Main Form --}}
            <div class="lg:col-span-2 flex flex-col gap-5">

                {{-- Informasi Umum --}}
                <div class="bg-white rounded-3xl border border-slate-200 animate-fadeUp-1">
                    <div class="px-7 py-5 border-b border-slate-100">
                        <h2 class="font-display font-bold text-[1.05rem] text-slate-900">Informasi Umum</h2>
                        <p class="text-[12px] text-slate-400 mt-0.5">Perbarui data diri dasar administrator.</p>
                    </div>
                    <div class="px-7 py-6">
                        @if(session('success'))
                            <div
                                class="flex items-center gap-3 mb-5 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-[13px] font-semibold">
                                <i class="ri-check-double-line text-emerald-500 text-[17px]"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.profile.update') }}" method="POST" id="profile-form">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Nama
                                        Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                                        class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)] @error('name') border-red-400 @enderror" />
                                    @error('name')<p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>@enderror
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label
                                        class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Email</label>
                                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                        required
                                        class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)] @error('email') border-red-400 @enderror" />
                                    @error('email')<p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-[22px] py-[11px] bg-[#0f766e] text-white font-display font-bold text-[13px] rounded-[12px] shadow-teal-md hover:bg-[#0a5e58] hover:shadow-teal-lg transition-all duration-200 hover:-translate-y-0.5 cursor-pointer border-none">
                                    <i class="ri-save-line"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Ubah Password --}}
                <div class="bg-white rounded-3xl border border-slate-200 animate-fadeUp-2">
                    <div class="px-7 py-5 border-b border-slate-100">
                        <h2 class="font-display font-bold text-[1.05rem] text-slate-900">Ubah Password</h2>
                        <p class="text-[12px] text-slate-400 mt-0.5">Pastikan akun kamu menggunakan password yang kuat.</p>
                    </div>
                    <div class="px-7 py-6">
                        @if(session('password_success'))
                            <div
                                class="flex items-center gap-3 mb-5 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-[13px] font-semibold">
                                <i class="ri-check-double-line text-emerald-500 text-[17px]"></i>
                                {{ session('password_success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="flex flex-col gap-1.5 mb-4">
                                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Password Saat
                                    Ini</label>
                                <div class="relative">
                                    <input type="password" name="current_password" id="cur-pass" required
                                        class="w-full py-[10px] px-[13px] pr-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)] @error('current_password') border-red-400 @enderror" />
                                    <button type="button" onclick="togglePass('cur-pass', this)"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 bg-transparent border-none cursor-pointer hover:text-slate-600 transition-all duration-150">
                                        <i class="ri-eye-line text-[16px]"></i>
                                    </button>
                                </div>
                                @error('current_password')<p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Password
                                        Baru</label>
                                    <div class="relative">
                                        <input type="password" name="password" id="new-pass" required
                                            class="w-full py-[10px] px-[13px] pr-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)] @error('password') border-red-400 @enderror" />
                                        <button type="button" onclick="togglePass('new-pass', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 bg-transparent border-none cursor-pointer hover:text-slate-600 transition-all duration-150">
                                            <i class="ri-eye-line text-[16px]"></i>
                                        </button>
                                    </div>
                                    @error('password')<p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>@enderror
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Konfirmasi
                                        Password</label>
                                    <div class="relative">
                                        <input type="password" name="password_confirmation" id="conf-pass" required
                                            class="w-full py-[10px] px-[13px] pr-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                                        <button type="button" onclick="togglePass('conf-pass', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 bg-transparent border-none cursor-pointer hover:text-slate-600 transition-all duration-150">
                                            <i class="ri-eye-line text-[16px]"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-[22px] py-[11px] bg-[#0f766e] text-white font-display font-bold text-[13px] rounded-[12px] shadow-teal-md hover:bg-[#0a5e58] hover:shadow-teal-lg transition-all duration-200 hover:-translate-y-0.5 cursor-pointer border-none">
                                    <i class="ri-lock-password-line"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Danger Zone --}}
                <div class="bg-white rounded-3xl border border-red-100 animate-fadeUp-3">
                    <div class="px-7 py-5 border-b border-red-100">
                        <h2 class="font-display font-bold text-[1.05rem] text-red-600">Danger Zone</h2>
                        <p class="text-[12px] text-slate-400 mt-0.5">Aksi berbahaya yang tidak bisa dibatalkan.</p>
                    </div>
                    <div class="px-7 py-6 flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <p class="font-semibold text-[14px] text-slate-900">Logout dari semua perangkat</p>
                            <p class="text-[12px] text-slate-400 mt-0.5">Akhiri semua sesi aktif di perangkat lain.</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST"
                            onsubmit="return confirm('Yakin ingin logout?');">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-[18px] py-[10px] bg-red-50 text-red-600 font-bold text-[13px] rounded-[12px] border border-red-200 hover:bg-red-100 transition-all duration-150 cursor-pointer">
                                <i class="ri-logout-box-line"></i> Logout Semua
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function togglePass(inputId, btn) {
            const inp = document.getElementById(inputId);
            const show = inp.type === 'password';
            inp.type = show ? 'text' : 'password';
            btn.querySelector('i').className = show ? 'ri-eye-off-line text-[16px]' : 'ri-eye-line text-[16px]';
        }
    </script>
@endsection