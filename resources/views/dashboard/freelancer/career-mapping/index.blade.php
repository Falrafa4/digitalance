@extends('layouts.dashboard')
@section('title', 'Pemetaan Karir AI | Digitalance')

@section('content')
<div class="animate-fadeUp">
    {{-- Header --}}
    <section class="mb-8">
        <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Pemetaan Karir AI</h1>
        <p class="text-slate-500 mt-1">Analisis peta jalan karir, sasaran milestone, dan pengembangan keahlian Anda berbasis kecerdasan buatan.</p>
    </section>

    {{-- Alert Status --}}
    <div class="mb-8">
        @if($freelancer->career_track_status === 'Approved')
            <div class="p-4.5 rounded-[22px] bg-emerald-50 border border-emerald-100 flex items-center gap-4 shadow-sm">
                <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0">
                    <i class="ri-verified-badge-line text-xl"></i>
                </div>
                <div>
                    <h4 class="text-emerald-950 font-extrabold text-[14.5px]">Akun Terverifikasi & Jalur Karir Terkunci</h4>
                    <p class="text-emerald-700/80 text-[13px] font-medium mt-0.5">Spesialisasi Anda saat ini adalah: <strong class="text-emerald-950">{{ $freelancer->career_track }}</strong>.</p>
                </div>
            </div>
        @elseif($freelancer->career_track_status === 'Pending')
            <div class="p-4.5 rounded-[22px] bg-amber-50 border border-amber-100 flex items-center gap-4 shadow-sm animate-pulse">
                <div class="w-11 h-11 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="ri-time-line text-xl"></i>
                </div>
                <div>
                    <h4 class="text-amber-950 font-extrabold text-[14.5px]">Menunggu Verifikasi Admin</h4>
                    <p class="text-amber-700/80 text-[13px] font-medium mt-0.5">Pengajuan spesialisasi jalur karir (<strong class="text-amber-950">{{ $freelancer->career_track }}</strong>) sedang ditinjau oleh administrator.</p>
                </div>
            </div>
        @elseif($freelancer->career_track_status === 'Rejected')
            <div class="p-4.5 rounded-[22px] bg-red-50 border border-red-100 flex items-center gap-4 shadow-sm">
                <div class="w-11 h-11 rounded-xl bg-red-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="ri-error-warning-line text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-red-950 font-extrabold text-[14.5px]">Verifikasi Ditolak / Dibatalkan</h4>
                    <p class="text-red-700/80 text-[13px] font-medium mt-0.5">Alasan: <span class="italic font-bold">{{ $freelancer->reject_reason ?? 'Tidak ada alasan spesifik.' }}</span></p>
                    <p class="text-red-700/80 text-[12px] font-medium mt-1">Silakan isi form di bawah untuk mengajukan ulang pemetaan karir Anda.</p>
                </div>
            </div>
        @else
            <div class="p-4.5 rounded-[22px] bg-indigo-50 border border-indigo-100 flex items-center gap-4 shadow-sm">
                <div class="w-11 h-11 rounded-xl bg-indigo-600 text-white flex items-center justify-center flex-shrink-0">
                    <i class="ri-information-line text-xl"></i>
                </div>
                <div>
                    <h4 class="text-indigo-950 font-extrabold text-[14.5px]">Langkah Wajib: Ajukan Verifikasi Akun</h4>
                    <p class="text-indigo-700/80 text-[13px] font-medium mt-0.5">Agar dapat menerima order dan tampil di pencarian klien, Anda wajib menyetujui pemetaan karir dan mengajukan verifikasi ke admin.</p>
                </div>
            </div>
        @endif
    </div>

    @if(!$careerMap)
        {{-- ===== STEP 1: FORM ===== --}}
        <div class="max-w-2xl mx-auto">
            <div class="bg-white border border-slate-200 rounded-[22px] p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-teal-50 border border-teal-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <i class="ri-edit-line text-2xl text-[#0f766e]"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900">Isi Preferensi Karir Anda</h2>
                        <p class="text-slate-500 text-[13px] font-medium mt-0.5">Bantu AI memahami minat dan keahlian Anda untuk rekomendasi yang akurat.</p>
                    </div>
                </div>

                <form action="{{ route('freelancer.career-mapping.analyze') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Bidang Fokus <span class="text-red-400">*</span></label>
                        <input type="text" name="focus_field" required value="{{ old('focus_field') }}"
                               placeholder="Misal: Pengembangan Web, Jaringan Komputer, IoT, Desain Grafis"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-[13px] font-semibold text-slate-800 placeholder-slate-400 focus:border-[#0f766e] focus:ring-2 focus:ring-[#0f766e]/10 transition-all outline-none">
                        @error('focus_field') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Keahlian Utama <span class="text-red-400">*</span></label>
                        <textarea name="top_skills" required rows="3"
                                  placeholder="Sebutkan keahlian teknis yang Anda kuasai (pisahkan dengan koma).&#10;Contoh: Laravel, Tailwind CSS, MikroTik, Figma, Python, React.js"
                                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-[13px] font-semibold text-slate-800 placeholder-slate-400 focus:border-[#0f766e] focus:ring-2 focus:ring-[#0f766e]/10 transition-all outline-none resize-y">{{ old('top_skills') }}</textarea>
                        @error('top_skills') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Area Ketertarikan Karir <span class="text-red-400">*</span></label>
                        <textarea name="interest_area" required rows="3"
                                  placeholder="Jelaskan bidang karir yang paling Anda minati ke depannya.&#10;Contoh: Saya ingin menjadi full-stack developer spesialisasi aplikasi web perusahaan, atau Saya tertarik di bidang network security"
                                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-[13px] font-semibold text-slate-800 placeholder-slate-400 focus:border-[#0f766e] focus:ring-2 focus:ring-[#0f766e]/10 transition-all outline-none resize-y">{{ old('interest_area') }}</textarea>
                        @error('interest_area') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full py-3.5 rounded-xl bg-[#0f766e] text-white font-extrabold text-[13.5px] hover:bg-[#0a5e58] transition-all shadow-md flex items-center justify-center gap-2">
                            <i class="ri-sparkling-2-line text-lg"></i>
                            Analisis dengan AI
                        </button>
                        <p class="text-[11px] text-slate-400 font-medium text-center mt-2">AI akan menganalisis profil dan preferensi Anda untuk menyusun peta karir yang dipersonalisasi.</p>
                    </div>
                </form>
            </div>
        </div>
    @else
        {{-- ===== STEP 2 & 3: RESULTS + SUBMIT ===== --}}
        <div id="results"></div>
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
            {{-- LEFT COLUMN: Overview & Roadmap --}}
            <div class="xl:col-span-2 space-y-8">
                {{-- Overview Card --}}
                <div class="bg-white border border-slate-200 rounded-[22px] p-6 shadow-sm">
                    <h3 class="font-extrabold text-slate-900 text-lg mb-4 flex items-center gap-2">
                        <i class="ri-compass-3-line text-[#0f766e]"></i> Analisis Spesialisasi AI
                    </h3>
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <div class="w-24 h-24 bg-teal-50 border border-teal-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <i class="ri-award-line text-4xl text-[#0f766e]"></i>
                        </div>
                        <div class="flex-1 text-center sm:text-left">
                            <span class="text-xs font-black uppercase tracking-wider text-slate-400">Jalur Karir yang Disarankan</span>
                            <h2 class="text-2xl font-black text-slate-900 mt-0.5">{{ $careerMap['career_track'] }}</h2>
                            <div class="mt-2 flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                                <span class="px-3 py-1 bg-slate-100 text-slate-700 text-[11.5px] font-bold rounded-full uppercase tracking-wider">
                                    LEVEL: {{ $careerMap['current_level'] }}
                                </span>
                                <span class="text-slate-300 text-sm hidden sm:inline">•</span>
                                <span class="text-slate-500 font-semibold text-[12.5px]">
                                    {{ $careerMap['xp'] }} XP Terkumpul
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Learning Roadmap Card --}}
                <div class="bg-white border border-slate-200 rounded-[22px] p-6 shadow-sm">
                    <h3 class="font-extrabold text-slate-900 text-lg mb-3 flex items-center gap-2">
                        <i class="ri-road-map-line text-[#0f766e]"></i> Panduan Rencana Belajar
                    </h3>
                    <p class="text-slate-600 text-[13.5px] leading-relaxed font-medium bg-slate-50 border border-slate-100 p-4 rounded-xl">
                        {{ $careerMap['learning_roadmap'] }}
                    </p>
                </div>

                {{-- Milestones Timeline --}}
                <div class="bg-white border border-slate-200 rounded-[22px] p-6 shadow-sm">
                    <h3 class="font-extrabold text-slate-900 text-lg mb-6 flex items-center gap-2">
                        <i class="ri-milestone-line text-[#0f766e]"></i> Sasaran Karir & Milestones
                    </h3>
                    
                    <div class="relative pl-6 border-l border-slate-200 space-y-6">
                        @foreach($careerMap['milestones'] as $milestone)
                            @php
                                $isCompleted = strtolower($milestone['status']) === 'completed';
                            @endphp
                            <div class="relative">
                                <div class="absolute -left-9 top-1 w-6 h-6 rounded-full flex items-center justify-center 
                                            {{ $isCompleted ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                                    <i class="{{ $isCompleted ? 'ri-checkbox-circle-fill' : 'ri-checkbox-blank-circle-line' }} text-sm"></i>
                                </div>
                                
                                <div class="p-4 bg-white border border-slate-200 rounded-xl hover:border-[#0f766e] transition-all">
                                    <div class="flex items-center justify-between gap-3 flex-wrap">
                                        <h4 class="font-extrabold text-slate-900 text-[14px]">{{ $milestone['title'] }}</h4>
                                        <span class="px-2 py-0.5 text-[9.5px] font-black uppercase tracking-wider rounded-md
                                                     {{ $isCompleted ? 'bg-emerald-50 border border-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $milestone['status'] }}
                                        </span>
                                    </div>
                                    <p class="text-slate-500 text-xs mt-1 leading-relaxed font-medium">{{ $milestone['description'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Skill Gaps & Recommended Jobs --}}
            <div class="space-y-8">
                {{-- Skill Gaps Analysis --}}
                <div class="bg-white border border-slate-200 rounded-[22px] p-6 shadow-sm">
                    <h3 class="font-extrabold text-slate-900 text-lg mb-4 flex items-center gap-2">
                        <i class="ri-bubble-chart-line text-[#0f766e]"></i> Analisis Celah Skill (Gaps)
                    </h3>
                    <div class="space-y-4">
                        @foreach($careerMap['skill_gaps'] as $gap)
                            <div class="p-4 bg-amber-50/60 border border-amber-100 rounded-xl">
                                <span class="text-xs font-black text-amber-800 uppercase tracking-wider">{{ $gap['name'] }}</span>
                                <p class="text-slate-600 text-xs mt-1 leading-relaxed font-medium">{{ $gap['recommended_action'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Recommended Loker --}}
                <div class="bg-white border border-slate-200 rounded-[22px] p-6 shadow-sm">
                    <h3 class="font-extrabold text-slate-900 text-lg mb-4 flex items-center gap-2">
                        <i class="ri-briefcase-line text-[#0f766e]"></i> Lowongan Relevan
                    </h3>
                    <div class="space-y-3">
                        @forelse($matchingLokers as $job)
                            <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl hover:border-[#0f766e] transition-all">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 text-[13px] line-clamp-1">{{ $job->title }}</h4>
                                        <p class="text-slate-400 text-[11px] font-semibold mt-0.5">{{ $job->client->name ?? 'Klien' }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 bg-teal-50 border border-teal-100 text-[#0f766e] text-[9.5px] font-black uppercase tracking-wider rounded">
                                        {{ $job->status }}
                                    </span>
                                </div>
                                <div class="mt-3 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs font-bold text-slate-500">
                                    <span>Budget</span>
                                    <span class="text-[#0f766e]">Rp {{ number_format($job->budget_min, 0, ',', '.') }}</span>
                                </div>
                                <a href="{{ route('freelancer.loker.show', $job->id) }}" class="block text-center mt-3 py-1.5 bg-slate-900 text-white rounded-lg font-bold text-[11px] hover:bg-black transition-all">
                                    Ajukan Lamaran
                                </a>
                            </div>
                        @empty
                            <p class="text-slate-400 text-xs font-semibold text-center py-4">Belum ada lowongan yang cocok saat ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== STEP 3: VERIFICATION SUBMIT BUTTON ===== --}}
        @if(in_array($freelancer->career_track_status, ['None', 'Rejected']))
            <section class="bg-white border-2 border-dashed border-[#0f766e]/40 rounded-[22px] p-8 text-center shadow-md">
                <div class="max-w-xl mx-auto space-y-6">
                    <div class="w-16 h-16 bg-teal-50 text-[#0f766e] rounded-full flex items-center justify-center mx-auto shadow-inner">
                        <i class="ri-shield-user-line text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900">Ajukan Aktivasi & Verifikasi Akun</h3>
                        <p class="text-slate-500 text-sm mt-1.5 leading-relaxed">
                            Dengan mengajukan jalur karir <strong class="text-slate-900">"{{ $careerMap['career_track'] }}"</strong> ini, profil dan portfolio Anda akan dikirim ke admin untuk direview dan diverifikasi sebagai freelancer resmi Digitalance.
                        </p>
                    </div>

                    <form action="{{ route('freelancer.career-mapping.submit') }}" method="POST" id="verification-form" class="space-y-4">
                        @csrf
                        <input type="hidden" name="career_track" value="{{ $careerMap['career_track'] }}">

                        <div class="flex items-center justify-center gap-3 p-4 bg-slate-50 border border-slate-100 rounded-xl max-w-sm mx-auto">
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" id="approve-toggle" class="sr-only peer" onchange="toggleSubmitButton()">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0f766e]"></div>
                            </label>
                            <span class="text-xs font-bold text-slate-700">Saya menyetujui jalur karir ini</span>
                        </div>

                        <button type="submit" id="submit-verification-btn" disabled 
                                class="w-full max-w-sm py-3 rounded-xl bg-slate-400 text-white font-extrabold text-[13.5px] cursor-not-allowed transition-all shadow-md">
                            Kirim Pengajuan Verifikasi ke Admin
                        </button>
                    </form>

                    {{-- Tombol ulang analisis / ubah preferensi --}}
                    <div class="pt-2 flex items-center justify-center gap-4">
                        <form action="{{ route('freelancer.career-mapping.analyze') }}" method="POST">
                            @csrf
                            <input type="hidden" name="focus_field" value="{{ old('focus_field', session('last_focus_field', '')) }}">
                            <input type="hidden" name="top_skills" value="{{ old('top_skills', session('last_top_skills', '')) }}">
                            <input type="hidden" name="interest_area" value="{{ old('interest_area', session('last_interest_area', '')) }}">
                            <button type="submit" class="text-[#0f766e] text-xs font-bold hover:underline flex items-center gap-1">
                                <i class="ri-refresh-line"></i> Analisis Ulang
                            </button>
                        </form>
                        <span class="text-slate-300 text-xs">|</span>
                        <a href="{{ route('freelancer.career-mapping', ['reset' => 1]) }}" class="text-slate-500 text-xs font-bold hover:text-slate-700 flex items-center gap-1">
                            <i class="ri-edit-line"></i> Ubah Preferensi
                        </a>
                    </div>
                </div>
            </section>
        @elseif($freelancer->status !== 'Approved')
            <section class="bg-white border border-slate-200 rounded-[22px] p-6 text-center">
                <p class="text-slate-500 text-sm font-bold flex items-center justify-center gap-1.5">
                    <i class="ri-checkbox-circle-fill text-emerald-500 text-lg"></i>
                    Verifikasi Akun sudah selesai. Anda terdaftar sebagai spesialis di bidang <strong>"{{ $freelancer->career_track }}"</strong>.
                </p>
            </section>
        @endif
    @endif
</div>
@endsection

@section('scripts')
<script>
    function toggleSubmitButton() {
        const toggle = document.getElementById('approve-toggle');
        const btn = document.getElementById('submit-verification-btn');
        if (toggle && btn) {
            if (toggle.checked) {
                btn.disabled = false;
                btn.classList.remove('bg-slate-400', 'cursor-not-allowed');
                btn.classList.add('bg-[#0f766e]', 'hover:bg-[#0a5e58]', 'shadow-teal-sm');
            } else {
                btn.disabled = true;
                btn.classList.add('bg-slate-400', 'cursor-not-allowed');
                btn.classList.remove('bg-[#0f766e]', 'hover:bg-[#0a5e58]', 'shadow-teal-sm');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash;
        if (hash === '#results') {
            document.getElementById('results')?.scrollIntoView({ behavior: 'smooth' });
        }
    });
</script>
@endsection