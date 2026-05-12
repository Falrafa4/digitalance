@extends('layouts.dashboard')
@section('title', 'Lowongan Kerja | Digitalance')

@section('content')
<div class="content-scroll flex-1 px-8 py-7 overflow-y-auto relative overflow-hidden">
    {{-- Decorative Background Icon --}}
    <div class="absolute -right-12 -top-12 w-64 h-64 opacity-[0.03] pointer-events-none rotate-12">
        <i class="ri-briefcase-2-line text-[250px]"></i>
    </div>

    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp relative z-10">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Lowongan Kerja</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Posting lowongan dan kelola lamaran freelancer.</p>
        </div>
        <a href="{{ route('client.loker.create') }}"
            class="px-5 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all shadow-teal-sm flex items-center gap-2">
            <i class="ri-add-line"></i> Posting Lowongan
        </a>
    </div>

    @if($lokkers->isEmpty())
        <div class="text-center py-20 px-5 bg-white border-2 border-dashed border-slate-200 rounded-[20px] animate-fadeUp relative z-10">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mx-auto mb-4">
                <i class="ri-briefcase-2-line"></i>
            </div>
            <h3 class="font-display text-[1.15rem] font-bold text-slate-700 mb-1">Belum Ada Lowongan</h3>
            <p class="text-[13px] text-slate-400 mb-5">Posting lowongan kerja untuk menemukan freelancer terbaik.</p>
            <a href="{{ route('client.loker.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all">
                <i class="ri-add-line"></i> Posting Sekarang
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 pb-8">
            @foreach($lokkers as $loker)
                @php
                    $statusClass = $loker->status === 'Open'
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-slate-100 text-slate-600';
                    $appCount = $loker->applications->count();
                    $pendingCount = $loker->applications->where('status', 'Pending')->count();
                @endphp
                <div class="bg-white border border-slate-200 rounded-[18px] p-6 hover:shadow-teal-sm transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="ri-briefcase-2-line text-[50px] text-slate-100/50 -rotate-12 translate-x-4 -translate-y-4"></i>
                    </div>
                    <div class="flex items-start justify-between gap-3 mb-4 relative z-10">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $statusClass }}">
                                    {{ $loker->status }}
                                </span>
                                @if($loker->category)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-600 uppercase tracking-wider">
                                        {{ $loker->category->name }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-extrabold text-[15px] text-slate-900 leading-tight">{{ $loker->title }}</h3>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <a href="{{ route('client.loker.edit', $loker->id) }}"
                                class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-all">
                                <i class="ri-pencil-line text-[14px]"></i>
                            </a>
                            <form action="{{ route('client.loker.destroy', $loker->id) }}" method="POST"
                                onsubmit="event.preventDefault(); customConfirm('Hapus lowongan ini?').then(res => { if(res) this.submit(); });">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-400 hover:bg-red-100 hover:text-red-600 transition-all">
                                    <i class="ri-delete-bin-line text-[14px]"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="text-[13px] text-slate-500 leading-relaxed line-clamp-2 mb-4">{{ $loker->description }}</p>

                    @if($loker->budget_min || $loker->budget_max)
                        <p class="text-[12px] font-bold text-[#0f766e] mb-3">
                            Budget: @if($loker->budget_min && $loker->budget_max)
                                Rp {{ number_format((float)$loker->budget_min, 0, ',', '.') }} - Rp {{ number_format((float)$loker->budget_max, 0, ',', '.') }}
                            @elseif($loker->budget_min)
                                Min Rp {{ number_format((float)$loker->budget_min, 0, ',', '.') }}
                            @else
                                Maks Rp {{ number_format((float)$loker->budget_max, 0, ',', '.') }}
                            @endif
                        </p>
                    @endif

                    @if($appCount > 0)
                        <div class="bg-slate-50 rounded-xl p-4 mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                                    Lamaran Masuk
                                </p>
                                <span class="text-[11px] font-bold text-indigo-600">{{ $appCount }} freelancer</span>
                            </div>
                            @foreach($loker->applications->take(3) as $app)
                                <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-slate-200 flex items-center justify-center text-slate-500">
                                            <i class="ri-user-line text-[14px]"></i>
                                        </div>
                                        <div>
                                            <p class="text-[12px] font-bold text-slate-800">{{ $app->freelancer->skomda_student->name ?? 'Freelancer' }}</p>
                                            @if($app->proposed_price)
                                                <p class="text-[10px] text-slate-400">Rp {{ number_format((float) $app->proposed_price, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        @if($app->status === 'Pending')
                                            <form action="{{ route('client.loker.applications.approve', $app->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 font-bold text-[10px] hover:bg-emerald-100 transition-all">
                                                    <i class="ri-check-line"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('client.loker.applications.reject', $app->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg bg-red-50 text-red-500 font-bold text-[10px] hover:bg-red-100 transition-all">
                                                    <i class="ri-close-line"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md {{ $app->status === 'Approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                                {{ $app->status }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @if($appCount > 3)
                                <p class="text-[11px] text-center text-slate-400 mt-2 font-semibold">+{{ $appCount - 3 }} lainnya</p>
                            @endif
                        </div>
                    @else
                        <div class="bg-slate-50 rounded-xl p-4 mb-4 text-center">
                            <p class="text-[12px] text-slate-400 font-semibold">Belum ada lamaran masuk</p>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 relative z-10">
                        <div class="flex items-center gap-1 text-[11px] text-slate-400 font-semibold">
                            <i class="ri-time-line"></i>
                            {{ $loker->created_at->diffForHumans() }}
                        </div>
                        @if($loker->deadline)
                            <span class="text-[11px] text-slate-400 font-semibold">
                                Batas: {{ \Carbon\Carbon::parse($loker->deadline)->format('d M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
