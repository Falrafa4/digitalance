@extends('layouts.dashboard')
@section('title', 'Profil Freelancer | Digitalance')

@section('content')
    <div class="content-scroll flex-1 px-4 sm:px-8 py-7 overflow-y-auto">

        {{-- Page Header --}}
        <div class="mb-8 animate-fadeUp">
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Profil Freelancer</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Kelola informasi akun dan keamanan.</p>
        </div>

        @php
            $profile = $freelancer ?? $user;
            $student = $profile->skomda_student;
            $displayName = $student?->name ?? $profile->name ?? 'Freelancer';
            $displayEmail = $student?->email ?? $profile->email ?? '-';
            $displayPhone = $student?->phone ?? '-';
            $displayMajor = $student?->major ?? 'Siswa SKOMDA';
            $displayClass = $student?->class ?? '-';
            $displayNis = $student?->nis ?? '-';
            $avatarFallbackUrl = 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode('freelancer-' . $displayNis . '-' . random_int(1000, 999999));
            $avatarUrl = $profile->profile_photo ? asset('storage/' . $profile->profile_photo) : $avatarFallbackUrl;
            $serviceItems = $services ?? collect();
            $portfolioItems = $portofolios ?? collect();
            $skillItems = $skillTags ?? collect();
            $approvedServiceItems = $approvedServices ?? $serviceItems->where('status', 'Approved');
            $profileStats = $stats ?? [
                'services' => $serviceItems->count(),
                'approvedServices' => $approvedServiceItems->count(),
                'portofolios' => $portfolioItems->count(),
                'skills' => $skillItems->count(),
            ];
        @endphp

        <div class="mb-6 animate-fadeUp-1">
            <div class="relative overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900 to-[#0f766e]"></div>
                <div class="absolute -right-16 -top-16 h-44 w-44 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -left-10 bottom-0 h-32 w-32 rounded-full bg-teal-300/20 blur-2xl"></div>
                <div class="relative p-6 sm:p-8 text-white">
                    <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
                        <div class="flex flex-col sm:flex-row gap-5 sm:items-center min-w-0">
                            <div class="shrink-0">
                                <x-avatar :user="$user" role="freelancer" :size="128" class="w-28 h-28 sm:w-32 sm:h-32 rounded-[28px] object-cover border-4 border-white/20 shadow-2xl" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-black uppercase tracking-[0.24em] text-teal-100/80">Freelancer
                                    Profile</p>
                                <h2 class="mt-2 font-display text-[2rem] sm:text-[2.45rem] font-extrabold leading-tight truncate">
                                    {{ $displayName }}
                                </h2>
                                <p class="mt-2 text-white/80 text-[14px] sm:text-[15px] max-w-2xl leading-relaxed">
                                    {{ $profile->bio ?? 'Belum ada bio.' }}
                                </p>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-[12px] font-bold">
                                        <i class="ri-user-3-line"></i> {{ $displayMajor }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-[12px] font-bold">
                                        <i class="ri-graduation-cap-line"></i> {{ $displayClass }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-[12px] font-bold">
                                        <i class="ri-id-card-line"></i> NIS {{ $displayNis }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ ($profile->status ?? '') === 'Approved' ? 'bg-emerald-400/15 border-emerald-300/20 text-emerald-100' : 'bg-amber-400/15 border-amber-300/20 text-amber-100' }} border text-[12px] font-bold">
                                        <i class="ri-shield-user-line"></i> {{ $profile->status ?? 'Pending' }}
                                    </span>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-3 text-[13px] text-white/78">
                                    <span class="inline-flex items-center gap-2">
                                        <i class="ri-mail-line"></i> {{ $displayEmail }}
                                    </span>
                                    <span class="inline-flex items-center gap-2">
                                        <i class="ri-phone-line"></i> {{ $displayPhone }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 xl:justify-end">
                            <a href="{{ route('freelancer.services.index') }}"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-[14px] bg-white text-slate-900 font-bold text-[13px] hover:bg-slate-100 transition-all">
                                <i class="ri-service-line"></i> Lihat Layanan
                            </a>
                            <a href="{{ route('freelancer.portofolios.index') }}"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-[14px] bg-white/10 border border-white/15 text-white font-bold text-[13px] hover:bg-white/15 transition-all">
                                <i class="ri-layout-grid-line"></i> Portofolio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6 animate-fadeUp-2">
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">Total Layanan</p>
                <p class="text-[30px] font-extrabold text-slate-900 mt-2">{{ $profileStats['services'] }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">Layanan Disetujui</p>
                <p class="text-[30px] font-extrabold text-slate-900 mt-2">{{ $profileStats['approvedServices'] }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">Portofolio</p>
                <p class="text-[30px] font-extrabold text-slate-900 mt-2">{{ $profileStats['portofolios'] }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">Skill Area</p>
                <p class="text-[30px] font-extrabold text-slate-900 mt-2">{{ $profileStats['skills'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6 animate-fadeUp-3">
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-[18px] border border-slate-200 p-6">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <h2 class="font-display font-bold text-[1.15rem] text-slate-900">Tentang Freelancer</h2>
                            <p class="text-[12px] text-slate-400 mt-0.5">Ringkasan profil yang dibaca klien.</p>
                        </div>
                    </div>

                    @if($profile->bio)
                        <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $profile->bio }}</p>
                    @else
                        <div class="rounded-[16px] border border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-slate-500 text-[13px]">
                            Belum ada bio yang ditulis.
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5">
                        <div class="rounded-[16px] border border-slate-200 px-4 py-3">
                            <p class="text-[11px] font-black uppercase tracking-[.12em] text-slate-400">Email</p>
                            <p class="text-slate-900 font-semibold mt-1 break-all">{{ $displayEmail }}</p>
                        </div>
                        <div class="rounded-[16px] border border-slate-200 px-4 py-3">
                            <p class="text-[11px] font-black uppercase tracking-[.12em] text-slate-400">Telepon</p>
                            <p class="text-slate-900 font-semibold mt-1">{{ $displayPhone }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[18px] border border-slate-200 p-6">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <h2 class="font-display font-bold text-[1.15rem] text-slate-900">Layanan Saya</h2>
                            <p class="text-[12px] text-slate-400 mt-0.5">Data layanan ditarik langsung dari database.</p>
                        </div>
                    </div>

                    @if($serviceItems->isEmpty())
                        <div class="rounded-[18px] border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-slate-500">
                            Belum ada layanan yang ditambahkan.
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($serviceItems as $service)
                                <div class="rounded-[18px] border border-slate-200 p-5 hover:shadow-md transition-all">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-extrabold text-slate-900 truncate">{{ $service->title }}</p>
                                            <p class="text-[13px] text-slate-500 mt-1">
                                                {{ $service->category->name ?? '-' }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wide {{ $service->status === 'Approved' ? 'bg-emerald-50 text-emerald-700' : ($service->status === 'Rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                            {{ $service->status }}
                                        </span>
                                    </div>
                                    <p class="text-slate-600 text-[13px] leading-relaxed mt-3 line-clamp-3">
                                        {{ $service->description }}
                                    </p>
                                    <div class="flex flex-wrap gap-2 mt-4 text-[12px] text-slate-500 font-semibold">
                                        <span class="px-3 py-1 rounded-full bg-slate-100">Rp{{ number_format((float) ($service->price_min ?? 0), 0, ',', '.') }}</span>
                                        <span class="px-3 py-1 rounded-full bg-slate-100">Sampai Rp{{ number_format((float) ($service->price_max ?? 0), 0, ',', '.') }}</span>
                                        <span class="px-3 py-1 rounded-full bg-slate-100">{{ $service->delivery_time ?? '-' }} hari</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-[18px] border border-slate-200 p-6">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <h2 class="font-display font-bold text-[1.15rem] text-slate-900">Portofolio</h2>
                            <p class="text-[12px] text-slate-400 mt-0.5">Karya nyata yang terhubung ke layanan.</p>
                        </div>
                    </div>

                    @if($portfolioItems->isEmpty())
                        <div class="rounded-[18px] border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-slate-500">
                            Belum ada portofolio yang ditambahkan.
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($portfolioItems as $portfolio)
                                <div class="rounded-[18px] border border-slate-200 overflow-hidden bg-white hover:shadow-md transition-all">
                                    <div class="aspect-[16/10] bg-slate-100">
                                        @if($portfolio->media_url)
                                            <img src="{{ asset('storage/' . $portfolio->media_url) }}" alt="{{ $portfolio->title }}"
                                                class="h-full w-full object-cover" />
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-slate-300 text-sm font-bold">
                                                Media tidak tersedia
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <p class="font-extrabold text-slate-900 truncate">{{ $portfolio->title }}</p>
                                        <p class="text-[13px] text-slate-500 mt-1 line-clamp-2">
                                            {{ $portfolio->description ?? 'Belum ada deskripsi.' }}
                                        </p>
                                        <p class="text-[12px] text-slate-400 mt-3">
                                            {{ $portfolio->service->title ?? 'Layanan terkait' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-[18px] border border-slate-200 p-6">
                    <h2 class="font-display font-bold text-[1.15rem] text-slate-900">Skill Area</h2>
                    <p class="text-[12px] text-slate-400 mt-0.5">Diambil dari kategori layanan yang kamu punya.</p>

                    @if($skillItems->isEmpty())
                        <div class="mt-4 rounded-[16px] border border-dashed border-slate-200 bg-slate-50 p-5 text-slate-500 text-[13px]">
                            Belum ada kategori layanan.
                        </div>
                    @else
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($skillItems as $skill)
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1.5 text-[12px] font-bold text-slate-700">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-[18px] border border-slate-200 p-6">
                    <h2 class="font-display font-bold text-[1.15rem] text-slate-900">Akses Cepat</h2>
                    <div class="mt-4 flex flex-col gap-2">
                        <a href="{{ route('freelancer.profile') }}" class="px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 text-slate-700 font-bold text-[13px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">Refresh Profil</a>
                        <a href="{{ route('freelancer.services.index') }}" class="px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 text-slate-700 font-bold text-[13px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">Kelola Layanan</a>
                        <a href="{{ route('freelancer.portofolios.index') }}" class="px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 text-slate-700 font-bold text-[13px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">Kelola Portofolio</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Sidebar Profile --}}
            <div class="lg:col-span-1">
                <div
                    class="bg-white rounded-[18px] border border-slate-200 p-6 flex flex-col items-center text-center animate-fadeUp">
<div class="relative mb-4">
                            <x-avatar :user="$user" role="freelancer" :size="128" class="w-24 h-24 rounded-[18px] object-cover border-4 border-white shadow-teal-md" />
                        </div>
                    <h3 class="font-display font-extrabold text-[1.15rem] text-slate-900">{{ $user->name }}</h3>
                    <p class="text-[13px] text-slate-400 mt-0.5">{{ $user->email }}</p>
                    <span
                        class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 bg-orange-50 text-orange-600 text-[11px] font-bold rounded-full">
                        <i class="ri-vip-crown-line"></i> Freelancer
                    </span>
                    @if($user->skomda_student)
                        <p class="text-xs text-slate-500 mt-3 font-semibold">Terkoneksi dengan akun Skomda Student (NIS:
                            {{ $user->skomda_student->nis }})</p>
                    @endif
                </div>
            </div>

            {{-- Main Form --}}
            <div class="lg:col-span-2 flex flex-col gap-5">

                {{-- Informasi Umum --}}
                <div class="bg-white rounded-[18px] border border-slate-200 animate-fadeUp-1">
                    <div class="px-7 py-5 border-b border-slate-100">
                        <h2 class="font-display font-bold text-[1.05rem] text-slate-900">Informasi Umum</h2>
                        <p class="text-[12px] text-slate-400 mt-0.5">Perbarui bio dan nomor telepon.</p>
                    </div>
                    <div class="px-7 py-6">
                        @if(session('success'))
                            <div
                                class="flex items-center gap-3 mb-5 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-[13px] font-semibold">
                                <i class="ri-check-double-line text-emerald-500 text-[17px]"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('freelancer.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="flex flex-col gap-1.5 mb-4">
                                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Foto
                                    Profil</label>
                                <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp"
                                    class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 file:mr-4 file:rounded-[9px] file:border-0 file:bg-[#0f766e] file:px-4 file:py-2 file:text-[12px] file:font-bold file:text-white hover:file:bg-[#0a5e58] focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)] @error('profile_photo') border-red-400 @enderror" />
                                <p class="text-[10px] text-slate-400 mt-0.5">Format JPG, PNG, atau WEBP. Maksimal 5 MB.</p>
                                @error('profile_photo')<p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-1.5 mb-4">
                                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Bio /
                                    Deskripsi Diri</label>
                                <textarea name="bio" rows="3"
                                    class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)] resize-none @error('bio') border-red-400 @enderror">{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')<p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Nama
                                        Lengkap</label>
                                    <input type="text" value="{{ $user->name }}" disabled
                                        class="py-[10px] px-[13px] bg-slate-100 text-slate-500 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none cursor-not-allowed" />
                                    <p class="text-[10px] text-slate-400 mt-0.5">Nama dapatkan dari data Skomda.</p>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label
                                        class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Email</label>
                                    <input type="email" value="{{ $user->email }}" disabled
                                        class="py-[10px] px-[13px] bg-slate-100 text-slate-500 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none cursor-not-allowed" />
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
                <div class="bg-white rounded-[18px] border border-slate-200 animate-fadeUp-2">
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
                        @if(session('error'))
                            <div
                                class="flex items-center gap-3 mb-5 px-4 py-3 bg-red-50 border border-red-200 rounded-2xl text-red-800 text-[13px] font-semibold">
                                <i class="ri-error-warning-line text-red-500 text-[17px]"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('freelancer.password.update') }}" method="POST">
                            @csrf
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
                                    <i class="ri-lock-password-line"></i> Ubah Kata Sandi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Danger Zone --}}
                <div class="bg-white rounded-[18px] border border-red-100 animate-fadeUp-3">
                    <div class="px-7 py-5 border-b border-red-100">
                        <h2 class="font-display font-bold text-[1.05rem] text-red-600">Zona Berbahaya</h2>
                        <p class="text-[12px] text-slate-400 mt-0.5">Aksi berbahaya yang tidak bisa dibatalkan.</p>
                    </div>
                    <div class="px-7 py-6 flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <p class="font-semibold text-[14px] text-slate-900">Logout dari semua perangkat</p>
                            <p class="text-[12px] text-slate-400 mt-0.5">Akhiri semua sesi aktif di perangkat lain.</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST"
                            onsubmit="event.preventDefault(); customConfirm('Yakin ingin logout?').then(res => { if(res) this.submit(); });">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-[18px] py-[10px] bg-red-50 text-red-600 font-bold text-[13px] rounded-[12px] border border-red-200 hover:bg-red-100 transition-all duration-150 cursor-pointer">
                                <i class="ri-logout-box-line"></i> Keluar dari Semua Perangkat
                            </button>
                        </form>
                    </div>
                </div>

                    {{-- Delete Account (Danger) --}}
                    <div class="mt-6 bg-white rounded-[18px] border border-red-100 p-5">
                        <h3 class="font-display font-bold text-red-600 mb-2">Hapus Akun Freelancer</h3>
                        <p class="text-sm text-slate-500 mb-4">Menghapus akun akan mengembalikan data SkomdaStudent apabila email sekolah terdeteksi.</p>
                        <form action="{{ route('freelancer.delete') }}" method="POST" onsubmit="event.preventDefault(); customConfirm('Yakin ingin menghapus akun? Semua data terkait akan dihapus.').then(res => { if(res) this.submit(); });">
                            @csrf
                            <div class="mb-3">
                                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Masukkan Password</label>
                                <input type="password" name="password" required class="w-full py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none" />
                            </div>
                            <button type="submit" class="inline-flex items-center gap-2 px-[18px] py-[10px] bg-red-50 text-red-600 font-bold text-[13px] rounded-[12px] border border-red-200 hover:bg-red-100 transition-all duration-150 cursor-pointer">
                                <i class="ri-delete-bin-line"></i> Hapus Akun
                            </button>
                        </form>
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