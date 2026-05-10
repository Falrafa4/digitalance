@extends('layouts.dashboard')
@section('title', 'Detail Order')

@section('styles')
<style>[x-cloak]{display:none!important}</style>
@endsection

@section('content')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@if(session('success'))
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      window.showToast('Brief berhasil dikirim! Silakan tunggu respons freelancer.', 'success');
    });
  </script>
@endif
<section class="animate-fadeUp">
  <div class="flex flex-col lg:flex-row gap-6">
    <div class="flex-1 min-w-0 space-y-6">

      {{-- Header --}}
      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <a href="{{ route('client.orders.index') }}" class="text-slate-500 font-bold text-[13px] hover:text-slate-900">
              <i class="ri-arrow-left-line mr-1"></i> Kembali
            </a>
            <h1 class="font-display text-[1.65rem] font-extrabold text-slate-900 mt-2">Order #{{ $order->id }}</h1>
            <p class="text-slate-500 mt-1 text-[13.5px]">
              Jasa: <span class="font-bold">{{ $order->service->title ?? '-' }}</span>
            </p>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            @include('dashboard.client._ui.status-badge', ['status' => $order->status ?? '-'])
            <span class="px-3 py-1 rounded-full text-[12px] font-extrabold bg-white text-slate-700 border border-slate-200">
              Agreed: Rp {{ number_format((float)($order->agreed_price ?? 0), 0, ',', '.') }}
            </span>
          </div>
        </div>

        {{-- Tracking stepper (simple, status apapun tetap kebaca) --}}
        @php
          $raw = (string)($order->status ?? 'Pending');
          $norm = strtolower(str_replace(['_', '-'], ' ', $raw));

          // urutan logis, biar stepper konsisten walau status punya spasi
          $steps = [
            ['key' => 'pending', 'label' => 'Pending', 'desc' => 'Order dibuat'],
            ['key' => 'negotiated', 'label' => 'Negotiated', 'desc' => 'Negosiasi / konfirmasi'],
            ['key' => 'paid', 'label' => 'Paid', 'desc' => 'Pembayaran'],
            ['key' => 'in progress', 'label' => 'In Progress', 'desc' => 'Pengerjaan'],
            ['key' => 'revision', 'label' => 'Revision', 'desc' => 'Revisi'],
            ['key' => 'completed', 'label' => 'Completed', 'desc' => 'Selesai'],
          ];

          $currentIndex = 0;
          foreach($steps as $i => $st){
            if($st['key'] === $norm){ $currentIndex = $i; break; }
          }

          $isCancelled = ($norm === 'cancelled');
        @endphp

        <div class="mt-6">
          <p class="text-slate-400 text-[12px] font-extrabold uppercase tracking-widest mb-3">Tracking</p>

          @if($isCancelled)
            <div class="rounded-[16px] border border-rose-100 bg-rose-50 p-4">
              <p class="font-extrabold text-rose-700">Order dibatalkan</p>
              <p class="text-rose-600 text-[13px] mt-1">Status terakhir: {{ \Illuminate\Support\Str::headline($order->status) }}</p>
            </div>
          @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
              @foreach($steps as $i => $st)
                @php
                  $done = $i < $currentIndex;
                  $active = $i === $currentIndex;
                @endphp
                <div class="rounded-[16px] border p-4 {{ $active ? 'border-teal-200 bg-teal-50' : 'border-slate-200 bg-white' }}">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <p class="font-extrabold text-slate-900 text-[13px]">{{ $st['label'] }}</p>
                      <p class="text-slate-500 text-[12px] mt-1">{{ $st['desc'] }}</p>
                    </div>
                    @if($done)
                      <span class="text-emerald-600"><i class="ri-check-line"></i></span>
                    @elseif($active)
                      <span class="text-teal-700 font-extrabold text-[11px] px-2.5 py-1 rounded-full bg-white border border-teal-200">Now</span>
                    @else
                      <span class="text-slate-300"><i class="ri-circle-line"></i></span>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>

      {{-- ACTION BUTTONS: Accept/Tolak/Negosiasi --}}
      @if($order->status === 'Negotiated' && $order->agreed_price)
      <div x-data="{ showNego: false, showReject: false }" class="bg-white border border-slate-200 rounded-[18px] p-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div>
            <h3 class="font-display font-extrabold text-slate-900 text-[1.1rem]">Harga Disepakati</h3>
            <p class="text-2xl font-black text-[#0f766e] mt-1">Rp {{ number_format($order->agreed_price, 0, ',', '.') }}</p>
          </div>
          <div class="flex flex-col sm:flex-row gap-3">
            <button @click="showNego = true" class="px-5 py-3 rounded-[12px] bg-amber-50 border border-amber-200 text-amber-700 font-bold text-[13px] hover:bg-amber-100 transition-all">
              <i class="ri-chat-history-line mr-1"></i> Negosiasi
            </button>
            <form action="{{ route('client.orders.reject', $order->id) }}" method="POST" @submit="if(!showReject){ showReject = true; return false; } return true;">
              @csrf
              <input type="hidden" name="reason" id="rejectReason" value="">
              <button type="submit" class="px-5 py-3 rounded-[12px] bg-white border border-red-200 text-red-600 font-bold text-[13px] hover:bg-red-50 transition-all">
                <i class="ri-close-line mr-1"></i> Tolak
              </button>
            </form>
            <form action="{{ route('client.orders.accept', $order->id) }}" method="POST">
              @csrf
              <button type="submit" onclick="return confirm('Terima pesanan dan lanjut ke pembayaran?')" class="px-5 py-3 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all">
                <i class="ri-check-line mr-1"></i> Lanjut ke Pembayaran
              </button>
            </form>
          </div>
        </div>

        {{-- Banner Menunggu Pembayaran --}}
        <div class="mt-5 p-4 rounded-xl bg-blue-50 border border-blue-100 flex items-start gap-3">
          <div class="w-9 h-9 rounded-lg bg-blue-500 text-white flex items-center justify-center flex-shrink-0">
            <i class="ri-bank-card-line text-lg"></i>
          </div>
          <div>
            <p class="font-bold text-blue-800 text-sm mb-0.5">Menunggu Pembayaran</p>
            <p class="text-blue-700 text-xs leading-relaxed">Selesaikan pembayaran untuk melanjutkan pesanan.</p>
            <a href="{{ route('client.orders.checkout', $order->id) }}" class="inline-flex items-center gap-1 mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-all">
              <i class="ri-arrow-right-line"></i> Bayar Sekarang
            </a>
          </div>
        </div>

        {{-- Modal Negosiasi --}}
        <div x-show="showNego" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div @click="showNego = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
          <div class="relative w-full max-w-lg bg-white rounded-[24px] shadow-xl p-6 sm:p-8">
            <button @click="showNego = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
              <i class="ri-close-line"></i>
            </button>
            <h2 class="font-display text-xl font-bold text-slate-900 mb-1">Ajukan Negosiasi</h2>
            <p class="text-sm text-slate-500 mb-5">Kirimkan harga baru ke freelancer</p>
            
            <form action="{{ route('client.orders.nego', $order->id) }}" method="POST" class="space-y-4">
              @csrf
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Alasan <span class="text-red-500">*</span></label>
                <textarea name="reason" rows="2" required class="w-full px-4 py-3 rounded-[12px] border border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 outline-none text-sm
                     @error('reason') input-error @enderror" placeholder="Alasan nego..."></textarea>
                @error('reason')
                  <p class="text-red-600 text-[12px] font-bold mt-1">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Harga Baru <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-semibold">Rp</span>
                  <input type="number" name="new_price" required min="1000" class="w-full pl-10 pr-4 py-3 rounded-[12px] border border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 outline-none text-sm font-semibold
                     @error('new_price') input-error @enderror" placeholder="0">
                </div>
                @error('new_price')
                  <p class="text-red-600 text-[12px] font-bold mt-1">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="2" class="w-full px-4 py-3 rounded-[12px] border border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 outline-none text-sm" placeholder="Detail tambahan..."></textarea>
              </div>
              <div class="flex gap-3 pt-2">
                <button type="button" @click="showNego = false" class="flex-1 py-3 rounded-[12px] bg-slate-100 text-slate-600 font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-[12px] bg-amber-500 text-white font-bold text-sm hover:bg-amber-600">Kirim</button>
              </div>
            </form>
          </div>
        </div>

        {{-- Modal Konfirmasi Tolak --}}
        <div x-show="showReject" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div @click="showReject = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
          <div class="relative w-full max-w-md bg-white rounded-[20px] shadow-xl p-6">
            <div class="text-center">
              <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-close-line text-2xl text-red-600"></i>
              </div>
              <h3 class="font-display text-lg font-bold text-slate-900 mb-2">Tolak Pesanan?</h3>
              <p class="text-sm text-slate-500 mb-5">Mohon berikan alasan penolakan.</p>
              <form action="{{ route('client.orders.reject', $order->id) }}" method="POST" class="space-y-4">
                @csrf
                <textarea name="reason" rows="3" required class="w-full px-4 py-3 rounded-[12px] border border-slate-200 text-sm" placeholder="Alasan menolak..."></textarea>
                <div class="flex gap-3">
                  <button type="button" @click="showReject = false" class="flex-1 py-2.5 rounded-[12px] bg-slate-100 text-slate-600 font-bold text-sm">Batal</button>
                  <button type="submit" class="flex-1 py-2.5 rounded-[12px] bg-red-500 text-white font-bold text-sm hover:bg-red-600">Ya, Tolak</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      @endif

      {{-- Brief + Attachment --}}
      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem]">Brief</h2>
        <p class="text-slate-600 text-[14px] mt-3 whitespace-pre-line">{{ $order->brief ?? '-' }}</p>

        <div class="mt-6 pt-5 border-t border-slate-100">
          <p class="text-slate-400 text-[12px] font-extrabold uppercase tracking-widest mb-2">Upload Attachment (MVP)</p>
          <form method="POST" action="{{ route('client.orders.attachments.store', $order->id) }}" enctype="multipart/form-data"
                class="flex flex-col sm:flex-row gap-3">
            @csrf
            <input type="file" name="file"
                   class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200"/>
            <button class="px-5 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
              Upload
            </button>
          </form>
          @error('file')
            <p class="text-red-600 text-[12px] font-bold mt-2">{{ $message }}</p>
          @enderror

          <p class="text-slate-400 text-[12px] font-bold mt-3">
            Catatan: file path akan ditambahkan ke brief (tanpa tabel attachment).
          </p>
        </div>
      </div>

      {{-- Messages / Negotiation --}}
      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <div class="flex items-end justify-between gap-3">
          <div>
            <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem]">Messages / Negosiasi</h2>
            <p class="text-slate-500 text-[13.5px] mt-1">Diskusi detail, nego harga, revisi, dll.</p>
          </div>
          <a href="{{ route('client.messages.index') }}"
             class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px]
                    hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
            Inbox
          </a>
        </div>

        <div class="mt-4 space-y-3">
          @forelse(($order->negotiations ?? []) as $n)
            @php $mine = (($n->sender ?? '') === 'client'); @endphp
            <div class="rounded-[16px] border p-4 {{ $mine ? 'bg-teal-50 border-teal-100' : 'bg-slate-50 border-slate-200' }}">
              <div class="flex items-start justify-between gap-3">
                <p class="font-extrabold text-slate-900 text-[13px]">
                  {{ $mine ? 'Kamu' : 'Freelancer' }}
                </p>
                <p class="text-slate-400 text-[11px] font-bold">
                  {{ optional($n->created_at)->format('d M Y H:i') }}
                </p>
              </div>
              <p class="text-slate-700 mt-2 text-[13.5px] whitespace-pre-line">{{ $n->message ?? '-' }}</p>
            </div>
          @empty
            @include('dashboard.client._ui.empty', [
              'icon' => 'ri-message-3-line',
              'title' => 'Belum ada pesan',
              'desc' => 'Mulai diskusi dengan mengirim pesan di bawah.'
            ])
          @endforelse
        </div>

            <form action="{{ route('client.messages.send') }}" class="mt-5 flex flex-col sm:flex-row gap-3">
              @csrf
              <input type="hidden" name="order_id" value="{{ $order->id }}"/>
              <input type="text" name="message"
                     class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200 focus:border-[#0f766e] focus:ring-2 focus:ring-[#0f766e]/20 outline-none transition-all
                     @error('message') input-error @enderror"
                     placeholder="Tulis pesan...">
              <button type="submit" class="px-5 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
                Kirim
              </button>
            </form>
            @error('message')
              <p class="text-red-600 text-[12px] font-bold mt-2">{{ $message }}</p>
            @enderror
      </div>

      {{-- REVISION REQUEST (show when order is In Progress only) --}}
      @if($order->status === 'In Progress')
      <div x-data="{ showRevision: false }" class="bg-white border border-slate-200 rounded-[18px] p-6">
        <div class="flex items-center justify-between gap-3 mb-4">
          <div>
            <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem] flex items-center gap-2">
              <i class="ri-refresh-line text-amber-500"></i>
              Permintaan Revisi
            </h2>
            <p class="text-slate-500 text-[13.5px] mt-1">Tidak puas dengan hasil? Ajukan revisi.</p>
          </div>
          @if($order->status !== 'Revision')
          <button @click="showRevision = true" class="px-4 py-2.5 rounded-[12px] bg-amber-50 border border-amber-200 text-amber-700 font-bold text-[13px] hover:bg-amber-100 transition-all">
            <i class="ri-add-line mr-1"></i> Ajukan Revisi
          </button>
          @else
          <span class="px-3 py-1.5 rounded-full text-[12px] font-bold bg-amber-100 text-amber-700">
            Revisi Diproses
          </span>
          @endif
        </div>

        {{-- Revision Modal --}}
        <div x-show="showRevision" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div @click="showRevision = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
          <div class="relative w-full max-w-lg bg-white rounded-[24px] shadow-xl p-6 sm:p-8">
            <button @click="showRevision = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
              <i class="ri-close-line"></i>
            </button>
            <h2 class="font-display text-xl font-bold text-slate-900 mb-1">Ajukan Revisi</h2>
            <p class="text-sm text-slate-500 mb-5">Jelaskan bagian yang perlu direvisi</p>
            
            <form action="{{ route('client.orders.revision', $order->id) }}" method="POST" class="space-y-4">
              @csrf
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Alasan Revisi <span class="text-red-500">*</span></label>
                <textarea name="reason" rows="3" required class="w-full px-4 py-3 rounded-[12px] border border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 outline-none text-sm" placeholder="Bagian yang ingin direvisi..."></textarea>
              </div>
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Detail Tambahan</label>
                <textarea name="description" rows="2" class="w-full px-4 py-3 rounded-[12px] border border-slate-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 outline-none text-sm" placeholder="Detail tambahan..."></textarea>
              </div>
              <div class="flex gap-3 pt-2">
                <button type="button" @click="showRevision = false" class="flex-1 py-3 rounded-[12px] bg-slate-100 text-slate-600 font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-[12px] bg-amber-500 text-white font-bold text-sm hover:bg-amber-600">Kirim Revisi</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      @endif

      {{-- Review --}}
      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem]">Review</h2>
        <p class="text-slate-500 text-[13.5px] mt-1">Berikan rating setelah pekerjaan selesai.</p>

        @if(!empty($order->review))
          <div class="mt-4 rounded-[16px] border border-slate-200 bg-slate-50 p-5">
            <div class="flex items-center justify-between mb-2">
              <p class="font-extrabold text-slate-900">Kamu sudah memberi review.</p>
              <form action="{{ route('client.reviews.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus review ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-[11px] font-bold hover:bg-red-100 transition-all">
                  <i class="ri-delete-bin-line mr-1"></i> Hapus
                </button>
              </form>
            </div>
            <p class="text-slate-600 text-[13.5px] mt-2">
              Rating: <span class="font-extrabold">{{ $order->review->rating ?? '-' }}</span>
            </p>
            <p class="text-slate-600 text-[13.5px] mt-1">
              "{{ $order->review->comment ?? '-' }}"
            </p>
          </div>
        @else
          <form method="POST" action="{{ route('client.reviews.store') }}" class="mt-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}"/>

            <select name="rating" class="px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200">
              <option value="">Rating</option>
              @for($i=5;$i>=1;$i--) <option value="{{ $i }}">{{ $i }}</option> @endfor
            </select>

            <input name="comment" class="sm:col-span-3 px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200"
                   placeholder="Komentar singkat...">

            <div class="sm:col-span-4">
              <button class="px-5 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
                Kirim Review
              </button>
            </div>
          </form>
        @endif
      </div>
    </div>

    {{-- Sidebar kanan --}}
    <aside class="w-full lg:w-[360px] shrink-0 space-y-6">
      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <h3 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Freelancer</h3>

        <div class="flex items-start gap-3 mt-4">
          <div class="w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400">
            <i class="ri-user-3-line text-[20px]"></i>
          </div>
          <div class="min-w-0">
            <p class="font-extrabold text-slate-900 truncate">
              {{ optional(optional(optional($order->service)->freelancer)->skomda_student)->name ?? 'Freelancer' }}
            </p>
            <p class="text-slate-500 text-[13px] mt-1 line-clamp-3">
              {{ optional(optional($order->service)->freelancer)->bio ?? 'Belum ada bio.' }}
            </p>
          </div>
        </div>

        <div class="mt-5 pt-5 border-t border-slate-100 space-y-3">
          @if($order->status === 'Negotiated')
          <a href="{{ route('client.orders.checkout', $order->id) }}"
             class="w-full inline-flex items-center justify-center px-5 py-3 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all">
            Bayar Sekarang <i class="ri-bank-card-line ml-2"></i>
          </a>
          @endif
          <a href="{{ route('client.services.show', $order->service_id) }}"
             class="w-full inline-flex items-center justify-center px-5 py-3 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
                    hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
            Lihat Jasa <i class="ri-external-link-line ml-2"></i>
          </a>
          <a href="{{ route('client.payments.index') }}"
             class="w-full inline-flex items-center justify-center px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
            Ke Payments <i class="ri-bank-card-line ml-2"></i>
          </a>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <h3 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Transaksi</h3>
        <p class="text-slate-500 text-[13.5px] mt-1">Ringkasan transaksi terkait order ini.</p>

        <div class="mt-4 space-y-3">
          @forelse(($order->transactions ?? []) as $t)
            <div class="rounded-[16px] border border-slate-200 bg-slate-50 p-4">
              <p class="font-extrabold text-slate-900 text-[13px]">Transaction #{{ $t->id }}</p>
              <p class="text-slate-500 text-[12px] mt-1">{{ optional($t->created_at)->format('d M Y H:i') }}</p>
              @if(isset($t->status))
                <div class="mt-2">
                  @include('dashboard.client._ui.status-badge', ['status' => $t->status])
                </div>
              @endif
            </div>
          @empty
            @include('dashboard.client._ui.empty', [
              'icon' => 'ri-bank-card-line',
              'title' => 'Belum ada transaksi',
              'desc' => 'Jika transaksi dibuat, akan muncul di sini.'
            ])
          @endforelse
        </div>
      </div>
    </aside>
  </div>
</section>
@endsection