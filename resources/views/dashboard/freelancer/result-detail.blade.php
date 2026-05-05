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
            <div class="flex gap-2">
                <form action="{{ route('freelancer.results.destroy', $result->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus result ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl font-bold text-sm hover:bg-red-100 transition-colors">
                        <i class="ri-delete-bin-line mr-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="font-bold text-slate-900 mb-3 text-[15px]">Pesan / Pesan Klien</h3>
            <div class="bg-slate-50 rounded-[16px] p-5 border border-slate-100">
                <p class="text-slate-700 text-[14px] leading-relaxed whitespace-pre-wrap">{{ $result->message ?: ($result->note ?: 'Tidak ada pesan atau instruksi.') }}</p>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="font-bold text-slate-900 mb-3 text-[15px]">File / Attachment</h3>
            @if($result->file_url)
                <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-100 rounded-[16px]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                            <i class="ri-file-zip-line"></i>
                        </div>
                        <div>
                            <p class="text-[13px] font-bold text-slate-900">File Terlampir</p>
                            <p class="text-[11px] text-slate-500">Tersedia untuk diunduh</p>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $result->file_url) }}" target="_blank" class="px-4 py-2 bg-white text-blue-600 border border-blue-200 rounded-lg font-bold text-[12px] hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                        Unduh
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
                <textarea name="message" rows="4" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[14px] focus:border-[#0f766e] focus:ring-2 focus:ring-[#0f766e]/20 outline-none mb-4 resize-none" placeholder="Masukkan pesan baru jika perlu...">{{ $result->message }}</textarea>
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
