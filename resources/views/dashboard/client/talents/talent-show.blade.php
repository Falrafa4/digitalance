@extends('layouts.dashboard')
@section('title', 'Profil Talent')

@section('content')
    @php
        $student = $freelancer->skomda_student;
        $displayName = $student->name ?? $freelancer->name ?? 'Freelancer';
        $displayEmail = $student->email ?? '-';
        $displayPhone = $student->phone ?? '-';
        $displayMajor = $student->major ?? 'Siswa SKOMDA';
        $displayClass = $student->class ?? '-';
        $displayNis = $student->nis ?? '-';
        $avatarUrl = $freelancer->profile_photo 
            ? asset('storage/' . $freelancer->profile_photo) 
            : ($student->avatar 
                ? asset('storage/' . $student->avatar) 
                : 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($displayName . '-' . ($displayNis ?? '0')) . '&background=0f766e&color=fff&size=128');
        $serviceItems = $services ?? collect();
        $portfolioItems = $portofolios ?? collect();
        $skillItems = $skillTags ?? collect();
        $orderUrl = $serviceItems->isNotEmpty() ? route('client.orders.create', $serviceItems->first()->id) : '#';
        $profileStats = $stats ?? [
            'services' => $serviceItems->count(),
            'approvedServices' => $serviceItems->count(),
            'portofolios' => $portfolioItems->count(),
            'skills' => $skillItems->count(),
        ];
    @endphp

    <section class="animate-fadeUp">
        <div class="mb-6">
            <a href="{{ route('client.talents.index') }}" class="text-slate-500 font-bold text-[13px] hover:text-slate-900">
                <i class="ri-arrow-left-line mr-1"></i> Kembali
            </a>
        </div>

        <div class="relative overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm mb-6">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900 to-[#0f766e]"></div>
            <div class="absolute -right-16 -top-16 h-44 w-44 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -left-10 bottom-0 h-32 w-32 rounded-full bg-teal-300/20 blur-2xl"></div>
            <div class="relative p-6 sm:p-8 text-white">
                <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-6">
                    <div class="flex flex-col sm:flex-row gap-5 sm:items-center min-w-0">
                        <div class="shrink-0">
                            <img src="{{ $avatarUrl }}" alt="{{ $displayName }}"
                                class="w-28 h-28 sm:w-32 sm:h-32 rounded-[28px] object-cover border-4 border-white/20 shadow-2xl" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-black uppercase tracking-[0.24em] text-teal-100/80">Talent Profile</p>
                            <h1 class="mt-2 font-display text-[2rem] sm:text-[2.45rem] font-extrabold leading-tight truncate">
                                {{ $displayName }}
                            </h1>
                            <p class="mt-2 text-white/80 text-[14px] sm:text-[15px] max-w-2xl leading-relaxed">
                                {{ $freelancer->bio ?? 'Belum ada bio.' }}
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
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ ($freelancer->status ?? '') === 'Approved' ? 'bg-emerald-400/15 border-emerald-300/20 text-emerald-100' : 'bg-amber-400/15 border-amber-300/20 text-amber-100' }} border text-[12px] font-bold">
                                    <i class="ri-shield-user-line"></i> {{ $freelancer->status ?? 'Pending' }}
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
                        <a href="{{ $orderUrl }}"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-[14px] bg-white text-slate-900 font-bold text-[13px] hover:bg-slate-100 transition-all {{ $serviceItems->isEmpty() ? 'pointer-events-none opacity-50' : '' }}">
                            <i class="ri-shopping-bag-3-line"></i> Order Layanan
                        </a>
                        <a href="{{ route('client.services.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-[14px] bg-white/10 border border-white/15 text-white font-bold text-[13px] hover:bg-white/15 transition-all">
                            <i class="ri-service-line"></i> Katalog Jasa
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6 animate-fadeUp-1">
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

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-[18px] border border-slate-200 p-6">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <h2 class="font-display font-bold text-[1.15rem] text-slate-900">Tentang Freelancer</h2>
                            <p class="text-[12px] text-slate-400 mt-0.5">Ringkasan profil yang dapat kamu tinjau sebelum order.</p>
                        </div>
                    </div>

                    @if($freelancer->bio)
                        <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $freelancer->bio }}</p>
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
                            <h2 class="font-display font-bold text-[1.15rem] text-slate-900">Layanan Tersedia</h2>
                            <p class="text-[12px] text-slate-400 mt-0.5">Semua data layanan ditarik dari database.</p>
                        </div>
                    </div>

                    @if($serviceItems->isEmpty())
                        <div class="rounded-[18px] border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-slate-500">
                            Freelancer ini belum punya layanan yang disetujui.
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
                                    <div class="mt-4">
                                        <a href="{{ route('client.orders.create', $service->id) }}"
                                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">
                                            Order Layanan
                                        </a>
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
                            <p class="text-[12px] text-slate-400 mt-0.5">Karya nyata yang terhubung ke layanan freelancer.</p>
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
                    <p class="text-[12px] text-slate-400 mt-0.5">Diambil dari kategori layanan yang tersedia.</p>

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
                        <a href="{{ route('client.talents.index') }}" class="px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 text-slate-700 font-bold text-[13px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">Cari Talent Lain</a>
                        <a href="{{ route('client.orders.index') }}" class="px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 text-slate-700 font-bold text-[13px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">Lihat Orders</a>
                        <a href="{{ route('client.profile') }}" class="px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 text-slate-700 font-bold text-[13px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">Profil Saya</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection