@extends('layouts.dashboard')

@section('title', 'Order Detail | Digitalance')

@section('styles')
    <style>
        /* Mencegah elemen Alpine berkedip atau menutupi layar sebelum siap */
        [x-cloak] {
            display: none !important;
        }

        .animate-bounce-subtle {
            animation: bounce-subtle 2s infinite;
        }

        @keyframes bounce-subtle {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-4px);
            }
        }
    </style>
@endsection

@section('content')
    {{-- Skrip Alpine langsung di sini untuk memastikan load paling cepat --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="content-scroll flex-1 px-8 py-7 overflow-y-auto" x-data="{ 
            stage: 'decision', 
            showRejectModal: false, 
            reason: '' 
         }">

        <!-- HEADER -->
        <div class="mb-8 animate-fadeUp flex justify-between items-end gap-4 flex-wrap">
            <div>
                <div class="flex items-center gap-3 text-slate-400 text-sm mb-2">
                    <a href="{{ route('freelancer.orders.index') }}"
                        class="hover:text-emerald-600 transition-colors">Orders</a>
                    <i class="ri-arrow-right-s-line text-[10px]"></i>
                    <span class="text-slate-600 font-semibold">Order #{{ $order->id }}</span>
                </div>
                <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900 leading-tight">Detail Pesanan</h1>
                <p class="text-slate-500 text-[0.95rem] mt-1">Kelola respon dan negosiasi penawaran harga Anda.</p>
            </div>

            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-100 mb-2">
                <span
                    class="w-2 h-2 rounded-full @if($order->status == 'Pending') bg-amber-500 @else bg-emerald-500 @endif animate-pulse"></span>
                <span class="text-xs font-black uppercase tracking-widest text-slate-600">{{ $order->status }}</span>
            </div>
        </div>

        @if($order->status == 'Pending')

            <!-- FREELANCER ORDER RESPONSE SYSTEM -->
            <div class="mb-8 relative z-10">

                <!-- STAGE 1: DECISION (TOMBOL ACC/TOLAK) -->
                <div x-show="stage === 'decision'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="bg-white rounded-2xl shadow-md border border-slate-100 p-8">

                    <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="max-w-xl text-center md:text-left">
                            <h3
                                class="text-xl font-bold text-slate-800 mb-2 flex items-center justify-center md:justify-start gap-2">
                                <i class="ri-notification-3-line text-amber-500 text-2xl"></i>
                                Tentukan Keputusan Anda
                            </h3>
                            <p class="text-slate-500 text-sm leading-relaxed font-medium">Klik <strong>ACC Pesanan</strong>
                                untuk mengajukan penawaran harga final, atau <strong>Tolak</strong> jika detail tidak sesuai
                                dengan kapasitas Anda.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                            <button type="button" @click="stage = 'negotiation'"
                                class="flex-1 sm:w-52 flex items-center justify-center gap-2 bg-[#0f766e] hover:bg-[#0a5e58] text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 shadow-md hover:-translate-y-1">
                                <i class="ri-check-double-line text-xl"></i>
                                ACC Pesanan
                            </button>

                            <button type="button" @click="showRejectModal = true"
                                class="flex-1 sm:w-52 flex items-center justify-center gap-2 bg-white text-red-600 border border-red-100 hover:bg-red-50 font-semibold py-4 px-6 rounded-xl transition-all duration-300 hover:-translate-y-1">
                                <i class="ri-close-line text-xl"></i>
                                Tolak Pesanan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STAGE 2: NEGOTIATION (FORM HARGA) -->
                <div x-show="stage === 'negotiation'" x-cloak x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-6"
                    class="bg-white rounded-3xl shadow-2xl shadow-emerald-100 border-2 border-emerald-500/10 p-8 md:p-10">

                    <div class="flex items-center gap-4 mb-8">
                        <div
                            class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 animate-bounce-subtle">
                            <i class="ri-money-dollar-circle-line text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Kirim Penawaran Harga</h3>
                            <p class="text-sm text-slate-500 font-medium italic">Langkah terakhir sebelum pesanan masuk ke tahap
                                negosiasi resmi.</p>
                        </div>
                    </div>

                    <form x-data="{loading:false}" @submit.prevent="loading = true; $el.submit();"
                        action="{{ route('freelancer.orders.accept', $order->id) }}" method="POST">
                        @csrf
                        @method('POST')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                            <div>
                                <label
                                    class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-3 text-center md:text-left">Harga
                                    Dari Client</label>
                                <div
                                    class="px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-500 font-bold flex items-center justify-center md:justify-start gap-2">
                                    <span class="text-slate-400 font-medium">Rp</span>
                                    <span class="text-2xl">{{ number_format($order->service->price ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div>
                                <label for="agreed_price"
                                    class="block text-[0.65rem] font-black text-emerald-600 uppercase tracking-widest mb-3 text-center md:text-left">Harga
                                    Penawaran Anda</label>
                                <div class="relative group">
                                    <span
                                        class="absolute left-6 top-1/2 -translate-y-1/2 text-emerald-600 font-black text-xl">Rp</span>
                                    <input type="number" name="agreed_price" id="agreed_price" required
                                        class="w-full pl-16 pr-6 py-5 bg-white border-2 border-emerald-100 rounded-2xl focus:border-emerald-500 focus:ring-8 focus:ring-emerald-500/10 outline-none transition-all font-black text-slate-900 text-2xl shadow-inner text-center md:text-left"
                                        value="{{ old('agreed_price', $order->agreed_price ?? ($order->service->price ?? 0)) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label for="note"
                                class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-3">Catatan
                                Penawaran</label>
                            <textarea name="note" id="note" rows="4"
                                class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl focus:border-emerald-500 focus:ring-8 focus:ring-emerald-500/10 outline-none transition-all placeholder:text-slate-300 text-slate-700 font-semibold leading-relaxed"
                                placeholder="Contoh: Harga ini sudah termasuk revisi 3x dan pengerjaan express..."></textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-4 border-t border-slate-50">
                            <button type="button" @click="stage = 'decision'"
                                class="w-full sm:w-auto px-8 py-4 text-slate-400 hover:text-slate-600 font-bold transition-all uppercase text-xs tracking-widest">
                                Batal
                            </button>
                            <button type="submit" :disabled="loading" :aria-busy="loading"
                                class="w-full sm:w-auto px-12 py-4 bg-[#0f766e] hover:bg-[#0a5e58] text-white font-black rounded-xl transition-all shadow-md active:scale-95 uppercase tracking-wide disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <svg x-show="loading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor">
                                    <path d="M21 12a9 9 0 11-6.219-8.485"></path>
                                </svg>
                                <span x-text="loading ? 'Mengirim...' : 'Kirim Penawaran Final'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- DETAIL KONTEN ORDER -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                    <h4
                        class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="ri-file-list-3-line text-lg"></i>
                        Brief & Instruksi
                    </h4>
                    <div
                        class="text-slate-700 leading-relaxed font-medium text-[1rem] whitespace-pre-line bg-slate-50/50 p-6 rounded-2xl border border-slate-100">
                        {{ $order->brief }}
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <h4 class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-6">Profil Klien</h4>
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-50 flex items-center justify-center text-slate-400 text-2xl font-bold border border-slate-100">
                            {{ strtoupper(substr($order->client->name ?? 'C', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ $order->client->name ?? 'Digitalance Client' }}</p>
                            <p class="text-xs text-slate-400 font-medium">Bergabung
                                {{ $order->client->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                    <div class="space-y-3 pt-4 border-t border-slate-50 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-medium">Kategori</span>
                            <span
                                class="font-bold text-slate-700">{{ $order->service->service_category->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-medium">Tanggal Masuk</span>
                            <span class="font-bold text-slate-700">{{ $order->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL PENOLAKAN -->
    <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center p-4"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <!-- Backdrop Blur -->
        <div x-show="showRejectModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="showRejectModal = false"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <!-- Modal Box -->
        <div x-show="showRejectModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden z-[1001] border border-slate-100">

            <div class="p-10">
                <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 mb-6">
                    <i class="ri-error-warning-line text-4xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-2">Yakin Ingin Menolak?</h3>
                <p class="text-slate-500 text-sm mb-8 leading-relaxed">Tindakan ini tidak dapat dibatalkan. Klien akan
                    menerima notifikasi bahwa Anda menolak pesanan ini.</p>

                <form action="{{ route('freelancer.orders.reject', $order->id) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="mb-8">
                        <label for="reason"
                            class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-3">Alasan
                            Penolakan (Wajib)</label>
                        <textarea name="reason" id="reason" x-model="reason" rows="4" required
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-rose-500 focus:ring-8 focus:ring-rose-500/10 outline-none transition-all placeholder:text-slate-300 text-slate-700 font-semibold"
                            placeholder="Tuliskan alasan singkat penolakan..."></textarea>
                    </div>

                    <div class="flex gap-4">
                        <button type="button" @click="showRejectModal = false"
                            class="flex-1 py-4 px-6 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all uppercase text-xs tracking-widest">
                            Batal
                        </button>
                        <button type="submit" :disabled="!reason.trim()"
                            class="flex-1 py-4 px-6 bg-rose-600 hover:bg-rose-700 disabled:opacity-50 text-white font-black rounded-xl transition-all shadow-lg shadow-rose-200 uppercase text-xs tracking-widest">
                            Ya, Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection