@extends('layouts.dashboard')
@section('title', 'Results | Digitalance')

@section('content')
<div class="animate-fadeUp flex-1 px-8 py-7 overflow-y-auto">
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900 leading-tight">Project Results</h1>
            <p class="text-slate-500 mt-1 text-[0.95rem]">Pantau dan kirimkan hasil pekerjaan akhir ke klien.</p>
        </div>
    </div>

    @if($results->isEmpty())
        <div class="text-center py-16 px-5 bg-white border-2 border-dashed border-slate-200 rounded-[20px]">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mx-auto mb-4">
                <i class="ri-file-check-line"></i>
            </div>
            <h3 class="font-display text-[1.15rem] font-bold text-slate-700 mb-1">Belum Ada Hasil Kerja</h3>
            <p class="text-[13px] text-slate-400">Kamu belum mengunggah hasil pekerjaan (result) untuk order apapun.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 pb-6">
            @foreach($results as $res)
                <div class="bg-white border border-slate-200 rounded-[20px] p-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    <div class="flex items-start justify-between mb-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Order #{{ $res->order_id }}</span>
                            <h3 class="font-bold text-slate-900 line-clamp-1" title="{{ $res->order->service->title ?? '-' }}">{{ $res->order->service->title ?? 'Service' }}</h3>
                        </div>
                    </div>

                    <div class="flex-1 mb-4">
                        <p class="text-[12px] font-bold text-slate-500 uppercase mb-2">Pesan/Catatan</p>
                        <div class="bg-slate-50 rounded-xl p-3 text-[13px] text-slate-700 leading-relaxed border border-slate-100">
                            {{ $res->message ?: ($res->note ?: 'Tidak ada pesan / catatan.') }}
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100 mt-auto">
                        @if($res->file_url)
                            <a href="{{ asset('storage/' . $res->file_url) }}" target="_blank" class="flex-1 py-2.5 rounded-xl border border-blue-200 bg-blue-50 text-blue-700 font-bold text-[12.5px] text-center hover:bg-blue-100 transition-colors">
                                <i class="ri-download-cloud-2-line mr-1"></i> Unduh File
                            </a>
                        @else
                            <div class="flex-1 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-400 font-bold text-[12.5px] text-center cursor-not-allowed">
                                Tidak ada file
                            </div>
                        @endif
                        <a href="{{ route('freelancer.results.show', $res->id) }}" class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-[#0f766e] hover:text-white transition-colors">
                            <i class="ri-eye-line text-lg"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
