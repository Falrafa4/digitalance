@extends('layouts.dashboard')
@section('title', 'Proyek Saya')

@section('content')
  <section class="animate-fadeUp">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Proyek Saya</h1>
        <p class="text-slate-500 mt-1">Order aktif kamu ditampilkan sebagai proyek.</p>
      </div>

      <a href="{{ route('client.orders.index') }}" class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
                hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
        Ke Pesanan <i class="ri-arrow-right-line ml-1"></i>
      </a>
    </div>

    @if(empty($projects) || count($projects) === 0)
      <x-ui.empty-state icon="ri-briefcase-4-line" title="Belum ada proyek aktif"
        description="Proyek muncul ketika kamu punya order yang sedang diproses."
        actionUrl="{{ route('client.services.index') }}" actionLabel="Cari Layanan" />
    @else
      <div data-client-pager data-page-size="8" class="space-y-4">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5" data-pager-list>
          @foreach($projects as $p)
            <div data-pager-item class="bg-white border border-slate-200 rounded-[18px] p-5 hover:shadow-lg transition-all">
              <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                  <p class="font-extrabold text-slate-900">Order #{{ $p->id }}</p>
                  <p class="text-slate-500 text-[13px] mt-1 truncate">{{ $p->service->title ?? '-' }}</p>
                </div>
                <x-ui.status-badge :status="$p->status ?? '-'" />
              </div>

              <div class="mt-4 flex gap-2">
                <a href="{{ route('client.orders.show', $p->id) }}"
                  class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">
                  Rincian
                </a>
                <a href="{{ route('client.services.show', $p->service_id) }}" class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px]
                              hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
                  Layanan
                </a>
              </div>
            </div>
          @endforeach
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
          <p class="text-slate-500 text-[12.5px] font-bold" data-pager-info></p>
          <div class="flex items-center gap-2">
            <button type="button" data-pager-prev
              class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] disabled:opacity-40 transition-all">Sebelumnya</button>
            <div class="flex items-center gap-2" data-pager-numbers></div>
            <button type="button" data-pager-next
              class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] disabled:opacity-40 transition-all">Berikutnya</button>
          </div>
        </div>
      </div>


    @endif
  </section>
@endsection