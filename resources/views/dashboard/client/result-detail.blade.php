@extends('layouts.dashboard')
@section('title', 'Detail Hasil | Digitalance')

@section('content')
  <section class="animate-fadeUp">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
      <div>
        <a href="{{ route('client.results.index') }}"
          class="text-slate-500 font-bold text-[13px] hover:text-slate-900 inline-flex items-center gap-1 mb-2">
          <i class="ri-arrow-left-line"></i> Kembali
        </a>
        <h1 class="font-display text-[1.65rem] font-extrabold text-slate-900">Detail Hasil</h1>
        <p class="text-slate-500 mt-1">Order #{{ $result->order_id }}</p>
      </div>
      <x-ui.status-badge :status="$result->order->status ?? '-'" class="px-3 py-1.5 rounded-lg text-[12px] uppercase" />
    </div>

    <div class="max-w-2xl space-y-6">
      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem] mb-4">Informasi Hasil</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Versi</p>
            <p class="font-extrabold text-slate-900 text-[15px]">{{ $result->version ?? '-' }}</p>
          </div>
          <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tanggal Kirim</p>
            <p class="font-extrabold text-slate-900 text-[15px]">
              {{ $result->created_at->timezone(config('app.timezone'))->format('d M Y, H:i') }} WIB</p>
          </div>
        </div>

        @if($result->note)
          <div class="mt-4 bg-slate-50 rounded-xl p-5">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Catatan</p>
            <p class="text-slate-700 text-[14px] leading-relaxed">{{ $result->note }}</p>
          </div>
        @endif
      </div>

      @if($result->file_url)
        <div class="bg-white border border-slate-200 rounded-[18px] p-6">
          <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem] mb-4">File Hasil</h2>
          @php
            $resultIsLink = $result->isExternalLink();
            $resultFileUrl = $result->downloadUrl();
            $resultMime = (!$resultIsLink && $result->file_url && file_exists(storage_path('app/public/' . $result->file_url))) ? mime_content_type(storage_path('app/public/' . $result->file_url)) : null;
          @endphp
          <div class="rounded-xl border border-blue-100 bg-blue-50 overflow-hidden">
            @if($resultIsLink)
              <div class="p-5">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Link Hasil</p>
                <a href="{{ $resultFileUrl }}" target="_blank"
                  rel="noopener noreferrer"
                  class="text-[#0f766e] font-bold hover:underline break-all">{{ $result->fileLabel() }}</a>
              </div>
            @elseif(in_array($resultMime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']))
              <a href="{{ $resultFileUrl }}" target="_blank" class="block">
                <img src="{{ $resultFileUrl }}" alt="{{ $result->version ?? 'Result file' }}"
                  class="w-full max-h-[420px] object-cover">
              </a>
            @elseif($resultMime === 'application/pdf')
              <iframe src="{{ $resultFileUrl }}" class="w-full h-[520px] border-0"></iframe>
            @else
              <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                    <i class="{{ $result->fileIcon() }}"></i>
                  </div>
                  <div>
                    <p class="text-[13px] font-bold text-slate-900">{{ $result->fileLabel() }}</p>
                    <p class="text-[11px] text-slate-500">Tersedia untuk diunduh</p>
                  </div>
                </div>
                <a href="{{ $resultFileUrl }}" target="_blank"
                  rel="noopener noreferrer"
                  class="px-4 py-2 bg-white text-blue-600 border border-blue-200 rounded-lg font-bold text-[12px] hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                  {{ $result->fileActionLabel() }}
                </a>
              </div>
            @endif
          </div>
        </div>
      @endif

      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem] mb-4">Layanan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Layanan</p>
            <p class="font-bold text-slate-900">{{ $result->order->service->title ?? '-' }}</p>
            <p class="text-slate-500 text-[12px] mt-0.5">{{ $result->order->service->category->name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Freelancer</p>
            <p class="font-bold text-slate-900">{{ $result->order->service->freelancer->skomda_student->name ?? '-' }}</p>
          </div>
        </div>
      </div>

      <div class="flex gap-3">
        <a href="{{ route('client.orders.show', $result->order_id) }}"
          class="flex-1 px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all text-center">
          Lihat Order
        </a>
        <a href="{{ route('client.results.index') }}" class="flex-1 px-5 py-3 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
                  hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
          Kembali
        </a>
      </div>
    </div>
  </section>
@endsection
