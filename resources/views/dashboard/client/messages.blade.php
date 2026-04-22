@extends('layouts.dashboard')
@section('title', 'Messages')

@section('content')
<div class="animate-fadeUp">
  <div class="mb-6">
    <h1 class="font-display text-[1.8rem] font-extrabold text-slate-900">Messages</h1>
    <p class="text-slate-500 mt-1">MVP: daftar pesan negosiasi (per order).</p>
  </div>

  <div class="bg-white border border-slate-200 rounded-[18px] overflow-hidden">
    <div class="divide-y divide-slate-100">
      @forelse(($threads ?? []) as $t)
        <div class="p-5 flex items-center justify-between gap-4">
          <div class="min-w-0">
            <p class="font-extrabold text-slate-900">Order #{{ $t->order_id ?? '-' }}</p>
            <p class="text-slate-500 text-[13px] mt-1 truncate">{{ $t->message ?? '-' }}</p>
          </div>
          @if(!empty($t->order_id))
            <a href="{{ route('client.orders.show', $t->order_id) }}"
               class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">
              Buka
            </a>
          @endif
        </div>
      @empty
        <div class="p-10 text-center">
          <p class="font-extrabold text-slate-900 text-[1.25rem]">Belum ada pesan</p>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection