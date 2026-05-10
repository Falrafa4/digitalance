@extends('layouts.dashboard')
@section('title', 'Checkout | Digitalance')

@section('content')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<div class="animate-fadeUp flex-1 px-8 py-7 overflow-y-auto" x-data="{ 
    method: 'qris',
    feePercent: 10,
    price: {{ (float)($order->agreed_price ?? 0) }},
    get fee() { return Math.round(this.price * (this.feePercent / 100)) },
    get total() { return this.price + this.fee }
}">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900 leading-tight">Checkout</h1>
            <p class="text-slate-500 mt-1 text-[0.95rem]">Pilih metode pembayaran dan selesaikan transaksi.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                {{-- Order Summary --}}
                <div class="bg-white border border-slate-200 rounded-[24px] p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-6">Ringkasan Pesanan</h3>
                    
                    <div class="flex gap-6 mb-8">
                        <div class="w-24 h-24 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[#0f766e] text-3xl shadow-sm">
                            <i class="ri-service-line"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-teal-600 uppercase tracking-widest mb-1">
                                {{ $order->service->service_category->name ?? 'Service' }}
                            </p>
                            <h4 class="text-xl font-bold text-slate-900 mb-2">{{ $order->service->title }}</h4>
                            <div class="flex items-center gap-2 text-sm text-slate-500 font-medium">
                                <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px]">
                                    <i class="ri-user-3-fill"></i>
                                </div>
                                <span>{{ $order->service->freelancer->skomda_student->name }}</span>
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
                            <span class="font-bold text-amber-600">+ Rp <span x-text="fee.toLocaleString('id-ID')"></span></span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t-2 border-dashed border-slate-100">
                            <span class="text-lg font-black text-slate-900">Total Pembayaran</span>
                            <span class="text-2xl font-black text-[#0f766e]">Rp <span x-text="total.toLocaleString('id-ID')"></span></span>
                        </div>
                    </div>
                </div>

                {{-- Payment Methods --}}
                <div class="bg-white border border-slate-200 rounded-[24px] p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 mb-6">Pilih Metode Pembayaran</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="payment_method" value="qris" x-model="method" class="absolute opacity-0">
                            <div class="p-5 rounded-2xl border-2 transition-all flex items-center gap-4" :class="method === 'qris' ? 'border-[#0f766e] bg-teal-50/50 shadow-md shadow-teal-100' : 'border-slate-100 hover:border-slate-200'">
                                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-rose-500">
                                    <i class="ri-qr-code-line text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-black text-slate-900 text-sm">QRIS</p>
                                    <p class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">Gopay, OVO, Dana, LinkAja</p>
                                </div>
                                <div class="ml-auto" x-show="method === 'qris'">
                                    <i class="ri-checkbox-circle-fill text-[#0f766e] text-xl"></i>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="payment_method" value="va_bca" x-model="method" class="absolute opacity-0">
                            <div class="p-5 rounded-2xl border-2 transition-all flex items-center gap-4" :class="method === 'va_bca' ? 'border-[#0f766e] bg-teal-50/50 shadow-md shadow-teal-100' : 'border-slate-100 hover:border-slate-200'">
                                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600">
                                    <i class="ri-bank-line text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-black text-slate-900 text-sm">BCA Virtual Account</p>
                                    <p class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">Transfer Bank BCA</p>
                                </div>
                                <div class="ml-auto" x-show="method === 'va_bca'">
                                    <i class="ri-checkbox-circle-fill text-[#0f766e] text-xl"></i>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="payment_method" value="va_mandiri" x-model="method" class="absolute opacity-0">
                            <div class="p-5 rounded-2xl border-2 transition-all flex items-center gap-4" :class="method === 'va_mandiri' ? 'border-[#0f766e] bg-teal-50/50 shadow-md shadow-teal-100' : 'border-slate-100 hover:border-slate-200'">
                                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-800">
                                    <i class="ri-bank-card-2-line text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-black text-slate-900 text-sm">Mandiri VA</p>
                                    <p class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">Livin' by Mandiri</p>
                                </div>
                                <div class="ml-auto" x-show="method === 'va_mandiri'">
                                    <i class="ri-checkbox-circle-fill text-[#0f766e] text-xl"></i>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="payment_method" value="va_bri" x-model="method" class="absolute opacity-0">
                            <div class="p-5 rounded-2xl border-2 transition-all flex items-center gap-4" :class="method === 'va_bri' ? 'border-[#0f766e] bg-teal-50/50 shadow-md shadow-teal-100' : 'border-slate-100 hover:border-slate-200'">
                                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-emerald-600">
                                    <i class="ri-money-dollar-box-line text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-black text-slate-900 text-sm">BRI Virtual Account</p>
                                    <p class="text-[11px] text-slate-400 font-bold uppercase tracking-tighter">BRIVA / Mobile Banking</p>
                                </div>
                                <div class="ml-auto" x-show="method === 'va_bri'">
                                    <i class="ri-checkbox-circle-fill text-[#0f766e] text-xl"></i>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Checkout Panel --}}
            <div class="space-y-6">
                <div class="bg-slate-900 rounded-[32px] p-8 text-white shadow-2xl shadow-slate-300 sticky top-8 overflow-hidden">
                    {{-- Decorative Circle --}}
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-teal-500/10 rounded-full blur-3xl"></div>
                    
                    <h3 class="text-lg font-bold mb-8 flex items-center gap-3 relative z-10">
                        <i class="ri-shield-flash-line text-teal-400"></i>
                        Penyelesaian
                    </h3>

                    {{-- Dynamic Simulation Content --}}
                    <div class="relative z-10 mb-8">
                        <div x-show="method === 'qris'" class="text-center animate-fadeUp">
                            <div class="bg-white p-4 rounded-2xl mb-4 inline-block shadow-lg">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=digitalance-payment-simulation-{{ $order->id }}" alt="QRIS" class="w-32 h-32 mx-auto">
                            </div>
                            <p class="text-[10px] font-black text-teal-400 uppercase tracking-widest mb-1">Scan QRIS</p>
                            <p class="text-xs text-slate-400">Silakan scan kode QR di atas</p>
                        </div>

                        <div x-show="method.startsWith('va_')" class="animate-fadeUp">
                            <div class="p-5 bg-white/5 rounded-2xl border border-white/10 mb-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nomor Virtual Account</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-mono font-black text-teal-400">880123{{ $order->id }}9988</span>
                                    <button class="text-xs font-bold text-white/50 hover:text-white"><i class="ri-file-copy-line"></i></button>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 text-center">Transfer tepat sampai digit terakhir</p>
                        </div>
                    </div>

                    <div class="space-y-3 pt-6 border-t border-white/10 relative z-10 mb-8">
                        <div class="flex justify-between text-xs font-medium text-slate-400">
                            <span>Subtotal</span>
                            <span>Rp <span x-text="total.toLocaleString('id-ID')"></span></span>
                        </div>
                        <div class="flex justify-between text-sm font-black">
                            <span>Total Bayar</span>
                            <span class="text-teal-400 text-lg">Rp <span x-text="total.toLocaleString('id-ID')"></span></span>
                        </div>
                    </div>

                    <form action="{{ route('client.orders.process-payment', $order->id) }}" method="POST" class="relative z-10">
                        @csrf
                        <input type="hidden" name="total_paid" :value="total">
                        <input type="hidden" name="payment_method" :value="method">
                        <button type="submit" class="w-full py-4 bg-teal-500 hover:bg-teal-400 text-white font-black rounded-2xl transition-all shadow-lg shadow-teal-500/20 flex items-center justify-center gap-3 text-sm uppercase tracking-widest group">
                            Konfirmasi Pembayaran
                            <i class="ri-arrow-right-line group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>

                    <div class="mt-8 flex items-center justify-center gap-3 relative z-10 opacity-50 grayscale">
                        <div class="h-4 w-auto bg-white/10 px-2 rounded text-[8px] flex items-center font-bold">QRIS</div>
                        <div class="h-4 w-auto bg-white/10 px-2 rounded text-[8px] flex items-center font-bold">BCA</div>
                        <div class="h-4 w-auto bg-white/10 px-2 rounded text-[8px] flex items-center font-bold">VISA</div>
                        <div class="h-4 w-auto bg-white/10 px-2 rounded text-[8px] flex items-center font-bold">GOPAY</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
