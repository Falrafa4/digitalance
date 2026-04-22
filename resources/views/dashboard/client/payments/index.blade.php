@extends('layouts.dashboard')
@section('title', 'Payments')

@section('content')
<div class="animate-fadeUp">
  <div class="mb-6">
    <h1 class="font-display text-[1.8rem] font-extrabold text-slate-900">Payments</h1>
    <p class="text-slate-500 mt-1">Daftar transaksi kamu.</p>
  </div>

  <div class="bg-white border border-slate-200 rounded-[18px] overflow-hidden">
    <div class="divide-y divide-slate-100">
      @forelse(($transactions ?? []) as $t)
        <div class="p-5 flex items-center justify-between gap-4">
          <div>
            <p class="font-extrabold text-slate-900">Transaction #{{ $t->id }}</p>
            <p class="text-slate-500 text-[13px] mt-1">Order #{{ $t->order_id ?? '-' }}</p>
          </div>

          @if(!empty($t->order_id))
            <a href="{{ route('client.payments.show', $t->order_id) }}"
               class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">
              Detail
            </a>
          @endif
        </div>
      @empty
        <div class="p-10 text-center">
          <p class="font-extrabold text-slate-900 text-[1.25rem]">Belum ada transaksi</p>
          <p class="text-slate-500 mt-2">Transaksi akan muncul setelah pembayaran dibuat.</p>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection