@extends('layouts.dashboard')
@section('title', 'Buat Review | Digitalance')

@section('content')
    <section class="animate-fadeUp max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('client.orders.show', $order->id) }}"
                class="text-slate-500 font-bold text-[13px] hover:text-slate-900 inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Kembali ke Order
            </a>
            <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Buat Review</h1>
            <p class="text-slate-500 mt-1">Berikan penilaian untuk {{ $order->service->title ?? 'service ini' }}.</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-[18px] p-6">
            <div class="mb-6 rounded-[16px] border border-slate-200 bg-slate-50 p-4">
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Order</p>
                <p class="font-extrabold text-slate-900">#{{ $order->id }} - {{ $order->service->title ?? '-' }}</p>
                <p class="text-slate-500 text-[13px] mt-1">Freelancer:
                    {{ optional($order->service->freelancer->skomda_student)->name ?? 'Freelancer' }}</p>
            </div>

            <form method="POST" action="{{ route('client.reviews.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Rating</label>
                    <select name="rating" required
                        class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all focus:border-[#0f766e] focus:bg-white">
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}">{{ $i }} Star</option>
                        @endfor
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Komentar</label>
                    <textarea name="comment" rows="5"
                        class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all focus:border-[#0f766e] focus:bg-white"
                        placeholder="Tulis pengalamanmu bekerja dengan freelancer ini..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('client.orders.show', $order->id) }}"
                        class="px-5 py-2.5 rounded-[12px] border border-slate-200 text-slate-600 font-bold text-[13px] hover:bg-slate-50 transition-all">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all shadow-sm">
                        Kirim Review
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection