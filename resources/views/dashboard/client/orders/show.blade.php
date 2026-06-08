@extends('layouts.dashboard')
@section('title', 'Detail Order')

@section('styles')
  <style>
    [x-cloak] {
      display: none !important
    }

    details>summary {
      list-style: none;
    }

    details>summary::-webkit-details-marker {
      display: none;
    }
  </style>
@endsection

@section('content')
   <section class="animate-fadeUp">
    <div class="flex flex-col lg:flex-row gap-6">
      <div class="flex-1 min-w-0 space-y-6">

        {{-- Header --}}
        <div class="bg-white border border-slate-200 rounded-[18px] p-6">
          <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
              <a href="{{ route('client.orders.index') }}"
                class="text-slate-500 font-bold text-[13px] hover:text-slate-900">
                <i class="ri-arrow-left-line mr-1"></i> Kembali
              </a>
              <h1 class="font-display text-[1.65rem] font-extrabold text-slate-900 mt-2">Order #{{ $order->id }}</h1>
              <p class="text-slate-500 mt-1 text-[13.5px]">
                @if($order->lokerApplication)
                  <span class="font-bold text-indigo-600">Lowongan:
                    {{ $order->brief ? \Illuminate\Support\Str::limit($order->brief, 50) : '-' }}</span>
                @else
                  Jasa: <span class="font-bold">{{ $order->service?->title ?? '-' }}</span>
                @endif
              </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
              <x-ui.status-badge :status="$order->status ?? '-'" />
              <span
                class="px-3 py-1 rounded-full text-[12px] font-extrabold bg-white text-slate-700 border border-slate-200">
                Agreed: Rp{{ number_format((float) ($order->agreed_price ?? 0), 0, ',', '.') }}
              </span>
              <span class="px-3 py-1 rounded-full text-[12px] font-extrabold bg-white text-slate-700 border border-slate-200">
                Deadline: {{ $order->deadline ? \Carbon\Carbon::parse($order->deadline)->translatedFormat('d M Y') : '-' }}
              </span>
            </div>
          </div>

          {{-- Tracking stepper --}}
          @php
            $raw = (string) ($order->status ?? 'Pending');
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
            foreach ($steps as $i => $st) {
              if ($st['key'] === $norm) {
                $currentIndex = $i;
                break;
              }
            }

            $isCancelled = ($norm === 'cancelled');
          @endphp

          <div class="mt-6">
            <p class="text-slate-400 text-[12px] font-extrabold uppercase tracking-widest mb-3">Tracking</p>

            @if($isCancelled)
              <div class="rounded-[16px] border border-rose-100 bg-rose-50 p-4">
                <p class="font-extrabold text-rose-700">Order dibatalkan</p>
                <p class="text-rose-600 text-[13px] mt-1">Status terakhir:
                  {{ \Illuminate\Support\Str::headline($order->status) }}
                </p>
              </div>
            @else
              <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($steps as $i => $st)
                  @php
                    $done = $i < $currentIndex;
                    $active = $i === $currentIndex;
                  @endphp
                  <div
                    class="rounded-[16px] border p-4 {{ $active ? 'border-teal-200 bg-teal-50' : 'border-slate-200 bg-white' }}">
                    <div class="flex items-start justify-between gap-3">
                      <div>
                        <p class="font-extrabold text-slate-900 text-[13px]">{{ $st['label'] }}</p>
                        <p class="text-slate-500 text-[12px] mt-1">{{ $st['desc'] }}</p>
                      </div>
                      @if($done)
                        <span class="text-emerald-600"><i class="ri-check-line"></i></span>
                      @elseif($active)
                        <span
                          class="text-teal-700 font-extrabold text-[11px] px-2.5 py-1 rounded-full bg-white border border-teal-200">Now</span>
                      @else
                        <span class="text-slate-300"><i class="ri-circle-line"></i></span>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>

          @php
            $nextAction = match ($order->status) {
              'Pending' => 'Tunggu freelancer memberi harga atau kirim negosiasi jika perlu.',
              'Negotiated' => 'Terima harga untuk lanjut ke pembayaran, atau kirim negosiasi baru.',
              'Paid' => 'Freelancer mulai mengerjakan pesanan.',
              'In Progress' => $order->results->count() > 0
              ? 'Freelancer sudah mengirim hasil. Kamu bisa menerima hasil atau ajukan revisi.'
              : 'Tunggu freelancer mengirim hasil kerja.',
              'Revision' => 'Freelancer sedang meninjau permintaan revisi kamu.',
              'Completed' => $order->review ? 'Order selesai dan sudah direview.' : 'Order selesai. Kamu masih bisa memberi review.',
              'Cancelled' => 'Order ini sudah dibatalkan.',
              default => 'Pantau perkembangan order dari panel ini.',
            };
          @endphp

          <div class="mt-5 rounded-[16px] border border-slate-200 bg-slate-50 p-4">
            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Langkah Berikutnya</p>
            <p class="text-[13px] text-slate-700 leading-relaxed">{{ $nextAction }}</p>
          </div>
        </div>

        {{-- ACTION: Pending (from loker approval) --}}
        @if($order->status === 'Pending' && $order->lokerApplication && $order->agreed_price)
          <div x-data="{ isSubmitting: false }" class="bg-white border border-slate-200 rounded-[18px] p-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
              <div>
                <h3 class="font-display font-extrabold text-slate-900 text-[1.1rem]">Harga Fluancer</h3>
                <p class="text-2xl font-black text-[#0f766e] mt-1">Rp{{ number_format($order->agreed_price, 0, ',', '.') }}
                </p>
                <p class="text-[12px] text-slate-500 mt-1">Dari lamaran lowongan:
                  {{ optional($order->lokerApplication->freelancer->skomda_student)->name ?? 'Freelancer' }}
                </p>
              </div>
              <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('client.orders.reject', $order->id) }}" method="POST">
                  @csrf
                  <button type="submit"
                    class="px-5 py-3 rounded-[12px] bg-white border border-red-200 text-red-600 font-bold text-[13px] hover:bg-red-50 transition-all">
                    <i class="ri-close-line mr-1"></i> Tolak
                  </button>
                </form>
                <form action="{{ route('client.orders.accept', $order->id) }}" method="POST" @submit="isSubmitting = true">
                  @csrf
                  <button type="submit" :disabled="isSubmitting"
                    onclick="return confirm('Terima dan lanjut ke pembayaran?')"
                    class="px-5 py-3 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all disabled:opacity-50">
                    <i class="ri-check-line mr-1"></i> Terima & Bayar
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endif

        {{-- ACTION: Negotiated (Melengkapi Tombol Trigger Modal & Pembayaran Sukses) --}}
        @if($order->status === 'Negotiated' && $order->agreed_price)
          <div x-data="{ showNego: false, showReject: false, isSubmitting: false, isSubmittingNego: false }"
            class="bg-white border border-slate-200 rounded-[18px] p-6">

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
              <div>
                <h3 class="font-display font-extrabold text-slate-900 text-[1.1rem]">Penawaran Harga Hasil Negosiasi</h3>
                <p class="text-2xl font-black text-[#0f766e] mt-1">Rp{{ number_format($order->agreed_price, 0, ',', '.') }}
                </p>
                <p class="text-[12px] text-slate-500 mt-1">Silakan lakukan pelunasan untuk memulai project, atau ajukan
                  negosiasi ulang.</p>
              </div>
              <div class="flex flex-wrap gap-2.5 w-full sm:w-auto justify-end">
                <button type="button" @click="showReject = true"
                  class="px-4 py-2.5 rounded-[12px] bg-white border border-red-200 text-red-600 font-bold text-[12.5px] hover:bg-red-50 transition-all flex items-center gap-1.5">
                  <i class="ri-close-line"></i> Tolak
                </button>
                <button type="button" @click="showNego = true"
                  class="px-4 py-2.5 rounded-[12px] bg-white border border-amber-200 text-amber-600 font-bold text-[12.5px] hover:bg-amber-50 transition-all flex items-center gap-1.5">
                  <i class="ri-exchange-line"></i> Negosiasi
                </button>
                <a href="{{ route('client.orders.checkout', $order->id) }}"
                  class="px-5 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[12.5px] hover:bg-[#0a5e58] transition-all flex items-center gap-1.5">
                  <i class="ri-wallet-3-line"></i> Bayar Sekarang
                </a>
              </div>
            </div>
            {{-- Modal Negosiasi (Client) --}}
            <div x-show="showNego"
              x-init="$watch('showNego', value => { if(value) { $nextTick(() => window.DigitalanceUtils.focusTrap($el)) } })"
              x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog" aria-modal="true">
              <div @click="showNego = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
              <div class="relative w-full max-w-lg bg-white rounded-[24px] shadow-xl p-6 sm:p-8">
                <button @click="showNego = false"
                  class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500"
                  aria-label="Tutup modal">
                  <i class="ri-close-line"></i>
                </button>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-1">Ajukan Negosiasi</h2>
                <form action="{{ route('client.orders.nego', $order->id) }}" method="POST" class="space-y-4"
                  @submit.prevent="isSubmittingNego = true; $el.submit();">
                  @csrf
                  <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Alasan <span
                        class="text-red-500">*</span></label>
                    <textarea name="reason" rows="2" required
                      class="w-full px-4 py-3 rounded-[12px] border border-slate-200 focus:border-amber-400 outline-none text-sm"
                      placeholder="Misal: budget saya masih di bawah harga tersebut..."></textarea>
                  </div>
                  <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Harga Baru <span
                        class="text-red-500">*</span></label>
                    <div class="relative">
                      <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-semibold">Rp</span>
                      <input type="text" name="new_price" required data-rupiah-input inputmode="numeric"
                        class="w-full pl-10 pr-4 py-3 rounded-[12px] border border-slate-200 focus:border-amber-400 outline-none text-sm font-semibold"
                        placeholder="Masukkan angka yang kamu mau">
                    </div>
                  </div>
                  <div class="flex gap-3 pt-2">
                    <button type="button" @click="showNego = false"
                      class="flex-1 py-3 rounded-[12px] bg-slate-100 text-slate-600 font-bold text-sm">Batal</button>
                    <button type="submit" :disabled="isSubmittingNego"
                      class="flex-1 py-3 rounded-[12px] bg-amber-500 text-white font-bold text-sm hover:bg-amber-600 disabled:opacity-50">
                      <span x-show="!isSubmittingNego">Kirim</span>
                      <span x-show="isSubmittingNego"><i class="ri-loader-4-line animate-spin"></i></span>
                    </button>
                  </div>
                </form>
              </div>
            </div>

            {{-- Modal Tolak --}}
            <div x-show="showReject"
              x-init="$watch('showReject', value => { if(value) { $nextTick(() => window.DigitalanceUtils.focusTrap($el)) } })"
              x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog" aria-modal="true">
              <div @click="showReject = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
              <div class="relative w-full max-w-md bg-white rounded-[20px] shadow-xl p-6">
                <h3 class="font-display text-lg font-bold text-slate-900 mb-2 text-center">Tolak Pesanan?</h3>
                <form action="{{ route('client.orders.reject', $order->id) }}" method="POST" class="space-y-4"
                  @submit="isSubmitting = true">
                  @csrf
                  <textarea name="reason" rows="3" required
                    class="w-full px-4 py-3 rounded-[12px] border border-slate-200 text-sm"
                    placeholder="Alasan menolak..."></textarea>
                  <div class="flex gap-3">
                    <button type="button" @click="showReject = false"
                      class="flex-1 py-2.5 rounded-[12px] bg-slate-100 text-slate-600 font-bold text-sm">Batal</button>
                    <button type="submit" :disabled="isSubmitting"
                      class="flex-1 py-2.5 rounded-[12px] bg-red-600 text-white font-bold text-sm hover:bg-red-700 disabled:opacity-50">
                      <span x-show="!isSubmitting">Ya, Tolak</span>
                      <span x-show="isSubmitting"><i class="ri-loader-4-line animate-spin"></i></span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endif

        {{-- Brief Section --}}
        <div class="bg-white border border-slate-200 rounded-[18px] p-6">
          <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem]">Brief</h2>
          <p class="text-slate-600 text-[14px] mt-3 whitespace-pre-line">{{ $order->brief ?? '-' }}</p>

          @if($order->attachments->count() > 0)
            <div class="mt-6 pt-5 border-t border-slate-100">
              <p class="text-slate-400 text-[12px] font-extrabold uppercase tracking-widest mb-3">Lampiran
                ({{ $order->attachments->count() }})</p>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($order->attachments as $att)
                  <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50">
                    @if(in_array($att->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']))
                      <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ $att->file_name }}"
                          class="w-full h-28 object-cover hover:opacity-90 transition-opacity">
                      </a>
                    @else
                      <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank"
                        class="flex flex-col items-center justify-center h-28 p-2 text-center">
                        <i class="ri-file-line text-2xl text-slate-400 mb-1"></i>
                        <span class="text-[9px] text-slate-500 truncate w-full">{{ $att->file_name }}</span>
                      </a>
                    @endif
                    <div class="px-2 py-1 bg-white border-t border-slate-100">
                      <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank"
                        class="text-[9px] font-bold text-[#0f766e] hover:underline truncate block">{{ $att->file_name }}</a>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

          <div class="mt-6 pt-5 border-t border-slate-100">
            <p class="text-slate-400 text-[12px] font-extrabold uppercase tracking-widest mb-2">Upload Attachment</p>
            <form method="POST" action="{{ route('client.orders.attachments.store', $order->id) }}"
              enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3">
              @csrf
              <input type="file" name="file[]" multiple accept="image/*,.pdf,.zip,.doc,.docx,.rar"
                class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200" />
              <button
                class="px-5 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
                Upload
              </button>
            </form>
          </div>

          @if($order->results->count() > 0)
            <div class="mt-6 pt-5 border-t border-slate-100">
              <p class="text-slate-400 text-[12px] font-extrabold uppercase tracking-widest mb-3">Hasil Freelancer</p>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($order->results as $result)
                  <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-start justify-between gap-3">
                      <div>
                        <p class="font-extrabold text-slate-900">{{ $result->version ?? 'Version' }}</p>
                        <p class="text-[12px] text-slate-500 mt-1">{{ optional($result->created_at)->format('d M Y, H:i') }}</p>
                      </div>
                      <a href="{{ route('client.results.show', $result->id) }}" class="text-[#0f766e] text-[12px] font-bold hover:underline">Detail</a>
                    </div>
                    @if($result->file_url)
                      <a href="{{ $result->downloadUrl() }}" target="_blank" rel="noopener noreferrer"
                        class="mt-3 inline-flex items-center gap-2 text-[12px] font-semibold text-slate-600 hover:text-[#0f766e]">
                        <i class="{{ $result->fileIcon() }}"></i> {{ $result->fileActionLabel() }}
                      </a>
                    @endif
                  </div>
                @endforeach
              </div>
            </div>
          @endif
        </div>

        {{-- Messages / Negotiation --}}
        <div class="bg-white border border-slate-200 rounded-[18px] p-6">
          <div class="flex items-end justify-between gap-3">
            <div>
              <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem]">Messages / Negosiasi</h2>
              <p class="text-slate-500 text-[13.5px] mt-1">Diskusi detail, nego harga, revisi, dll.</p>
            </div>
            <a href="{{ route('client.messages.index') }}" class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px]
                                    hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
              Inbox
            </a>
          </div>

          <div class="mt-4 space-y-3">
            @forelse(($order->negotiations ?? []) as $n)
              @php $mine = (($n->sender ?? '') === 'client'); @endphp
              <div
                class="rounded-[16px] border p-4 {{ $mine ? 'bg-teal-50 border-teal-100' : 'bg-slate-50 border-slate-200' }}">
                <div class="flex items-start justify-between gap-3">
                  <p class="font-extrabold text-slate-900 text-[13px]">{{ $mine ? 'Kamu' : 'Freelancer' }}</p>
                  <p class="text-slate-400 text-[11px] font-bold">{{ optional($n->created_at)->format('d M Y H:i') }}</p>
                </div>
                <p class="text-slate-700 mt-2 text-[13.5px] whitespace-pre-line">{{ $n->message ?? '-' }}</p>
              </div>
            @empty
              <x-ui.empty-state icon="ri-message-3-line" title="Belum ada pesan"
                description="Mulai diskusi dengan mengirim pesan di bawah." />
            @endforelse
          </div>

          <form action="{{ route('client.messages.send') }}" method="POST" class="mt-5 flex flex-col sm:flex-row gap-3">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}" />
            <input type="text" name="message"
              class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200 focus:border-[#0f766e] outline-none"
              placeholder="Tulis pesan...">
            <button type="submit"
              class="px-5 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
              Kirim
            </button>
          </form>
        </div>

        {{-- Revision --}}
        @if($order->status === 'In Progress')
          <div x-data="{ showRevision: false, isSubmitting: false }"
            class="bg-white border border-slate-200 rounded-[18px] p-6">
            <div class="flex items-center justify-between gap-3 mb-4">
              <div>
                <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem] flex items-center gap-2">
                  <i class="ri-refresh-line text-amber-500"></i> Permintaan Revisi
                </h2>
                <p class="text-slate-500 text-[13.5px] mt-1">Tidak puas dengan hasil? Ajukan revisi.</p>
              </div>
              <button @click="showRevision = true"
                class="px-4 py-2.5 rounded-[12px] bg-amber-50 border border-amber-200 text-amber-700 font-bold text-[13px] hover:bg-amber-100 transition-all"
                aria-label="Ajukan revisi">
                <i class="ri-add-line mr-1"></i> Ajukan Revisi
              </button>
            </div>

            <div x-show="showRevision"
              x-init="$watch('showRevision', value => { if(value) { $nextTick(() => window.DigitalanceUtils.focusTrap($el)) } })"
              x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog" aria-modal="true">
              <div @click="showRevision = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
              <div class="relative w-full max-w-lg bg-white rounded-[24px] shadow-xl p-6 sm:p-8">
                <button @click="showRevision = false"
                  class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500"
                  aria-label="Tutup modal"><i class="ri-close-line"></i></button>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-1">Ajukan Revisi</h2>
                <form action="{{ route('client.orders.revision', $order->id) }}" method="POST" class="space-y-4"
                  @submit="isSubmitting = true">
                  @csrf
                  <textarea name="reason" rows="3" required
                    class="w-full px-4 py-3 rounded-[12px] border border-slate-200 text-sm"
                    placeholder="Bagian yang ingin direvisi..."></textarea>
                  <div class="flex gap-3">
                    <button type="button" @click="showRevision = false"
                      class="flex-1 py-3 rounded-[12px] bg-slate-100 text-slate-600 font-bold text-sm">Batal</button>
                    <button type="submit" :disabled="isSubmitting"
                      class="flex-1 py-3 rounded-[12px] bg-amber-500 text-white font-bold text-sm hover:bg-amber-600 disabled:opacity-50">
                      <span x-show="!isSubmitting">Kirim Revisi</span>
                      <span x-show="isSubmitting"><i class="ri-loader-4-line animate-spin"></i></span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endif

        @if($order->status === 'In Progress')
          <div class="bg-white border border-slate-200 rounded-[18px] p-6">
            <div class="flex items-center justify-between gap-3 mb-4">
              <div>
                <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem] flex items-center gap-2">
                  <i class="ri-checkbox-circle-line text-emerald-500"></i> Finalisasi Hasil
                </h2>
                <p class="text-slate-500 text-[13.5px] mt-1">Terima hasil kerja jika sudah sesuai dan pesanan akan ditutup.
                </p>
              </div>
            </div>

            @if($order->results->count() > 0)
              <form action="{{ route('client.orders.complete', $order->id) }}" method="POST"
                onsubmit="event.preventDefault(); customConfirm('Terima hasil kerja dan tandai order sebagai selesai?').then(res => { if(res) this.submit(); });">
                @csrf
                <button type="submit"
                  class="px-5 py-3 rounded-[12px] bg-emerald-600 text-white font-bold text-[13px] hover:bg-emerald-700 transition-all">
                  <i class="ri-check-line mr-1"></i> Terima & Selesaikan Order
                </button>
              </form>
            @else
              <div class="rounded-[16px] border border-amber-100 bg-amber-50 p-4">
                <p class="font-extrabold text-amber-700 text-[13px]">Belum ada hasil kerja untuk diterima.</p>
                <p class="text-amber-600 text-[12.5px] mt-1">Tunggu freelancer mengirim hasil sebelum menutup order.</p>
              </div>
            @endif
          </div>
        @endif

        {{-- Review Section --}}
        <div class="bg-white border border-slate-200 rounded-[18px] p-6">
          <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem]">Review</h2>
          @if(!empty($order->review))
            <div class="mt-4 rounded-[16px] border border-slate-200 bg-slate-50 p-5">
              <p class="font-extrabold text-slate-900">Rating: {{ str_repeat('★', (int) ($order->review->rating ?? 0)) }}</p>
              <p class="text-slate-600 mt-2 italic">"{{ $order->review->comment ?? '-' }}"</p>
            </div>
          @elseif($order->status === 'Completed')
            <form method="POST" action="{{ route('client.reviews.store') }}" class="mt-4 space-y-4"
              x-data="{ rating: {{ (int) old('rating', 5) }} }">
              @csrf
              <input type="hidden" name="order_id" value="{{ $order->id }}" />
              <div class="flex flex-col gap-2">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Rating</label>
                <div class="flex flex-wrap gap-2">
                  @for($i = 5; $i >= 1; $i--)
                    <label class="relative cursor-pointer">
                      <input type="radio" name="rating" value="{{ $i }}" x-model.number="rating"
                        class="absolute opacity-0 pointer-events-none" {{ $i === 5 ? 'checked' : '' }}>
                      <span
                        class="inline-flex items-center gap-1 px-4 py-2 rounded-[12px] border font-bold text-[13px] transition-all"
                        :class="rating === {{ $i }} ? 'bg-amber-50 text-amber-700 border-amber-200 shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:border-amber-200 hover:text-amber-600'">
                        {{ str_repeat('★', $i) }}
                      </span>
                    </label>
                  @endfor
                </div>
              </div>
              <textarea name="comment" rows="3" class="w-full px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200"
                placeholder="Komentar singkat..."></textarea>
              <p class="text-[12px] text-amber-600 font-semibold">Review hanya bisa diisi setelah order selesai.</p>
              <button
                class="px-6 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">Kirim
                Ulas</button>
            </form>
          @else
            <div class="mt-4 rounded-[16px] border border-amber-100 bg-amber-50 p-4 text-amber-700 text-[13px] font-semibold">
              Review baru bisa diisi ketika order berstatus selesai.
            </div>
          @endif
        </div>
      </div>

      {{-- Sidebar --}}
      <aside class="w-full lg:w-[360px] shrink-0 space-y-6">
        <div class="bg-white border border-slate-200 rounded-[18px] p-6">
          <h3 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Freelancer</h3>
          <div class="flex items-center gap-3 mt-4">
            <div
              class="w-12 h-12 rounded-2xl bg-[#0f766e]/10 border border-[#0f766e]/20 flex items-center justify-center text-[#0f766e]">
              <i class="ri-user-3-line text-[20px]"></i>
            </div>
            <div class="min-w-0">
              <p class="font-bold text-slate-900 text-[14px] truncate">
                @if($order->freelancer)
                  {{ optional($order->freelancer->skomda_student)->name ?? 'Freelancer' }}
                @else
                  {{ optional(optional($order->service?->freelancer)->skomda_student)->name ?? 'Freelancer' }}
                @endif
              </p>
              <p class="text-slate-500 text-[12px] font-bold">
                @if($order->lokerApplication)
                  <span class="text-indigo-600">Dari Lamaran Lowongan</span>
                @else
                  Verified Professional
                @endif
              </p>
            </div>
          </div>

          <div class="mt-6 pt-5 border-t border-slate-100 space-y-3">
            @if($order->freelancer)
              <a href="{{ route('client.talents.show', ['freelancer' => $order->freelancer_id, 'return_to' => route('client.orders.show', $order->id)]) }}"
                class="block w-full px-4 py-3 rounded-[12px] bg-slate-50 border border-slate-200 text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
                Lihat Profil
              </a>
            @elseif($order->service?->freelancer_id)
              <a href="{{ route('client.talents.show', ['freelancer' => $order->service->freelancer_id, 'return_to' => route('client.orders.show', $order->id)]) }}"
                class="block w-full px-4 py-3 rounded-[12px] bg-slate-50 border border-slate-200 text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
                Lihat Profil
              </a>
            @endif
            <a href="{{ route('client.messages.index') }}"
              class="block w-full px-4 py-3 rounded-[12px] bg-[#0f766e] text-white font-bold text-[12.5px] hover:bg-[#0a5e58] transition-all text-center">
              Chat Freelancer
            </a>
          </div>
        </div>
      </aside>
    </div>
  </section>
@endsection
