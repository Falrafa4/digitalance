@extends('layouts.dashboard')
@section('title', 'Lamaran Saya | Digitalance')

@section('content')
<div class="content-scroll flex-1 px-8 py-7 overflow-y-auto">
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Lamaran Saya</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Riwayat lamaran lowongan kerja.</p>
        </div>
        <a href="{{ route('freelancer.loker.index') }}"
            class="px-5 py-2.5 rounded-[12px] border border-slate-200 text-slate-600 font-bold text-[13px] hover:bg-slate-50 transition-all flex items-center gap-2">
            <i class="ri-add-line"></i> Lihat Lowongan
        </a>
    </div>

    @if($applications->isEmpty())
        <div class="text-center py-20 px-5 bg-white border-2 border-dashed border-slate-200 rounded-[20px] animate-fadeUp">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mx-auto mb-4">
                <i class="ri-file-list-3-line"></i>
            </div>
            <h3 class="font-display text-[1.15rem] font-bold text-slate-700 mb-1">Belum Ada Lamaran</h3>
            <p class="text-[13px] text-slate-400 mb-5">Lamar lowongan kerja yang sesuai kemampuanmu.</p>
            <a href="{{ route('freelancer.loker.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all">
                Lihat Lowongan
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 pb-8">
            @foreach($applications as $app)
                @php
                    $statusClass = match($app->status) {
                        'Approved' => 'bg-emerald-100 text-emerald-700',
                        'Rejected' => 'bg-red-100 text-red-600',
                        default => 'bg-yellow-100 text-yellow-700',
                    };
                @endphp
                <div class="bg-white border border-slate-200 rounded-[18px] p-6 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $statusClass }}">
                                    {{ $app->status === 'Approved' ? 'Diterima' : ($app->status === 'Rejected' ? 'Ditolak' : 'Menunggu') }}
                                </span>
                                @if($app->loker->category)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-600 uppercase tracking-wider">
                                        {{ $app->loker->category->name }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-extrabold text-[15px] text-slate-900 leading-tight">{{ $app->loker->title }}</h3>
                        </div>
                    </div>

                    <p class="text-[13px] text-slate-500 leading-relaxed line-clamp-2 mb-3">{{ $app->proposal }}</p>

                    <div class="flex flex-wrap gap-3 mb-4 text-[12px] text-slate-500">
                        @if($app->proposed_price)
                            <span class="flex items-center gap-1 font-bold text-[#0f766e]">
                                <i class="ri-money-rupee-circle-line"></i>
                                Rp {{ number_format((float)$app->proposed_price, 0, ',', '.') }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1 font-semibold">
                            <i class="ri-user-line text-slate-400"></i>
                            {{ $app->loker->client->name ?? 'Client' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <span class="text-[11px] text-slate-400 font-semibold">
                            {{ $app->created_at->diffForHumans() }}
                        </span>
                        <a href="{{ route('freelancer.loker.show', $app->loker_id) }}"
                            class="px-4 py-2 rounded-[11px] border border-slate-200 text-slate-600 font-bold text-[12px] hover:bg-slate-50 transition-all">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
