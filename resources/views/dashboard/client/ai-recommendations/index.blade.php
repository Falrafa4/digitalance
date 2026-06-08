@extends('layouts.dashboard')
@section('title', 'Rekomendasi Freelancer AI | Digitalance')

@section('content')
<div class="animate-fadeUp">
    {{-- Header --}}
    <section class="mb-8">
        <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Rekomendasi Freelancer AI</h1>
        <p class="text-slate-500 mt-1">Gunakan kecerdasan buatan Groq AI untuk mencari dan mencocokkan siswa freelancer terbaik untuk proyek Anda.</p>
    </section>

    {{-- Filter Forms --}}
    <section class="bg-white border border-slate-200 rounded-[22px] p-6 mb-8 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Match by Job Posting --}}
            <div>
                <h3 class="font-bold text-slate-900 text-[14.5px] mb-3 flex items-center gap-2">
                    <i class="ri-briefcase-2-line text-[#0f766e]"></i> Cocokkan Berdasarkan Lowongan Kerja
                </h3>
                <form action="{{ route('client.ai-recommendations') }}" method="GET" class="space-y-3">
                    <div>
                        <select name="loker_id" class="w-full px-4 py-3 border-[1.5px] border-slate-200 rounded-[12px] text-[13.5px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]">
                            <option value="">-- Pilih Lowongan Kerja Anda --</option>
                            @foreach($lokers as $loker)
                                <option value="{{ $loker->id }}" {{ isset($activeLoker) && $activeLoker->id == $loker->id ? 'selected' : '' }}>
                                    {{ $loker->title }} (Budget: Rp {{ number_format($loker->budget_min, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-5 py-3 bg-[#0f766e] text-white rounded-xl font-bold text-[13px] hover:bg-[#0a5e58] transition-all shadow-md shadow-teal-sm flex items-center justify-center gap-2">
                        <i class="ri-ai-generate"></i> Cocokkan AI
                    </button>
                </form>
            </div>

            {{-- Custom Search Query --}}
            <div>
                <h3 class="font-bold text-slate-900 text-[14.5px] mb-3 flex items-center gap-2">
                    <i class="ri-search-eye-line text-[#0f766e]"></i> Pencarian Custom AI
                </h3>
                <form action="{{ route('client.ai-recommendations') }}" method="GET" class="space-y-3">
                    <div class="relative">
                        <i class="ri-magic-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="q" placeholder="Contoh: 'Butuh developer laravel yang paham mysql'..." 
                               value="{{ $customQuery ?? '' }}"
                               class="w-full pl-10 pr-4 py-3 border-[1.5px] border-slate-200 rounded-[12px] text-[13.5px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]" />
                    </div>
                    <button type="submit" class="px-5 py-3 bg-slate-900 text-white rounded-xl font-bold text-[13px] hover:bg-black transition-all shadow-md flex items-center justify-center gap-2">
                        <i class="ri-cpu-line"></i> Temukan Talent
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- Recommendations Results --}}
    @if(isset($activeLoker) || isset($customQuery))
        <h2 class="font-display text-[1.45rem] font-extrabold text-slate-900 mb-5 flex items-center gap-2">
            <i class="ri-sparkling-fill text-amber-500"></i> Hasil Rekomendasi AI
            @if(isset($activeLoker))
                <span class="text-xs font-semibold px-2.5 py-1 bg-teal-50 border border-teal-100 text-[#0f766e] rounded-full">Loker: {{ $activeLoker->title }}</span>
            @else
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full">Kueri: "{{ $customQuery }}"</span>
            @endif
        </h2>

        @if(empty($recommendations))
            <div class="py-12 px-6 text-center bg-white border border-slate-200 rounded-[22px]">
                <div class="text-slate-300 text-[56px] mb-3"><i class="ri-emotion-sad-line"></i></div>
                <h3 class="font-extrabold text-slate-900 text-lg">Tidak ada kecocokan ditemukan</h3>
                <p class="text-slate-500 mt-2 text-sm">Coba gunakan kueri atau deskripsi Loker yang lebih umum.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($recommendations as $rec)
                    @php
                        $f = $rec['freelancer'];
                        $score = $rec['score'];
                        $analysis = $rec['analysis'];
                        $breakdown = $rec['breakdown'];

                        // Color class based on match score
                        $scoreColor = 'text-emerald-600 bg-emerald-50 border-emerald-100';
                        $scoreStroke = '#10b981';
                        if ($score < 80 && $score >= 60) {
                            $scoreColor = 'text-indigo-600 bg-indigo-50 border-indigo-100';
                            $scoreStroke = '#6366f1';
                        } elseif ($score < 60) {
                            $scoreColor = 'text-amber-600 bg-amber-50 border-amber-100';
                            $scoreStroke = '#f59e0b';
                        }
                    @endphp
                    <div class="bg-white border border-slate-200 rounded-[22px] p-6 hover:shadow-lg transition-all duration-300">
                        <div class="flex flex-col lg:flex-row gap-6">
                            
                            {{-- LEFT: Freelancer profile & Score Ring --}}
                            <div class="flex items-start gap-4 lg:w-1/3 min-w-0">
                                <div class="relative flex-shrink-0">
                                    @php
                                        $avatarUrl = $f->profile_photo 
                                            ? asset('storage/' . $f->profile_photo) 
                                            : 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($f->skomda_student->name ?? 'Freelancer') . '&background=0f766e&color=fff&size=64';
                                    @endphp
                                    <img src="{{ $avatarUrl }}" class="w-16 h-16 rounded-2xl bg-slate-100 object-cover" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-black text-slate-900 text-base truncate">{{ $f->skomda_student->name ?? 'Freelancer' }}</h3>
                                    <p class="text-[#0f766e] text-[11px] font-bold uppercase tracking-wider mt-0.5">{{ $f->skomda_student->major ?? 'Siswa' }} | {{ $f->skomda_student->class ?? '' }}</p>
                                    
                                    {{-- Career Track Badge --}}
                                    @if($f->career_track && $f->career_track_status === 'Approved')
                                        <div class="mt-2 inline-flex items-center gap-1 px-2.5 py-0.5 bg-blue-50 border border-blue-100 text-blue-700 text-[11px] font-bold rounded-full">
                                            <i class="ri-verified-badge-line"></i> {{ $f->career_track }}
                                        </div>
                                    @endif

                                    <div class="mt-3 flex items-center gap-3">
                                        <div class="flex items-center text-amber-500 font-bold text-[12.5px]">
                                            <i class="ri-star-fill mr-1"></i>
                                            @php
                                                $rating = \App\Models\Review::whereHas('order.service', fn($q) => $q->where('freelancer_id', $f->id))->avg('rating') ?? 0.0;
                                            @endphp
                                            {{ $rating > 0 ? number_format($rating, 1) : 'Baru' }}
                                        </div>
                                        <div class="text-slate-400 text-[12.5px]">•</div>
                                        <div class="text-slate-500 font-semibold text-[12.5px]">
                                            @php
                                                $completedCount = \App\Models\Order::where('freelancer_id', $f->id)->where('status', 'Completed')->count();
                                            @endphp
                                            {{ $completedCount }} Proyek Selesai
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- CENTER: Match Gauge & Breakdown --}}
                            <div class="flex-1 lg:border-x lg:border-slate-100 lg:px-6">
                                <div class="flex items-center gap-6">
                                    {{-- Circular Score --}}
                                    <div class="relative w-18 h-18 flex-shrink-0 flex items-center justify-center">
                                        <svg class="absolute inset-0 w-full h-full transform -rotate-90">
                                            <circle cx="36" cy="36" r="30" stroke="#f1f5f9" stroke-width="6" fill="transparent" />
                                            <circle cx="36" cy="36" r="30" stroke="{{ $scoreStroke }}" stroke-width="6" fill="transparent"
                                                    stroke-dasharray="188.4" stroke-dashoffset="{{ 188.4 - (188.4 * $score) / 100 }}" />
                                        </svg>
                                        <div class="text-center">
                                            <span class="text-lg font-black text-slate-800">{{ $score }}%</span>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Match</p>
                                        </div>
                                    </div>

                                    {{-- Breakdown progress bars --}}
                                    <div class="flex-1 space-y-2">
                                        {{-- Skills Match --}}
                                        <div>
                                            <div class="flex justify-between text-[11px] font-bold text-slate-500 mb-0.5">
                                                <span>Kesesuaian Keahlian</span>
                                                <span>{{ $breakdown['skills'] ?? 0 }}%</span>
                                            </div>
                                            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $breakdown['skills'] ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                        {{-- Category Match --}}
                                        <div>
                                            <div class="flex justify-between text-[11px] font-bold text-slate-500 mb-0.5">
                                                <span>Kesesuaian Kategori</span>
                                                <span>{{ $breakdown['category'] ?? 0 }}%</span>
                                            </div>
                                            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-[#0f766e] rounded-full" style="width: {{ $breakdown['category'] ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                        {{-- Performance Match --}}
                                        <div>
                                            <div class="flex justify-between text-[11px] font-bold text-slate-500 mb-0.5">
                                                <span>Performa & Reputasi</span>
                                                <span>{{ $breakdown['performance'] ?? 0 }}%</span>
                                            </div>
                                            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-amber-500 rounded-full" style="width: {{ $breakdown['performance'] ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- AI Analysis --}}
                                <div class="mt-4 p-3.5 bg-slate-50 border border-slate-100 rounded-xl">
                                    <p class="text-[12.5px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5 mb-1.5">
                                        <i class="ri-ai-generate text-[#0f766e]"></i> Analisis AI Groq
                                    </p>
                                    <p class="text-[13px] text-slate-600 leading-relaxed font-medium">{{ $analysis }}</p>
                                </div>
                            </div>

                            {{-- RIGHT: Action Buttons --}}
                            <div class="flex flex-col gap-2 justify-center lg:w-48">
                                <a href="{{ route('client.talents.show', $f->id) }}" class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-700 bg-white font-bold text-[12.5px] text-center hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
                                    Lihat Profil Lengkap
                                </a>
                                
                                {{-- Message / Negotiation Action --}}
                                <a href="{{ route('client.messages.index') }}" class="w-full py-2.5 rounded-xl bg-slate-900 text-white font-bold text-[12.5px] text-center hover:bg-black transition-all shadow-sm flex items-center justify-center gap-1.5">
                                    <i class="ri-message-3-line"></i> Kirim Pesan / Nego
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="py-16 px-6 text-center bg-white border border-slate-200 rounded-[22px] shadow-sm">
            <div class="w-20 h-20 bg-teal-50 text-[#0f766e] rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i class="ri-ai-generate text-4xl"></i>
            </div>
            <h3 class="font-extrabold text-slate-900 text-lg">Siap Mencari dengan AI?</h3>
            <p class="text-slate-500 max-w-md mx-auto mt-2 text-sm">Pilih salah satu Lowongan Kerja aktif Anda di panel sebelah kiri atau masukkan kueri kustom pencarian di panel sebelah kanan.</p>
        </div>
    @endif
</div>
@endsection
