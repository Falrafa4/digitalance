@extends('layouts.dashboard')
@section('title', 'Client Dashboard | Digitalance')

@section('content')
    <div class="animate-fadeUp">

        {{-- HERO / GREETING (mirip UI gambar) --}}
        <section class="mb-9">
            <h1 class="font-display text-[2.6rem] sm:text-[3.1rem] font-extrabold text-slate-900 leading-tight">
                Hi, {{ $user->name }}!
                <span class="inline-block align-middle">👋</span>
            </h1>
            <p class="text-slate-500 text-[1.02rem] mt-2">
                Here's what's happening with your projects today.
            </p>
        </section>

        {{-- STAT CARDS (4 box) --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-10 animate-fadeUp-delay-1">

            {{-- TOTAL ORDERS --}}
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div
                        class="w-[54px] h-[54px] rounded-[16px] bg-emerald-50 flex items-center justify-center text-[#0f766e]">
                        <i class="ri-file-list-3-line text-[22px]"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">TOTAL ORDERS</p>
                        <p class="text-[28px] font-extrabold text-slate-900 leading-tight mt-1">
                            {{ $projects->count() ? $projects->count() + ($activeProjects + $completedProjects - $projects->count()) : ($activeProjects + $completedProjects) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ACTIVE PROJECTS --}}
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="w-[54px] h-[54px] rounded-[16px] bg-blue-50 flex items-center justify-center text-blue-700">
                        <i class="ri-timer-line text-[22px]"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">ACTIVE PROJECTS</p>
                        <p class="text-[28px] font-extrabold text-slate-900 leading-tight mt-1">{{ $activeProjects }}</p>
                    </div>
                </div>
            </div>

            {{-- COMPLETED --}}
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div
                        class="w-[54px] h-[54px] rounded-[16px] bg-amber-50 flex items-center justify-center text-amber-700">
                        <i class="ri-medal-line text-[22px]"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">COMPLETED</p>
                        <p class="text-[28px] font-extrabold text-slate-900 leading-tight mt-1">{{ $completedProjects }}</p>
                    </div>
                </div>
            </div>

            {{-- TOTAL SPENT --}}
            <div class="bg-white border border-slate-200 rounded-[18px] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div
                        class="w-[54px] h-[54px] rounded-[16px] bg-emerald-50 flex items-center justify-center text-[#0f766e]">
                        <i class="ri-wallet-3-line text-[22px]"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">TOTAL SPENT</p>
                        <p class="text-[22px] sm:text-[24px] font-extrabold text-slate-900 leading-tight mt-1">
                            Rp {{ number_format((float) $totalSpent, 0, ',', '.') }}
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
                        <h2 class="font-display text-[1.55rem] font-extrabold text-slate-900">My Projects</h2>
                        <p class="text-slate-500 text-[0.95rem] mt-1">Pantau ringkasan order terakhir kamu.</p>
                    </div>

                    <a href="{{ route('client.orders.index') }}" class="px-4 py-2 rounded-[11px] border-[1.5px] border-slate-200 bg-white text-slate-700 font-bold text-[12.5px]
                                hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
                        View All
                    </a>
                </div>

                <div class="bg-white border border-slate-200 rounded-[18px] overflow-hidden">
                    @if($projects->count())
                        <div class="divide-y divide-slate-100">
                            @foreach($projects as $o)
                                <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-slate-900 font-extrabold text-[14.5px] truncate">
                                            {{ $o->service->title ?? $o->service->name ?? 'Service' }}
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
                                            Detail
                                        </a>
                                        <a href="{{ route('client.services.show', $o->service_id) }}" class="px-4 py-2.5 rounded-[12px] bg-white border-[1.5px] border-slate-200 text-slate-700 font-bold text-[12.5px]
                                                                    hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
                                            Lihat Jasa
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-10">
                            <div class="border-2 border-dashed border-slate-200 rounded-[18px] p-10 text-center">
                                <div class="text-slate-300 text-[44px] mb-3">
                                    <i class="ri-inbox-2-line"></i>
                                </div>
                                <p class="text-slate-900 font-extrabold text-[1.25rem]">No Projects Yet</p>
                                <p class="text-slate-500 mt-2">Mulai order pertamamu dari katalog jasa.</p>
                                <a href="{{ route('client.services.index') }}"
                                    class="inline-flex items-center justify-center mt-5 px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
                                    Browse Katalog
                                    <i class="ri-arrow-right-line ml-2"></i>
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
                        <h2 class="font-display text-[1.55rem] font-extrabold text-slate-900">System Alerts</h2>
                        <p class="text-slate-500 text-[0.95rem] mt-1">Info sistem untuk akunmu.</p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-[18px] p-5">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-[48px] h-[48px] rounded-[16px] bg-emerald-50 flex items-center justify-center text-[#0f766e]">
                            <i class="ri-shield-check-line text-[22px]"></i>
                        </div>

                        <div class="flex-1">
                            <p class="font-extrabold text-slate-900 text-[14.5px]">Semua Sistem Normal</p>
                            <p class="text-slate-500 text-[13px] mt-1">
                                Tidak ada masalah terdeteksi.
                            </p>
                        </div>
                    </div>

                    {{-- Optional hint --}}
                    <div class="mt-5 p-4 rounded-[16px] bg-slate-50 border border-slate-100">
                        <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">Quick Tips</p>
                        <p class="text-slate-600 text-[13px] mt-1">
                            Untuk mempercepat proses, isi brief yang jelas dan tentukan deadline yang realistis.
                        </p>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="mt-6 bg-white border border-slate-200 rounded-[18px] p-5">
                    <p class="text-[12px] font-extrabold text-slate-400 uppercase tracking-[.12em]">Quick Actions</p>
                    <div class="mt-4 flex flex-col gap-2.5">
                        <a href="{{ route('client.services.index') }}"
                            class="flex items-center justify-between px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 hover:border-[#0f766e] transition-all">
                            <span class="font-bold text-[13px] text-slate-800">Find Talent</span>
                            <i class="ri-arrow-right-line text-slate-400"></i>
                        </a>

                        <a href="{{ route('client.orders.index') }}"
                            class="flex items-center justify-between px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 hover:border-[#0f766e] transition-all">
                            <span class="font-bold text-[13px] text-slate-800">Orders</span>
                            <i class="ri-arrow-right-line text-slate-400"></i>
                        </a>

                        <a href="{{ route('client.profile') }}"
                            class="flex items-center justify-between px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 hover:border-[#0f766e] transition-all">
                            <span class="font-bold text-[13px] text-slate-800">Account</span>
                            <i class="ri-arrow-right-line text-slate-400"></i>
                        </a>
                    </div>
                </div>

            </div>
        </section>

    </div>
@endsection