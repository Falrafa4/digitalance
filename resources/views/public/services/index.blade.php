@extends('layouts.app')

@section('title', 'Services - Digitalance')

@section('content')
    <section class="pt-24 pb-16 bg-slate-100 min-h-screen" id="services">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_0.8fr] gap-10 items-end mb-10">
                <div>
                    <div
                        class="inline-flex items-center gap-2 bg-emerald-500/10 text-emerald-700 px-4 py-2 rounded-full text-sm font-bold mb-4">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Katalog jasa berbasis kategori aktif
                    </div>
                    <h1
                        class="font-display text-[clamp(2rem,4vw,3.6rem)] font-extrabold leading-[1.1] tracking-tight text-slate-900 max-w-3xl">
                        Temukan layanan <span class="gradient-text">SKOMDA</span> yang paling sesuai.
                    </h1>
                    <p class="text-slate-500 text-lg leading-relaxed max-w-2xl mt-4">
                        Telusuri layanan yang sudah dipublish freelancer, difilter berdasarkan kategori, dan cari lewat kata
                        kunci, judul, atau nama freelancer.
                    </p>
                </div>

                <div
                    class="glass-card rounded-[1.75rem] p-6 border border-white/60 shadow-[0_20px_60px_rgba(15,23,42,.08)]">
                    <form action="{{ route('services.index') }}" method="GET" class="space-y-4">
                        <div>
                            <label for="serviceSearch" class="block text-sm font-bold text-slate-700 mb-2">Cari
                                layanan</label>
                            <div class="relative">
                                <input id="serviceSearch" type="text" name="q" value="{{ $search }}"
                                    placeholder="Web Design, Logo, Video Editing..."
                                    class="w-full px-5 py-4 pr-32 rounded-2xl border-2 border-slate-200 bg-white text-slate-900 focus:outline-none focus:border-primary transition-all" />
                                <button type="submit"
                                    class="absolute right-2 top-2 bottom-2 px-5 rounded-xl bg-primary text-white font-bold text-sm hover:bg-teal-800 transition-all">
                                    Search
                                </button>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-slate-700 mb-2">Filter kategori</p>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('services.index', ['q' => $search ?: null]) }}"
                                    class="px-4 py-2 rounded-full text-sm font-bold transition-all {{ empty($categoryId) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    Semua Kategori
                                </a>
                                @foreach($categories as $category)
                                    <a href="{{ route('services.index', array_filter(['q' => $search ?: null, 'category' => $category->id])) }}"
                                        class="px-4 py-2 rounded-full text-sm font-bold transition-all {{ (string) $categoryId === (string) $category->id ? 'bg-emerald-500 text-white' : 'bg-emerald-500/10 text-emerald-700 hover:bg-emerald-500/20' }}">
                                        {{ $category->name }}
                                        <span
                                            class="ml-1 text-[11px] opacity-80">({{ $category->approved_services_count }})</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($featuredServices->isNotEmpty())
                <div class="mb-12">
                    <div class="flex items-center justify-between gap-4 mb-5">
                        <div>
                            <h2 class="font-display text-2xl font-extrabold text-slate-900">Featured Services</h2>
                            <p class="text-slate-500 text-sm mt-1">Pilihan layanan terbaru dari freelancer aktif.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @foreach($featuredServices as $service)
                            <article
                                class="reveal bg-white rounded-[1.75rem] border border-slate-200 p-6 shadow-[0_12px_30px_rgba(15,23,42,.05)] hover:-translate-y-1 transition-all">
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="w-12 h-12 rounded-2xl gradient-bg shrink-0"></div>
                                    <span
                                        class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-600">{{ $service->category->name ?? 'Layanan' }}</span>
                                </div>
                                <h3 class="font-extrabold text-lg text-slate-900 leading-snug">{{ $service->title }}</h3>
                                <p class="text-sm text-slate-500 mt-2 leading-relaxed line-clamp-3">{{ $service->description }}</p>
                                <div class="flex items-center justify-between mt-5 pt-5 border-t border-slate-100">
                                    <div>
                                        <p class="text-xs text-slate-400 font-semibold">Freelancer</p>
                                        <p class="text-sm font-bold text-slate-800">
                                            {{ optional(optional($service->freelancer)->skomda_student)->name ?? 'Unknown' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-slate-400 font-semibold">Mulai dari</p>
                                        <p class="text-base font-extrabold text-emerald-600">Rp
                                            {{ number_format((float) $service->price_min, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-between gap-4 mb-5">
                <div>
                    <h2 class="font-display text-2xl font-extrabold text-slate-900">Semua Layanan</h2>
                    <p class="text-slate-500 text-sm mt-1">Hanya menampilkan layanan dengan status approved.</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-slate-700">{{ $services->total() }} layanan ditemukan</p>
                    <p class="text-xs text-slate-400">Kategori aktif: {{ $categories->count() }}</p>
                </div>
            </div>

            @if($services->isEmpty())
                <div class="bg-white rounded-[1.75rem] border border-dashed border-slate-300 p-10 text-center">
                    <h3 class="font-extrabold text-xl text-slate-900">Belum ada layanan yang cocok</h3>
                    <p class="text-slate-500 mt-2">Coba ubah kata kunci atau pilih kategori lain.</p>
                    <a href="{{ route('services.index') }}"
                        class="inline-flex items-center justify-center mt-6 px-6 py-3 rounded-2xl bg-slate-900 text-white font-bold hover:bg-black transition-all">
                        Reset Filter
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($services as $service)
                        <article
                            class="reveal bg-white rounded-[1.75rem] border border-slate-200 p-6 shadow-[0_12px_30px_rgba(15,23,42,.05)] hover:-translate-y-1 transition-all">
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="w-12 h-12 rounded-2xl gradient-bg shrink-0"></div>
                                <span
                                    class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-700">{{ $service->category->name ?? '-' }}</span>
                            </div>

                            <h3 class="font-extrabold text-xl text-slate-900 leading-snug">{{ $service->title }}</h3>
                            <p class="text-sm text-slate-500 mt-3 leading-relaxed line-clamp-4">{{ $service->description }}</p>

                            <div class="grid grid-cols-2 gap-3 mt-5">
                                <div class="rounded-2xl bg-slate-50 p-3">
                                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Harga</p>
                                    <p class="font-extrabold text-slate-900 mt-1">
                                        Rp {{ number_format((float) $service->price_min, 0, ',', '.') }}
                                        @if(!is_null($service->price_max))
                                            <span class="text-slate-400 font-semibold">- Rp
                                                {{ number_format((float) $service->price_max, 0, ',', '.') }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-3">
                                    <p class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Estimasi</p>
                                    <p class="font-extrabold text-slate-900 mt-1">
                                        {{ $service->delivery_time ? $service->delivery_time . ' hari' : 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-5 pt-5 border-t border-slate-100">
                                <div>
                                    <p class="text-xs text-slate-400 font-semibold">Freelancer</p>
                                    <p class="text-sm font-bold text-slate-800">
                                        {{ optional(optional($service->freelancer)->skomda_student)->name ?? 'Unknown' }}</p>
                                </div>
                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-sm hover:bg-black transition-all">
                                    Hire
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                        <polyline points="12 5 19 12 12 19" />
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection