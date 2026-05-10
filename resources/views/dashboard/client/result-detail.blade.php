@extends('layouts.dashboard')
@section('title', 'Detail Hasil | Digitalance')

@section('content')
<section class="animate-fadeUp">
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
    <div>
      <a href="{{ route('client.results.index') }}" class="text-slate-500 font-bold text-[13px] hover:text-slate-900 inline-flex items-center gap-1 mb-2">
        <i class="ri-arrow-left-line"></i> Kembali
      </a>
      <h1 class="font-display text-[1.65rem] font-extrabold text-slate-900">Detail Hasil</h1>
      <p class="text-slate-500 mt-1">Order #{{ $result->order_id }}</p>
    </div>
    @php
      $status = $result->order->status ?? '-';
      $statusClass = match($status) {
        'In Progress' => 'bg-blue-100 text-blue-700',
        'Revision' => 'bg-amber-100 text-amber-700',
        'Completed' => 'bg-emerald-100 text-emerald-700',
        default => 'bg-slate-100 text-slate-600'
      };
    @endphp
    <span class="px-3 py-1.5 rounded-lg text-[12px] font-bold uppercase {{ $statusClass }}">{{ $status }}</span>
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
          <p class="font-extrabold text-slate-900 text-[15px]">{{ $result->created_at->format('d M Y, H:i') }}</p>
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
      <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-100 rounded-xl">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
            <i class="ri-file-zip-line"></i>
          </div>
          <div>
            <p class="text-[13px] font-bold text-slate-900">File Terlampir</p>
            <p class="text-[11px] text-slate-500">Tersedia untuk diunduh</p>
          </div>
        </div>
        <a href="{{ asset('storage/' . $result->file_url) }}" target="_blank"
           class="px-4 py-2 bg-white text-blue-600 border border-blue-200 rounded-lg font-bold text-[12px] hover:bg-blue-600 hover:text-white transition-all shadow-sm">
          Unduh
        </a>
      </div>
    </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-[18px] p-6">
      <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem] mb-4">Layanan</h2>
      <p class="font-bold text-slate-900">{{ $result->order->service->title ?? '-' }}</p>
      <p class="text-slate-500 text-[13px] mt-1">{{ $result->order->service->service_category->name ?? '-' }}</p>
    </div>

    <div class="flex gap-3">
      <a href="{{ route('client.orders.show', $result->order_id) }}"
         class="flex-1 px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all text-center">
        Lihat Order
      </a>
      <a href="{{ route('client.results.index') }}"
         class="flex-1 px-5 py-3 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
                hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
        Kembali
      </a>
    </div>
  </div>
</section>
@endsection
