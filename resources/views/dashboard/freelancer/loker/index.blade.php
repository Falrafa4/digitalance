@extends('layouts.dashboard')
@section('title', 'Lowongan Kerja | Digitalance')

@section('content')
<div class="content-scroll flex-1 px-8 py-7 overflow-y-auto">
    <div class="mb-8 animate-fadeUp">
        <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Lowongan Kerja</h1>
        <p class="text-slate-500 text-[0.95rem] mt-1">Temukan project yang sesuai kemampuanmu.</p>
    </div>

    <form method="GET" class="bg-white border border-slate-200 rounded-[18px] p-4 mb-6 flex flex-wrap gap-3 items-end animate-fadeUp">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari lowongan..."
                class="w-full py-[9px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13px] outline-none transition-all focus:border-[#0f766e] focus:bg-white" />
        </div>
        <div class="min-w-[150px]">
            <select name="category"
                class="w-full py-[9px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13px] outline-none transition-all focus:border-[#0f766e] focus:bg-white">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @if(request('category') == $cat->id) selected @endif>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit"
            class="px-5 py-2.5 rounded-[11px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all">
            <i class="ri-search-line mr-1"></i> Cari
        </button>
        <a href="{{ route('freelancer.loker.index') }}"
            class="px-4 py-2.5 rounded-[11px] border border-slate-200 text-slate-500 font-bold text-[13px] hover:bg-slate-50 transition-all">
            Reset
        </a>
    </form>

    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('freelancer.loker.my-applications') }}"
            class="text-[13px] font-bold text-indigo-600 hover:text-indigo-700 transition-all">
            <i class="ri-file-list-3-line mr-1"></i> Lamaran Saya
        </a>
        <span class="text-[12px] text-slate-400 font-semibold">{{ $lokkers->count() }} lowongan tersedia</span>
    </div>

    @if($lokkers->isEmpty())
        <div class="text-center py-16 bg-white border-2 border-dashed border-slate-200 rounded-[20px] animate-fadeUp">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mx-auto mb-4">
                <i class="ri-briefcase-2-line"></i>
            </div>
            <h3 class="font-display text-[1.15rem] font-bold text-slate-700 mb-1">Tidak Ada Lowongan</h3>
            <p class="text-[13px] text-slate-400">Coba ubah kata kunci atau filter lainnya.</p>
        </div>
    @else
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 pb-8">
            @foreach($lokkers as $loker)
                @php
                    $statusClass = $loker->status === 'Open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500';
                @endphp
                <div class="bg-white border border-slate-200 rounded-[18px] p-6 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $statusClass }}">
                                    {{ $loker->status }}
                                </span>
                                @if($loker->category)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-600 uppercase tracking-wider">
                                        {{ $loker->category->name }}
                                    </span>
                                @endif
                                @if($loker->hasApplied)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-700 uppercase tracking-wider">
                                        {{ $loker->myApplication->status === 'Approved' ? 'Diterima' : ($loker->myApplication->status === 'Rejected' ? 'Ditolak' : 'Menunggu') }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-extrabold text-[15px] text-slate-900 leading-tight">{{ $loker->title }}</h3>
                        </div>
                    </div>

                    <p class="text-[13px] text-slate-500 leading-relaxed line-clamp-3 mb-4">{{ $loker->description }}</p>

                    <div class="flex flex-wrap gap-4 mb-4 text-[12px] text-slate-500">
                        @if($loker->budget_min || $loker->budget_max)
                            <span class="flex items-center gap-1 font-bold">
                                <i class="ri-money-rupee-circle-line text-[#0f766e]"></i>
                                @if($loker->budget_min && $loker->budget_max)
                                    Rp {{ number_format((float)$loker->budget_min, 0, ',', '.') }} - {{ number_format((float)$loker->budget_max, 0, ',', '.') }}
                                @elseif($loker->budget_min)
                                    Min Rp {{ number_format((float)$loker->budget_min, 0, ',', '.') }}
                                @else
                                    Maks Rp {{ number_format((float)$loker->budget_max, 0, ',', '.') }}
                                @endif
                            </span>
                        @endif
                        @if($loker->deadline)
                            <span class="flex items-center gap-1 font-semibold">
                                <i class="ri-calendar-line text-slate-400"></i>
                                {{ \Carbon\Carbon::parse($loker->deadline)->format('d M Y') }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1 font-semibold">
                            <i class="ri-user-line text-slate-400"></i>
                            {{ $loker->client->name ?? 'Client' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <span class="text-[11px] text-slate-400 font-semibold">
                            {{ $loker->created_at->diffForHumans() }}
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('freelancer.loker.show', $loker->id) }}"
                                class="px-4 py-2 rounded-[11px] border border-slate-200 text-slate-600 font-bold text-[12px] hover:bg-slate-50 transition-all">
                                Detail
                            </a>
                            @if(!$loker->hasApplied)
                                <button type="button" onclick="openApplyModal({{ $loker->id }})"
                                    class="px-4 py-2 rounded-[11px] bg-[#0f766e] text-white font-bold text-[12px] hover:bg-[#0a5e58] transition-all">
                                    Lamar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@foreach($lokkers as $loker)
    @if(!$loker->hasApplied)
        <div id="apply-modal-{{ $loker->id }}"
            class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-[20px] p-6 w-full max-w-lg animate-fadeUp">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-display text-[1.2rem] font-extrabold text-slate-900">Lamar: {{ $loker->title }}</h3>
                    <button onclick="closeApplyModal({{ $loker->id }})"
                        class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-200 transition-all">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                <form action="{{ route('freelancer.loker.apply', $loker->id) }}" method="POST">
                    @csrf
                    <div class="flex flex-col gap-1.5 mb-4">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Proposal *</label>
                        <textarea name="proposal" rows="5" required
                            placeholder="Jelaskan why kamu cocok untuk project ini, pengalaman serupa, dan approach yang akan kamu gunakan."
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all focus:border-[#0f766e] focus:bg-white resize-none @error('proposal') border-red-400 @enderror"></textarea>
                        @error('proposal')<p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1.5 mb-5">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Harga yang Ditawarkan (Rp)</label>
                        <input type="number" name="proposed_price" min="1000"
                            placeholder="Opsional — kosongkan jika perlu diskusi"
                            class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all focus:border-[#0f766e] focus:bg-white" />
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeApplyModal({{ $loker->id }})"
                            class="px-5 py-2.5 rounded-[12px] border border-slate-200 text-slate-600 font-bold text-[13px] hover:bg-slate-50 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all shadow-sm">
                            Kirim Lamaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endforeach

@push('scripts')
<script>
function openApplyModal(id) {
    document.getElementById('apply-modal-' + id).classList.remove('hidden');
    document.getElementById('apply-modal-' + id).classList.add('flex');
}
function closeApplyModal(id) {
    document.getElementById('apply-modal-' + id).classList.add('hidden');
    document.getElementById('apply-modal-' + id).classList.remove('flex');
}
document.querySelectorAll('[id^="apply-modal-"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeApplyModal(this.id.replace('apply-modal-', ''));
    });
});
</script>
@endpush
@endsection
