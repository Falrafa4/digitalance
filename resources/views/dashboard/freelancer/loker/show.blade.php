@extends('layouts.dashboard')
@section('title', $loker->title . ' | Digitalance')

@section('content')
    <div class="content-scroll flex-1 px-8 py-7 overflow-y-auto max-w-3xl">
        <div class="mb-8 animate-fadeUp">
            <a href="{{ route('freelancer.loker.index') }}"
                class="inline-flex items-center gap-1 text-slate-500 font-bold text-[13px] hover:text-slate-900 mb-3">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $loker->status === 'Open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $loker->status }}
                        </span>
                        @if($loker->category)
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-600 uppercase tracking-wider">
                                {{ $loker->category->name }}
                            </span>
                        @endif
                    </div>
                    <h1 class="font-display text-[2rem] font-extrabold text-slate-900 leading-tight">{{ $loker->title }}
                    </h1>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-[18px] p-6 mb-5 animate-fadeUp">
            <div class="flex flex-wrap gap-4 mb-4 text-[13px] text-slate-600">
                <span class="flex items-center gap-1.5 font-bold">
                    <i class="ri-user-line text-slate-400"></i>
                    {{ $loker->client->name ?? 'Klien' }}
                </span>
                @if($loker->budget_min || $loker->budget_max)
                    <span class="flex items-center gap-1.5 font-bold text-[#0f766e]">
                        <i class="ri-money-rupee-circle-line"></i>
                        @if($loker->budget_min && $loker->budget_max)
                            Rp {{ number_format((float) $loker->budget_min, 0, ',', '.') }} -
                            {{ number_format((float) $loker->budget_max, 0, ',', '.') }}
                        @elseif($loker->budget_min)
                            Min Rp {{ number_format((float) $loker->budget_min, 0, ',', '.') }}
                        @else
                            Maks Rp {{ number_format((float) $loker->budget_max, 0, ',', '.') }}
                        @endif
                    </span>
                @endif
                @if($loker->deadline)
                    <span class="flex items-center gap-1.5 font-semibold">
                        <i class="ri-calendar-line text-slate-400"></i>
                        Batas: {{ \Carbon\Carbon::parse($loker->deadline)->format('d M Y') }}
                    </span>
                @endif
                <span class="flex items-center gap-1.5 font-semibold">
                    <i class="ri-time-line text-slate-400"></i>
                    {{ $loker->created_at->diffForHumans() }}
                </span>
            </div>

            <div class="border-t border-slate-100 pt-4">
                <h3 class="text-[11px] font-extrabold text-slate-500 uppercase tracking-[.1em] mb-2">Deskripsi</h3>
                <p class="text-[13.5px] text-slate-700 leading-relaxed whitespace-pre-line">{{ $loker->description }}</p>
            </div>
        </div>

        @if($loker->hasApplied)
            <div class="bg-indigo-50 border border-indigo-200 rounded-[18px] p-5 mb-5 text-center animate-fadeUp">
                <p class="text-[13px] font-bold text-indigo-700">
                    Kamu sudah melamar lowongan ini.
                    <span class="text-[12px] font-semibold text-indigo-500 ml-1">
                        Status:
                        {{ $loker->myApplication->status === 'Approved' ? 'Diterima' : ($loker->myApplication->status === 'Rejected' ? 'Ditolak' : 'Menunggu persetujuan') }}
                    </span>
                </p>
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-[18px] p-6 animate-fadeUp">
                <h3 class="font-display text-[1.1rem] font-extrabold text-slate-900 mb-4">Kirim Lamaran</h3>
                <form action="{{ route('freelancer.loker.apply', $loker->id) }}" method="POST">
                    @csrf
                    <div class="flex flex-col gap-1.5 mb-4">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Proposal *</label>
                        <textarea name="proposal" rows="5" required
                            placeholder="Jelaskan kenapa kamu cocok untuk proyek ini, pengalaman serupa, dan pendekatan yang akan kamu gunakan."
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all focus:border-[#0f766e] focus:bg-white resize-none @error('proposal') border-red-400 @enderror">{{ old('proposal') }}</textarea>
                        @error('proposal')<p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1.5 mb-5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Harga yang Ditawarkan
                            (Rp)</label>
                        <input type="number" name="proposed_price" value="{{ old('proposed_price') }}" min="1000"
                            placeholder="Opsional"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all focus:border-[#0f766e] focus:bg-white" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('freelancer.loker.index') }}"
                            class="px-5 py-2.5 rounded-[12px] border border-slate-200 text-slate-600 font-bold text-[13px] hover:bg-slate-50 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all shadow-sm">
                            <i class="ri-send-plane-fill mr-1"></i> Kirim Lamaran
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection