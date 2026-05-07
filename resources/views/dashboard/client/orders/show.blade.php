@extends('layouts.dashboard')
@section('title', 'Detail Order')

@section('content')
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

        <form method="POST" action="{{ route('client.messages.send') }}" class="mt-5 flex flex-col sm:flex-row gap-3">
          @csrf
          <input type="hidden" name="order_id" value="{{ $order->id }}"/>
          <input type="text" name="message"
                 class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-200"
                 placeholder="Tulis pesan...">
          <button class="px-5 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
            Kirim
          </button>
        </form>
        @error('message')
          <p class="text-red-600 text-[12px] font-bold mt-2">{{ $message }}</p>
        @enderror
      </div>

      {{-- Review --}}
      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem]">Review</h2>
        <p class="text-slate-500 text-[13.5px] mt-1">Berikan rating setelah pekerjaan selesai.</p>

        @if(!empty($order->review))
          <div class="mt-4 rounded-[16px] border border-slate-200 bg-slate-50 p-5">
            <p class="font-extrabold text-slate-900">Kamu sudah memberi review.</p>
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