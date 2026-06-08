@extends('layouts.dashboard')
@section('title', 'Result Detail | Digitalance')

@section('content')
<div class="animate-fadeUp max-w-3xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('freelancer.results.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-semibold text-sm">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-[24px] p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 pb-6 border-b border-slate-100 gap-4">
            <div>
                <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900 mb-1">Detail Hasil Pekerjaan</h1>
                <p class="text-slate-500 text-[13px]">Order #{{ $result->order_id }} - {{ $result->order->service->title ?? '-' }}</p>
            </div>
            <div class="flex gap-2 items-center flex-wrap">
                <x-ui.status-badge :status="$result->order->status ?? '-'" class="px-3 py-1 rounded-lg text-[11px] uppercase" />
            </div>
        </div>

        @php
            $hasNewerResult = $result->order->results->where('id', '!=', $result->id)->count() > 0;
            $isLatestResult = $result->order->results->sortByDesc('created_at')->first()->id === $result->id;
        @endphp

        @if($isLatestResult && in_array($result->order->status, ['In Progress', 'Revision']))
        <div class="mb-6 p-4 rounded-xl bg-teal-50 border border-teal-100 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#0f766e] text-white flex items-center justify-center flex-shrink-0 text-sm">
                <i class="ri-information-line"></i>
            </div>
            <div>
                <p class="font-bold text-teal-800 text-sm">Menunggu Respon Klien</p>
                <p class="text-teal-700 text-xs mt-0.5">Hasil ini masih menunggu klien untuk menerima atau meminta revisi.</p>
            </div>
        </div>
        @elseif($result->order->status == 'Completed')
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 text-sm">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div>
                <p class="font-bold text-emerald-800 text-sm">Hasil Diterima</p>
                <p class="text-emerald-700 text-xs mt-0.5">Klien telah menerima hasil pekerjaan ini.</p>
            </div>
        </div>
        @elseif($result->order->status == 'Revision')
        <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center flex-shrink-0 text-sm">
                <i class="ri-refresh-line"></i>
            </div>
            <div>
                <p class="font-bold text-amber-800 text-sm">Revisi Diminta</p>
                <p class="text-amber-700 text-xs mt-0.5">Klien telah meminta revisi pada hasil ini.</p>
            </div>
        </div>
        @endif

        <div class="mb-8">
            <h3 class="font-bold text-slate-900 mb-3 text-[15px]">Pesan / Pesan Klien</h3>
            <div class="bg-slate-50 rounded-[16px] p-5 border border-slate-100">
                <p class="text-slate-700 text-[14px] leading-relaxed whitespace-pre-wrap">{{ $result->version ?: ($result->note ?: 'Tidak ada versi atau instruksi.') }}</p>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="font-bold text-slate-900 mb-3 text-[15px]">File / Attachment</h3>
            @if($result->file_url)
                <div class="flex items-center justify-between p-4 bg-teal-50 border border-teal-100 rounded-[16px]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-teal-100 text-[#0f766e] flex items-center justify-center text-xl">
                            <i class="{{ $result->fileIcon() }}"></i>
                        </div>
                        <div>
                            <p class="text-[13px] font-bold text-slate-900">{{ $result->fileLabel() }}</p>
                            <p class="text-[11px] text-slate-500">{{ $result->isExternalLink() ? 'Link eksternal' : 'File tersimpan' }}</p>
                        </div>
                    </div>
                    <a href="{{ $result->downloadUrl() }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2 bg-white text-[#0f766e] border border-teal-200 rounded-lg font-bold text-[12px] hover:bg-[#0f766e] hover:text-white transition-all shadow-sm">
                        {{ $result->fileActionLabel() }}
                    </a>
                </div>
            @else
                <div class="p-4 bg-slate-50 border border-slate-100 rounded-[16px] text-center text-slate-500 text-[13px]">
                    Tidak ada file yang diunggah.
                </div>
            @endif
        </div>

        <!-- Update Form -->
        <div class="pt-6 border-t border-slate-100">
            <h3 class="font-bold text-slate-900 mb-4 text-[15px]">Edit Catatan/Pesan</h3>
            <form action="{{ route('freelancer.results.update', $result->id) }}" method="POST">
                @csrf @method('PUT')
                <textarea name="note" rows="4" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[14px] focus:border-[#0f766e] focus:ring-2 focus:ring-[#0f766e]/20 outline-none mb-4 resize-none" placeholder="Masukkan catatan baru jika perlu...">{{ $result->note }}</textarea>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13.5px] hover:bg-[#0d6b63] transition-all shadow-teal-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
