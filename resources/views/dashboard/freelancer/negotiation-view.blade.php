@extends('layouts.dashboard')
@section('title', 'Negotiation Detail | Digitalance')

@section('content')
<div class="animate-fadeUp max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('freelancer.orders.show', $negotiation->order_id) }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-semibold text-sm">
            <i class="ri-arrow-left-line"></i> Kembali ke Order
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-[24px] p-8 shadow-sm">
        <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-100">
            <div>
                <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900 mb-1">Detail Negosiasi</h1>
                <p class="text-slate-500 text-[13px]">Order #{{ $negotiation->order_id }} - {{ $negotiation->order->service->title ?? '-' }}</p>
            </div>
            <span class="px-4 py-1.5 rounded-xl text-[12.5px] font-bold bg-slate-100 text-slate-600">
                NEGOTIATION
            </span>
        </div>

        <div class="mb-8">
            <h3 class="font-bold text-slate-900 mb-3 text-[15px] flex items-center gap-2">
                <i class="ri-chat-forward-line text-slate-400"></i>
                Isi Pesan Negosiasi
            </h3>
            <div class="bg-slate-50 rounded-[16px] p-5 border border-slate-100">
                <p class="text-slate-700 text-[14px] leading-relaxed whitespace-pre-wrap">{{ $negotiation->message }}</p>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100">
            <div class="flex gap-4">
                <form action="{{ route('freelancer.negotiations.reject', $negotiation->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" onclick="return confirm('Tolak negosiasi ini?')" class="w-full py-3.5 rounded-[14px] bg-white border border-red-200 text-red-600 font-bold text-[14px] hover:bg-red-50 transition-all flex items-center justify-center gap-2">
                        <i class="ri-close-circle-line"></i>
                        Tolak
                    </button>
                </form>
                <form action="{{ route('freelancer.negotiations.accept', $negotiation->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" onclick="return confirm('Terima negosiasi ini?')" class="w-full py-3.5 rounded-[14px] bg-emerald-500 text-white font-bold text-[14px] hover:bg-emerald-600 transition-all flex items-center justify-center gap-2">
                        <i class="ri-check-circle-line"></i>
                        Terima
                    </button>
                </form>
            </div>
            <p class="text-[11px] text-slate-400 mt-4 text-center">
                <i class="ri-information-line"></i> Catatan: Tombol terima/tolak di sini akan mengirimkan balasan otomatis ke klien.
            </p>
        </div>
    </div>
</div>
@endsection