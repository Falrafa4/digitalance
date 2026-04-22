@extends('layouts.dashboard')
@section('title', 'Payment Detail')

@section('content')
<div class="animate-fadeUp space-y-6">
  <a href="{{ route('client.payments.index') }}" class="text-slate-500 font-bold text-[13px] hover:text-slate-900">
    <i class="ri-arrow-left-line mr-1"></i> Kembali
  </a>

  <div class="bg-white border border-slate-200 rounded-[18px] p-6">
    <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900">Transaction #{{ $transaction->id }}</h1>
    <p class="text-slate-500 mt-2">Order ID: <span class="font-bold">#{{ $transaction->order_id ?? '-' }}</span></p>

    @if(!empty($transaction->order_id))
      <a href="{{ route('client.orders.show', $transaction->order_id) }}"
         class="mt-5 inline-flex px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
        Lihat Order
      </a>
    @endif
  </div>
</div>
@endsection