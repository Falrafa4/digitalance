@php
  use Illuminate\Support\Str;
@endphp
@extends('layouts.dashboard')
@section('title', 'Orders')

@section('content')
  <section class="animate-fadeUp">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Pesanan</h1>
        <p class="text-slate-500 mt-1">Pantau pesanan kamu dari dibuat sampai selesai.</p>
      </div>

      <a href="{{ route('client.services.index') }}"
        class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
        Buat Order <i class="ri-add-line ml-1"></i>
      </a>
    </div>

    @if(empty($orders) || count($orders) === 0)
      <x-ui.empty-state icon="ri-file-list-3-line" title="Belum ada order"
        description="Mulai order pertamamu dari katalog jasa." actionUrl="{{ route('client.services.index') }}"
        actionLabel="Browse Katalog" />
    @else
      <div data-client-pager data-page-size="8" class="space-y-4">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5" data-pager-list>
          @foreach($orders as $o)
            <div data-pager-item class="bg-white border border-slate-200 rounded-[18px] p-5 hover:shadow-lg transition-all">
              <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    @if($o->lokerApplication)
                      <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-indigo-100 text-indigo-700 uppercase tracking-wider">Lowongan</span>
                    @endif
                    <p class="font-extrabold text-slate-900">Order #{{ $o->id }}</p>
                  </div>
                  <p class="text-slate-500 text-[13px] mt-1 truncate">
                    @if($o->lokerApplication)
                      {{ optional(optional($o->freelancer)->skomda_student)->name ?? optional(optional($o->service?->freelancer)->skomda_student)->name ?? 'Freelancer' }} - {{ Str::limit($o->brief, 40) }}
                    @else
                      {{ $o->service?->title ?? '-' }}
                    @endif
                  </p>
                </div>
                <div class="flex items-center gap-2">
                  <x-ui.status-badge :status="$o->status ?? '-'" />
                  <div class="text-right shrink-0">
                    <p class="text-slate-400 text-[12px] font-bold">
                      @if($o->agreed_price)
                        Rp{{ number_format((float) $o->agreed_price, 0, ',', '.') }}
                      @else
                        <span class="text-slate-300">Belum disepakati</span>
                      @endif
                    </p>
                  </div>
                </div>
              </div>

              <div class="flex gap-2 mt-4">
                <a href="{{ route('client.orders.show', $o->id) }}"
                  class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all text-center">
                  Detail
                </a>
                @if(!$o->lokerApplication)
                  <a href="{{ route('client.services.show', $o->service_id) }}"
                    class="flex-1 px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
                    Lihat Jasa
                  </a>
                @endif
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