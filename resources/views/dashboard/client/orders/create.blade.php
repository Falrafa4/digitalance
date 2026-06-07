@extends('layouts.dashboard')
@section('title', 'Buat Order')

@section('content')
  <div class="animate-fadeUp grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 bg-white border border-slate-200 h-fit rounded-[18px] p-6">
      <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900">Buat Order</h1>
      <p class="text-slate-500 mt-1">Isi brief kebutuhanmu.</p>

      {{-- PERBAIKAN TASK 6: Form ditambahkan atribut enctype untuk mendukung pengunggahan file lampiran berkas --}}
      <form method="POST" action="{{ route('client.orders.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4"
        id="order-form">
        @csrf
        <input type="hidden" name="service_id" value="{{ $service->id }}" />

        <div>
          <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Brief
            Detail Proyek</label>
          <textarea name="brief" rows="7" required class="w-full px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 focus:border-[#0f766e] outline-none transition-all
                    @error('brief') border-red-500 @enderror"
            placeholder="Jelaskan kebutuhan desain/fitur aplikasi kamu sespesifik mungkin..."
            aria-label="Brief detail">{{ old('brief') }}</textarea>
          @error('brief') <p class="text-red-600 text-[12px] font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label
            class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Deadline</label>
          <input type="date" name="deadline" value="{{ old('deadline') }}"
            class="w-full px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 focus:border-[#0f766e] outline-none transition-all @error('deadline') border-red-500 @enderror" />
          @error('deadline') <p class="text-red-600 text-[12px] font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Lampiran awal dapat diunggah langsung dan dipreview sebelum order dikirim --}}
        <div>
          <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5 ml-0.5">Dokumen
            Pendukung (Opsional)</label>
          <div
            class="border-2 border-dashed border-slate-200 rounded-[14px] p-4 text-center hover:border-[#0f766e] transition-all bg-slate-50/50 cursor-pointer relative"
            id="attachment-dropzone">
            <input type="file" name="attachments[]" id="attachment_file_input" multiple
              accept="image/*,.pdf,.zip,.doc,.docx,.rar" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
            <div id="file-upload-placeholder" class="space-y-1">
              <i class="ri-attachment-line text-2xl text-slate-400"></i>
              <p class="text-sm text-slate-600 font-bold">Pilih berkas lampiran</p>
              <p class="text-xs text-slate-400">PDF, ZIP, DOCX, PNG, JPG, RAR. Maksimal 10 file, 50MB per file.</p>
            </div>
            <div id="file-upload-preview" class="hidden mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-left"></div>
          </div>
        </div>

        <div class="flex pt-2">
          <button type="submit" id="order-submit-btn"
            class="px-8 py-3.5 rounded-[14px] bg-slate-900 text-white font-bold text-[14px] hover:bg-black hover:shadow-lg hover:shadow-slate-200 transition-all disabled:opacity-50 flex items-center justify-center gap-3 min-w-[160px]">
            <i class="ri-loader-4-line animate-spin text-lg hidden" id="order-spinner"></i>
            <span id="order-btn-text">Buat Order</span>
          </button>
        </div>
      </form>

      @push('scripts')
        <script>
          (function () {
            var form = document.getElementById('order-form');
            var btn = document.getElementById('order-submit-btn');
            var spinner = document.getElementById('order-spinner');
            var btnText = document.getElementById('order-btn-text');
            var fileInput = document.getElementById('attachment_file_input');
            var placeholder = document.getElementById('file-upload-placeholder');
            var preview = document.getElementById('file-upload-preview');
            var serviceSwitchLinks = document.querySelectorAll('[data-service-switch]');

            if (fileInput) {
              fileInput.addEventListener('change', function () {
                var files = this.files ? Array.from(this.files) : [];
                if (!files.length) {
                  placeholder.classList.remove('hidden');
                  preview.classList.add('hidden');
                  preview.innerHTML = '';
                  return;
                }

                placeholder.classList.add('hidden');
                preview.classList.remove('hidden');
                preview.innerHTML = files.map(function (file) {
                  return '<div class="flex items-center justify-between gap-2 px-3 py-2 rounded-xl bg-teal-50/70 border border-teal-100 text-[#0f766e] font-bold text-xs">' +
                    '<span class="truncate">' + file.name + '</span>' +
                    '<span class="text-slate-400 font-semibold">' + Math.ceil(file.size / 1024) + ' KB</span>' +
                    '</div>';
                }).join('');
              });
            }

            window.clearSelectedFile = function (e) {
              e.preventDefault();
              e.stopPropagation();
              fileInput.value = '';
              placeholder.classList.remove('hidden');
              preview.classList.add('hidden');
              preview.innerHTML = '';
            };

            if (form && btn) {
              form.addEventListener('submit', function (e) {
                btn.disabled = true;
                spinner.classList.remove('hidden');
                btnText.textContent = 'Memproses...';
              });
            }

            serviceSwitchLinks.forEach(function (link) {
              link.addEventListener('click', function (e) {
                var brief = form ? form.querySelector('[name="brief"]') : null;
                var hasBrief = brief && brief.value.trim().length > 0;
                var hasFiles = fileInput && fileInput.files && fileInput.files.length > 0;

                if (!hasBrief && !hasFiles) {
                  return;
                }

                e.preventDefault();

                var targetUrl = this.href;
                var message = 'Kamu sudah mulai mengisi brief atau memilih lampiran. Ganti jasa sekarang? Isian saat ini tidak akan tersimpan.';

                if (window.customConfirm) {
                  window.customConfirm(message).then(function (confirmed) {
                    if (confirmed) {
                      window.location.href = targetUrl;
                    }
                  });

                  return;
                }

                if (window.confirm(message)) {
                  window.location.href = targetUrl;
                }
              });
            });
          })();
        </script>
      @endpush
    </div>

    @php
      $serviceItems = $freelancerServices ?? collect([$service]);
      $freelancerName = optional(optional($service->freelancer)->skomda_student)->name ?? 'Talent';
    @endphp

    <aside class="bg-white border border-slate-200 rounded-[18px] p-6 xl:self-start">
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="text-slate-400 text-[12px] font-extrabold uppercase tracking-widest">Jasa</p>
          <h2 class="font-display font-extrabold text-slate-900 text-[1.2rem] mt-2">Jasa dari {{ $freelancerName }}</h2>
        </div>
        <span
          class="shrink-0 px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[11px] font-black uppercase tracking-wide">
          {{ $serviceItems->count() }} Jasa
        </span>
      </div>

      <div class="mt-5 space-y-3">
        @forelse($serviceItems as $item)
          @php
            $isSelected = (int) $item->id === (int) $service->id;
            $minPrice = $item->price_min ? 'Rp' . number_format((float) $item->price_min, 0, ',', '.') : null;
            $maxPrice = $item->price_max ? 'Rp' . number_format((float) $item->price_max, 0, ',', '.') : null;
            $priceLabel = $minPrice && $maxPrice
              ? $minPrice . ' - ' . $maxPrice
              : ($minPrice ?? ($maxPrice ? 'Sampai ' . $maxPrice : 'Harga menyesuaikan brief'));
            $deliveryLabel = $item->delivery_time ? $item->delivery_time . ' hari' : 'Estimasi menyusul';
          @endphp

          @if($isSelected)
            <div class="rounded-[16px] border border-[#0f766e]/30 bg-teal-50/60 p-4">
          @else
            <a href="{{ route('client.orders.create', $item->id) }}" data-service-switch
              class="block rounded-[16px] border border-slate-200 bg-white p-4 hover:border-[#0f766e] hover:shadow-md transition-all">
          @endif
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <p class="font-extrabold text-slate-900 leading-snug">{{ $item->title ?? 'Layanan' }}</p>
                  <p class="text-slate-500 text-[12.5px] mt-1">
                    {{ $item->category->name ?? '-' }}
                  </p>
                </div>

                @if($isSelected)
                  <span
                    class="shrink-0 px-2.5 py-1 rounded-full bg-white border border-teal-200 text-[#0f766e] text-[10.5px] font-black uppercase tracking-wide">
                    Dipilih
                  </span>
                @else
                  <i class="ri-arrow-right-up-line shrink-0 text-slate-300 text-lg"></i>
                @endif
              </div>

              <p class="text-slate-600 text-[13px] leading-relaxed mt-3 line-clamp-2">
                {{ $item->description ?? 'Belum ada deskripsi.' }}
              </p>

              <div class="mt-4 flex flex-wrap gap-2">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 text-[11.5px] font-bold">
                  <i class="ri-price-tag-3-line text-[#0f766e]"></i> {{ $priceLabel }}
                </span>
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 text-[11.5px] font-bold">
                  <i class="ri-time-line text-[#0f766e]"></i> {{ $deliveryLabel }}
                </span>
              </div>
          @if($isSelected)
            </div>
          @else
            </a>
          @endif
        @empty
          <div class="rounded-[16px] border border-dashed border-slate-200 bg-slate-50 p-5 text-slate-500 text-[13px]">
            Belum ada jasa lain yang tersedia.
          </div>
        @endforelse
      </div>
    </aside>
  </div>
@endsection
