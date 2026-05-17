@extends('layouts.dashboard')
@section('title', 'Checkout | Digitalance')

@php
    $totalPayment = (float)($order->agreed_price ?? 0);
    $fee = round($totalPayment * 0.10);
    $grandTotal = $totalPayment + $fee;
@endphp

@section('content')
<div class="animate-fadeUp flex-1 px-8 py-7 overflow-y-auto"
     x-data="{
        method: 'qris',
        price: {{ (float)($order->agreed_price ?? 0) }},
        isSubmitting: false,
        showConfirm: false,
        paymentSuccess: false,
        paymentError: null,
        feeAmount() { return Math.round(this.price * 0.10); },
        totalAmount() { return this.price + this.feeAmount(); },
        methodLabel() {
            const labels = {
                'qris': 'QRIS',
                'va_bca': 'BCA Virtual Account',
                'va_mandiri': 'Mandiri VA',
                'va_bri': 'BRI Virtual Account'
            };
            return labels[this.method] || this.method.toUpperCase();
        },
        confirmPayment() { this.showConfirm = true; },
        cancelPayment() { this.showConfirm = false; },
        async submitPayment() {
            this.showConfirm = false;
            this.isSubmitting = true;
            this.paymentError = null;
            const form = document.getElementById('paymentForm');
            const formData = new FormData(form);
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || data.error || 'Pembayaran gagal');
                this.paymentSuccess = true;
                window.showToast?.('Pembayaran berhasil!', 'success');
                if (data.redirect) setTimeout(() => { window.location.href = data.redirect; }, 2000);
            } catch (err) {
                this.paymentError = err.message || 'Terjadi kesalahan.';
                window.showToast?.(this.paymentError, 'danger');
            } finally {
                this.isSubmitting = false;
            }
        }
     }">

    {{-- SUCCESS STATE --}}
    <div x-show="paymentSuccess" x-cloak class="max-w-4xl mx-auto">
        <div class="bg-white border border-emerald-200 rounded-[24px] p-16 text-center shadow-lg shadow-emerald-100">
            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ri-checkbox-circle-fill text-5xl text-emerald-500"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-900 mb-3">Pembayaran Berhasil!</h2>
            <p class="text-slate-500 text-base mb-8">Terima kasih. Pesanan Anda sedang diproses.</p>
            <div class="flex items-center justify-center gap-3 text-sm text-slate-400">
                <i class="ri-loader-4-line animate-spin"></i> Mengalihkan ke halaman order...
            </div>
        </div>
    </div>

    {{-- MAIN CHECKOUT --}}
    <div x-show="!paymentSuccess" class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900 leading-tight">Checkout</h1>
            <p class="text-slate-500 mt-1 text-[0.95rem]">Pilih metode pembayaran dan selesaikan transaksi.</p>
        </div>

        {{-- Error Banner --}}
        <div x-show="paymentError" x-cloak class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3">
            <i class="ri-error-warning-line text-red-500 text-lg flex-shrink-0 mt-0.5"></i>
            <div class="flex-1">
                <p class="font-bold text-red-700 text-sm" x-text="paymentError"></p>
                <button @click="paymentError = null" class="text-xs text-red-500 hover:text-red-700 mt-1 font-semibold">Tutup</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                {{-- Order Summary --}}
                <div class="bg-white border border-slate-200 rounded-[24px] p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-6">Ringkasan Pesanan</h3>

                    <div class="flex gap-6 mb-8">
                        <div class="w-24 h-24 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[#0f766e] text-3xl shadow-sm">
                            <i class="ri-briefcase-line"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-teal-600 uppercase tracking-widest mb-1">
                                {{ $order->lokerApplication ? 'Custom Project' : ($order->service?->service_category?->name ?? 'Service') }}
                            </p>
                            <h4 class="text-xl font-bold text-slate-900 mb-2">
                                {{ $order->lokerApplication ? 'Order #' . $order->id : ($order->service?->title ?? 'Service Order') }}
                            </h4>
                            <div class="flex items-center gap-2 text-sm text-slate-500 font-medium">
                                <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px]">
                                    <i class="ri-user-3-fill"></i>
                                </div>
                                <span>{{ $order->lokerApplication ? ($order->freelancer?->skomda_student?->name ?? 'Freelancer') : ($order->service?->freelancer?->skomda_student?->name ?? 'Freelancer') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-6 border-t border-slate-100">
                        <div class="flex justify-between items-center text-slate-600">
                            <span class="font-medium text-sm">Harga Jasa</span>
                            <span class="font-bold">Rp {{ number_format($order->agreed_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600">
                            <span class="font-medium text-sm">Biaya Platform (10%)</span>
                            <span class="font-bold text-amber-600">+ Rp <span x-text="feeAmount().toLocaleString('id-ID')"></span></span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t-2 border-dashed border-slate-100">
                            <span class="text-lg font-black text-slate-900">Total Pembayaran</span>
                            <span class="text-2xl font-black text-[#0f766e]">Rp <span x-text="totalAmount().toLocaleString('id-ID')"></span></span>
                        </div>
                    </div>
                </div>

                {{-- Payment Methods --}}
                <div class="bg-white border border-slate-200 rounded-[24px] p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-6">Pilih Metode Pembayaran</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach([
                            ['value' => 'qris', 'label' => 'QRIS', 'desc' => 'Gopay, OVO, Dana, LinkAja', 'icon' => 'ri-qr-code-line', 'color' => 'text-rose-500'],
                            ['value' => 'va_bca', 'label' => 'BCA Virtual Account', 'desc' => 'Transfer Bank BCA', 'icon' => 'ri-bank-line', 'color' => 'text-blue-600'],
                            ['value' => 'va_mandiri', 'label' => 'Mandiri VA', 'desc' => "Livin' by Mandiri", 'icon' => 'ri-bank-card-2-line', 'color' => 'text-blue-800'],
                            ['value' => 'va_bri', 'label' => 'BRI Virtual Account', 'desc' => 'BRIVA / Mobile Banking', 'icon' => 'ri-money-dollar-box-line', 'color' => 'text-emerald-600'],
                        ] as $pm)
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="payment_method" value="{{ $pm['value'] }}" x-model="method" class="absolute opacity-0 peer">
                            <div class="p-5 rounded-2xl border-2 transition-all flex items-center gap-4"
                                 :class="method === '{{ $pm['value'] }}' ? 'border-[#0f766e] bg-teal-50/50 shadow-md shadow-teal-100' : 'border-slate-100 hover:border-slate-200'">
                                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center {{ $pm['color'] }}">
                                    <i class="{{ $pm['icon'] }} text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-black text-slate-900 text-sm">{{ $pm['label'] }}</p>
                                    <p class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">{{ $pm['desc'] }}</p>
                                </div>
                                <div class="ml-auto" x-show="method === '{{ $pm['value'] }}'" x-transition>
                                    <i class="ri-checkbox-circle-fill text-[#0f766e] text-xl"></i>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Checkout Panel --}}
            <div class="space-y-6">
                <div class="bg-slate-900 rounded-[32px] p-8 text-white shadow-2xl shadow-slate-300 sticky top-8">
                    <h3 class="text-lg font-bold mb-8 flex items-center gap-3">
                        <i class="ri-shield-flash-line text-teal-400"></i> Penyelesaian
                    </h3>

                    <div class="mb-8">
                        {{-- QRIS --}}
                        <div x-show="method === 'qris'" class="text-center">
                            <div class="bg-white p-4 rounded-2xl mb-4 inline-block shadow-lg">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=digitalance-payment-{{ $order->id }}" alt="QRIS" class="w-32 h-32 mx-auto" loading="lazy">
                            </div>
                            <p class="text-[10px] font-black text-teal-400 uppercase tracking-widest mb-1">Scan QRIS</p>
                            <p class="text-xs text-slate-400">Silakan scan kode QR di atas</p>
                        </div>

                        {{-- VA --}}
                        <div x-show="method.startsWith('va_')" class="animate-fadeUp">
                            <div class="p-5 bg-white/5 rounded-2xl border border-white/10 mb-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nomor Virtual Account</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-mono font-black text-teal-400 select-all">880123{{ $order->id }}9988</span>
                                    <button type="button" @click="navigator.clipboard.writeText('880123{{ $order->id }}9988'); window.showToast?.('Nomor VA disalin!', 'success')" class="text-xs font-bold text-white/50 hover:text-white">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 text-center">Transfer tepat sampai digit terakhir</p>
                        </div>
                    </div>

                    <div class="space-y-3 pt-6 border-t border-white/10 mb-8">
                        <div class="flex justify-between text-xs font-medium text-slate-400">
                            <span>Subtotal</span>
                            <span>Rp <span x-text="price.toLocaleString('id-ID')"></span></span>
                        </div>
                        <div class="flex justify-between text-sm font-black">
                            <span>Total Bayar</span>
                            <span class="text-teal-400 text-lg">Rp <span x-text="totalAmount().toLocaleString('id-ID')"></span></span>
                        </div>
                    </div>

                    <form id="paymentForm" action="{{ route('client.orders.process-payment', $order->id) }}" method="POST" @submit.prevent="submitPayment">
                        @csrf
                        <input type="hidden" name="total_paid" :value="totalAmount()">
                        <input type="hidden" name="payment_method" :value="method">

                        <button type="button" @click="confirmPayment()" :disabled="isSubmitting"
                                class="w-full py-4 bg-teal-500 hover:bg-teal-400 disabled:opacity-60 disabled:cursor-not-allowed text-white font-black rounded-2xl transition-all shadow-lg shadow-teal-500/20 flex items-center justify-center gap-3 text-sm uppercase tracking-widest">
                            <template x-if="!isSubmitting">
                                <span>Konfirmasi Pembayaran <i class="ri-arrow-right-line"></i></span>
                            </template>
                            <template x-if="isSubmitting">
                                <span><svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Memproses...</span>
                            </template>
                        </button>
                    </form>

                    <div class="mt-8 flex items-center justify-center gap-3 opacity-50 grayscale">
                        <div class="h-4 w-auto bg-white/10 px-2 rounded text-[8px] flex items-center font-bold">QRIS</div>
                        <div class="h-4 w-auto bg-white/10 px-2 rounded text-[8px] flex items-center font-bold">BCA</div>
                        <div class="h-4 w-auto bg-white/10 px-2 rounded text-[8px] flex items-center font-bold">VISA</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CONFIRMATION MODAL --}}
    <div x-show="showConfirm" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div @click="showConfirm = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-[24px] shadow-2xl w-full max-w-md p-8 z-10">
            <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="ri-secure-payment-line text-4xl text-[#0f766e]"></i>
            </div>
            <h2 class="text-xl font-black text-slate-900 text-center mb-2">Konfirmasi Pembayaran</h2>
            <p class="text-slate-500 text-sm text-center mb-6">Pastikan detail pembayaran sudah benar.</p>

            <div class="bg-slate-50 rounded-2xl p-5 mb-6 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 font-medium">Metode</span>
                    <span class="font-bold text-slate-900" x-text="methodLabel()"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 font-medium">Jumlah</span>
                    <span class="font-bold text-[#0f766e]">Rp <span x-text="price.toLocaleString('id-ID')"></span></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500 font-medium">Biaya Admin</span>
                    <span class="font-bold text-amber-600">Rp <span x-text="feeAmount().toLocaleString('id-ID')"></span></span>
                </div>
                <div class="pt-3 border-t border-slate-200 flex justify-between">
                    <span class="font-black text-slate-900">Total</span>
                    <span class="font-black text-xl text-[#0f766e]">Rp <span x-text="totalAmount().toLocaleString('id-ID')"></span></span>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="cancelPayment()" class="flex-1 py-3.5 rounded-[14px] bg-slate-100 text-slate-600 font-bold text-sm hover:bg-slate-200">
                    Batal
                </button>
                <button type="button" @click="submitPayment()" class="flex-1 py-3.5 rounded-[14px] bg-[#0f766e] text-white font-bold text-sm hover:bg-[#0a5e58] shadow-lg shadow-teal-500/20 flex items-center justify-center gap-2">
                    <i class="ri-checkbox-circle-line"></i> Ya, Bayar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection