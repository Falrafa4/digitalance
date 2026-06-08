@extends('layouts.dashboard')
@section('title', 'Offer Detail | Digitalance')

@section('styles')
    <style>
        [x-cloak] {
            display: none !important
        }
    </style>
@endsection

@section('content')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <div class="animate-fadeUp max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('client.offers.index') }}"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-semibold text-sm">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-[24px] p-8 shadow-sm">
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-100">
                <div>
                    <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900 mb-1">Detail Penawaran</h1>
                    <p class="text-slate-500 text-[13px]">Order #{{ $offer->order_id }} -
                        {{ $offer->order->service->title ?? '-' }}</p>
                </div>
                <x-ui.status-badge :status="$offer->status ?? '-'" class="px-4 py-1.5 rounded-xl text-[12.5px]" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                <div class="bg-slate-50 rounded-[16px] p-5">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Freelancer</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-[#0f766e]">
                            <i class="ri-user-smile-line text-lg"></i>
                        </div>
                        <p class="font-bold text-slate-900">
                            {{ $offer->order->service->freelancer->user->name ?? $offer->order->service->freelancer->name ?? 'Freelancer' }}
                        </p>
                    </div>
                </div>
                <div class="bg-emerald-50 rounded-[16px] p-5">
                    <p class="text-[11px] font-bold text-emerald-600/70 uppercase tracking-wider mb-1">Harga Ditawarkan</p>
                    <p class="text-[24px] font-display font-extrabold text-emerald-700 leading-none mt-2">
                        Rp{{ number_format($offer->offered_price, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="font-bold text-slate-900 mb-3 text-[15px]">Pesan dari Freelancer</h3>
                <div class="bg-slate-50 rounded-[16px] p-5 border border-slate-100">
                    <p class="text-slate-700 text-[14px] leading-relaxed whitespace-pre-wrap">
                        {{ $offer->message ?: 'Tidak ada pesan yang disertakan.' }}</p>
                </div>
            </div>

            @if($offer->status === 'Sent')
                <div x-data="{ showNegotiation: false }" class="pt-6 border-t border-slate-100">
                    <div class="flex gap-4">
                        <button @click="showNegotiation = true"
                            class="flex-1 py-3.5 rounded-[14px] bg-amber-50 border border-amber-200 text-amber-700 font-bold text-[14px] hover:bg-amber-100 hover:border-amber-300 transition-all flex items-center justify-center gap-2">
                            <i class="ri-chat-history-line"></i>
                            Ajukan Negosiasi
                        </button>
                        <form action="{{ route('client.offers.accept', $offer->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" onclick="return confirm('Terima penawaran ini?')"
                                class="w-full py-3.5 rounded-[14px] bg-[#0f766e] text-white font-bold text-[14px] shadow-teal-sm hover:bg-[#0a5e58] hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                <i class="ri-check-line"></i>
                                Terima Penawaran
                            </button>
                        </form>
                    </div>

                    <!-- Modal Negosiasi -->
                    <div x-show="showNegotiation" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                        style="display: none;">
                        <div @click="showNegotiation = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
                        <div x-show="showNegotiation" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            class="relative w-full max-w-lg bg-white rounded-[24px] shadow-xl p-6 sm:p-8">
                            <button @click="showNegotiation = false"
                                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                                <i class="ri-close-line"></i>
                            </button>

                            <div class="mb-6">
                                <h2 class="font-display text-xl font-bold text-slate-900 mb-1">Ajukan Negosiasi</h2>
                                <p class="text-sm text-slate-500">Kirimkan предложение harga baru ke freelancer</p>
                            </div>

                            <form action="{{ route('client.negotiations.store', $offer->id) }}" method="POST" class="space-y-5">
                                @csrf

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Alasan Negosiasi <span
                                            class="text-red-500">*</span></label>
                                    <textarea name="reason" rows="3" required
                                        class="w-full px-4 py-3 rounded-[12px] border border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 outline-none transition-all text-sm"
                                        placeholder="Contoh: Budget terbatas, freelancer lain lebih murah..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Usulan Harga Baru <span
                                            class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-semibold">Rp</span>
                                        <input type="text" name="new_price" required data-rupiah-input inputmode="numeric"
                                            class="w-full pl-10 pr-4 py-3 rounded-[12px] border border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 outline-none transition-all text-sm font-semibold text-slate-700"
                                            placeholder="1.000.000" value="{{ old('new_price', $offer->offered_price) }}">
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1.5">Harga awal:
                                        Rp{{ number_format($offer->offered_price, 0, ',', '.') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Tambahan</label>
                                    <textarea name="description" rows="2"
                                        class="w-full px-4 py-3 rounded-[12px] border border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 outline-none transition-all text-sm"
                                        placeholder=" detail tambahan (opsional)..."></textarea>
                                </div>

                                <div class="flex gap-3 pt-2">
                                    <button type="button" @click="showNegotiation = false"
                                        class="flex-1 py-3 rounded-[12px] bg-slate-100 text-slate-600 font-bold text-sm hover:bg-slate-200 transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="flex-1 py-3 rounded-[12px] bg-amber-500 text-white font-bold text-sm hover:bg-amber-600 shadow-lg shadow-amber-200 transition-all">
                                        Kirim Negosiasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection