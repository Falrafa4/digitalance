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
                <span class="w-2 h-2 rounded-full @if($order->status == 'Pending') bg-amber-500 @elseif(in_array($order->status, ['In Progress', 'Revision'])) bg-blue-500 @else bg-emerald-500 @endif animate-pulse"></span>
                <span class="text-xs font-black uppercase tracking-widest text-slate-600">{{ $order->status }}</span>
            </div>
        </div>

        {{-- TRACKING STEPPER (like client) --}}
        <div class="bg-white border border-slate-200 rounded-[18px] p-6 mb-8">
            @php
                $raw = (string)($order->status ?? 'Pending');
                $norm = strtolower(str_replace(['_', '-'], ' ', $raw));
                $steps = [
                    ['key' => 'pending', 'label' => 'Pending', 'desc' => 'Order dibuat'],
                    ['key' => 'negotiated', 'label' => 'Negotiated', 'desc' => 'Negosiasi / konfirmasi'],
                    ['key' => 'paid', 'label' => 'Paid', 'desc' => 'Pembayaran'],
                    ['key' => 'in progress', 'label' => 'In Progress', 'desc' => 'Pengerjaan'],
                    ['key' => 'revision', 'label' => 'Revision', 'desc' => 'Revisi'],
                    ['key' => 'completed', 'label' => 'Completed', 'desc' => 'Selesai'],
                ];
                $currentIndex = 0;
                foreach($steps as $i => $st){ if($st['key'] === $norm){ $currentIndex = $i; break; } }
            @endphp
            <p class="text-slate-400 text-[12px] font-extrabold uppercase tracking-widest mb-3">Tracking</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                @foreach($steps as $i => $st)
                    @php $done = $i < $currentIndex; $active = $i === $currentIndex; @endphp
                    <div class="rounded-[12px] border p-3 {{ $active ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-extrabold text-slate-900 text-[11px]">{{ $st['label'] }}</p>
                                <p class="text-slate-400 text-[10px]">{{ $st['desc'] }}</p>
                            </div>
                            @if($done) <span class="text-emerald-600 text-xs"><i class="ri-check-line"></i></span>
                            @elseif($active) <span class="text-emerald-700 font-bold text-[10px] px-2 py-0.5 rounded bg-white border border-emerald-200">Now</span>
                            @else <span class="text-slate-300"><i class="ri-circle-line"></i></span> @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @php
            $latestClientNego = $order->negotiations
                ->where('sender', 'client')
                ->sortByDesc('created_at')
                ->first();
            $hasFreelancerResponse = $latestClientNego 
                ? $order->negotiations->where('sender', 'freelancer')->where('created_at', '>', $latestClientNego->created_at)->count() > 0 
                : false;
        @endphp

        @if($order->status == 'Pending')

            <!-- NEGO BARU MASUK ALERT -->
            @if($latestClientNego && !$hasFreelancerResponse && $latestClientNego->created_at->diffInHours(now()) < 24)
            <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-400 text-white flex items-center justify-center flex-shrink-0">
                    <i class="ri-notification-3-line text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-amber-800 text-sm mb-0.5">Nego Baru dari Klien</p>
                    <p class="text-amber-700 text-xs leading-relaxed">{{ Str::limit($latestClientNego->message, 120) }}</p>
                    <a href="#riwayat-nego" class="inline-flex items-center gap-1 mt-2 text-xs font-bold text-amber-700 hover:text-amber-900">
                        <i class="ri-reply-line"></i> Buka & Respond
                    </a>
                </div>
            </div>
            @endif

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
            
            @if($order->status == 'Negotiated')
            <!-- NEGOTIATION RESPONSE SYSTEM -->
            <div class="mb-8 relative z-10">
                <div class="bg-white rounded-2xl shadow-md border border-teal-100 p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center text-teal-600">
                            <i class="ri-chat-voice-line text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Respon Negosiasi</h3>
                            <p class="text-sm text-slate-500">Klien telah menanggapi penawaran Anda. Anda bisa mengupdate harga kesepakatan akhir di sini.</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('freelancer.orders.accept', $order->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Harga Saat Ini</label>
                                <div class="px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-500 font-bold">
                                    Rp {{ number_format($order->agreed_price ?? $order->service->price ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                            <div>
                                <label for="agreed_price" class="block text-[10px] font-black text-teal-600 uppercase tracking-widest mb-2">Harga Kesepakatan Baru (ACC)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-teal-600 font-bold">Rp</span>
                                    <input type="number" name="agreed_price" id="agreed_price" required
                                        class="w-full pl-12 pr-4 py-4 bg-white border-2 border-teal-100 rounded-xl focus:border-teal-500 outline-none font-bold text-slate-900 shadow-sm"
                                        value="{{ old('agreed_price', $order->agreed_price ?? ($order->service->price ?? 0)) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="note" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pesan Balasan</label>
                            <textarea name="note" id="note" rows="3"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:border-teal-500 outline-none text-sm font-medium"
                                placeholder="Tulis pesan konfirmasi atau alasan perubahan harga..."></textarea>
                        </div>

                        <button type="submit" class="w-full py-4 bg-[#0f766e] hover:bg-[#0a5e58] text-white font-bold rounded-xl transition-all shadow-lg shadow-teal-100 flex items-center justify-center gap-2">
                            <i class="ri-check-double-line text-lg"></i>
                            ACC & Update Penawaran Harga
                        </button>
                    </form>
                </div>
            </div>
            @endif

        {{-- REVISION RESPONSE (show when status is Revision) --}}
        @if($order->status == 'Revision')
        <div class="mb-8 relative z-10">
            <div class="bg-white rounded-2xl shadow-md border border-amber-100 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                        <i class="ri-refresh-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Permintaan Revisi dari Klien</h3>
                        <p class="text-sm text-slate-500">Klien meminta revisi pada pesanan ini.</p>
                    </div>
                </div>
                
                <div class="flex gap-4">
                    <form action="{{ route('freelancer.orders.revision.approve', $order->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-3.5 rounded-[14px] bg-emerald-500 text-white font-bold text-[14px] hover:bg-emerald-600 transition-all flex items-center justify-center gap-2">
                            <i class="ri-check-line"></i>
                            Terima Revisi
                        </button>
                    </form>
                    <form action="{{ route('freelancer.orders.revision.reject', $order->id) }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="reason" id="rejectReason{{ $order->id }}" value="">
                        <button type="button" onclick="const reason = prompt('Alasan penolakan:'); if(reason) { document.getElementById('rejectReason{{ $order->id }}').value = reason; this.closest('form').submit(); }" class="w-full py-3.5 rounded-[14px] bg-white border border-red-200 text-red-600 font-bold text-[14px] hover:bg-red-50 transition-all flex items-center justify-center gap-2">
                            <i class="ri-close-line"></i>
                            Tolak Revisi
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- PRICE NEGOTIATION NOTIFICATION REMOVED AS IT REQUIRES MIGRATION COLUMNS --}}

        {{-- SUBMIT RESULT (show when status is Paid or In Progress) --}}
        @if(in_array($order->status, ['Paid', 'In Progress']))
        <div class="mb-8 relative z-10">
            <div class="bg-white rounded-2xl shadow-md border border-emerald-100 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                        <i class="ri-upload-cloud-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Kirim Hasil Kerja</h3>
                        <p class="text-sm text-slate-500">Upload file hasil pekerjaan untuk dikirim ke klien.</p>
                    </div>
                </div>
                
                <form action="{{ route('freelancer.results.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Versi/Version</label>
                        <input type="text" name="version" required class="w-full px-4 py-3 rounded-[12px] border border-slate-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm" placeholder="Contoh: v1.0, Final, Revisi-1">
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">Catatan</label>
                        <textarea name="note" rows="2" class="w-full px-4 py-3 rounded-[12px] border border-slate-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm" placeholder="Catatan tambahan untuk klien..."></textarea>
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2">File Hasil <span class="text-red-500">*</span></label>
                        <input type="file" name="file" required accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png" class="w-full px-4 py-3 rounded-[12px] border border-slate-200 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none text-sm">
                    </div>
                    <button type="submit" class="w-full py-3.5 rounded-[14px] bg-emerald-500 text-white font-bold text-[14px] hover:bg-emerald-600 transition-all flex items-center justify-center gap-2">
                        <i class="ri-send-plane-line"></i>
                        Kirim Hasil ke Klien
                    </button>
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

        @php
            $freelancerResponded = $order->negotiations->where('sender', 'freelancer')->count() > 0;
        @endphp

        <!-- NEGOTIATION HISTORY -->
        <div class="mt-8 animate-fadeUp-2" id="riwayat-nego">
            <h4 class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="ri-history-line text-lg"></i>
                Riwayat Negosiasi & Pesan
            </h4>
            
            <div class="space-y-4">
                @forelse($order->negotiations->sortByDesc('created_at') as $nego)
                    <div class="bg-white border {{ $nego->sender == 'client' ? 'border-amber-100 bg-amber-50/10' : 'border-slate-100' }} rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $nego->sender == 'client' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $nego->sender }}
                                </span>
                                <span class="text-[11px] font-bold text-slate-400">
                                    {{ $nego->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                            @if($nego->sender == 'client' && $order->status != 'Completed')
                                <a href="{{ route('freelancer.negotiations.view', $nego->order_id) }}"
                                   class="px-3 py-1.5 rounded-lg bg-[#0f766e]/10 text-[#0f766e] text-[10px] font-bold hover:bg-[#0f766e]/20 transition-colors flex items-center gap-1">
                                    <i class="ri-reply-line"></i> Buka & Respond
                                </a>
                            @endif
                        </div>
                        <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $nego->message }}</div>
                    </div>
                @empty
                    <div class="py-12 text-center bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-slate-400 font-medium text-sm">Belum ada riwayat negosiasi.</p>
                    </div>
                @endforelse
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