@extends('layouts.dashboard')
@section('title', 'Buat Order')

@section('content')
  <div class="animate-fadeUp grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 bg-white border border-slate-200 rounded-[18px] p-6">
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
          })();
        </script>
      @endpush
    </div>

    <div class="bg-white border border-slate-200 rounded-[18px] p-6">
      <p class="text-slate-400 text-[12px] font-extrabold uppercase tracking-widest">Jasa</p>
      <p class="font-extrabold text-slate-900 mt-2">{{ $service->title ?? '-' }}</p>
      <p class="text-slate-500 text-[13px] mt-1">Kategori: {{ $service->category->name ?? '-' }}</p>
    </div>
  </div>
@endsection