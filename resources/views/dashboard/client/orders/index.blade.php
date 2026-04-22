@extends('layouts.dashboard')
@section('title', 'Orders')

@section('content')
<div class="animate-fadeUp">
  <div class="flex items-end justify-between mb-6">
    <div>
      <h1 class="font-display text-[1.8rem] font-extrabold text-slate-900">Orders</h1>
      <p class="text-slate-500 mt-1">Daftar pesanan kamu.</p>
    </div>
    <a href="{{ route('client.services.index') }}"
       class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
      Buat Order
    </a>
  </div>

  <div class="bg-white border border-slate-200 rounded-[18px] overflow-hidden">
    <div class="divide-y divide-slate-100">
      @forelse($orders as $o)
        <div class="p-5 flex items-center justify-between gap-4">
          <div class="min-w-0">
            <p class="font-extrabold text-slate-900">Order #{{ $o->id }}</p>
            <p class="text-slate-500 text-[13px] mt-1">{{ $o->service->title ?? '-' }}</p>
            <span class="inline-flex mt-2 px-3 py-1 rounded-full text-[12px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
              {{ $o->status ?? '-' }}
            </span>
          </div>
          <a href="{{ route('client.orders.show', $o->id) }}"
             class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">
            Detail
          </a>
        </div>
      @empty
        <div class="p-10 text-center">
          <p class="font-extrabold text-slate-900 text-[1.25rem]">Belum ada order</p>
          <p class="text-slate-500 mt-2">Mulai dari katalog jasa.</p>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection