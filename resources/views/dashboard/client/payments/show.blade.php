@extends('layouts.dashboard')
@section('title', 'Payment Detail')

@section('content')
<section class="animate-fadeUp">
  <div class="mb-6">
    <a href="{{ route('client.payments.index') }}" class="text-slate-500 font-bold text-[13px] hover:text-slate-900">
      <i class="ri-arrow-left-line mr-1"></i> Kembali
    </a>
    <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900 mt-2">Transaction #{{ $transaction->id }}</h1>
    <p class="text-slate-500 mt-1">Detail transaksi berdasarkan order.</p>
  </div>

  <div class="bg-white border border-slate-200 rounded-[18px] p-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
      <div>
        <p class="text-slate-500 text-[13.5px]">
          Order ID: <span class="font-extrabold text-slate-900">#{{ $transaction->order_id ?? '-' }}</span>
        </p>
        <p class="text-slate-500 text-[13.5px] mt-2">
          Dibuat: <span class="font-extrabold text-slate-900">{{ optional($transaction->created_at)->format('d M Y H:i') }}</span>
        </p>
      </div>

      @if(isset($transaction->status))
        @include('dashboard.client._ui.status-badge', ['status' => $transaction->status])
      @endif
    </div>

    <div class="mt-5 pt-5 border-t border-slate-100 flex flex-col sm:flex-row gap-3">
      @if(!empty($transaction->order_id))
        <a href="{{ route('client.orders.show', $transaction->order_id) }}"
           class="px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all text-center">
          Lihat Order <i class="ri-arrow-right-line ml-2"></i>
        </a>
      @endif
      <a href="{{ route('client.payments.index') }}"
         class="px-5 py-3 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
                hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
        Kembali ke Payments
      </a>
    </div>
  </div>
</section>
@endsection