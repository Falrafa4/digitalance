@extends('layouts.dashboard')
@section('title', 'Payments')

@section('content')
<section class="animate-fadeUp">
  <div class="mb-6">
    <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Payments</h1>
    <p class="text-slate-500 mt-1">Daftar transaksi kamu.</p>
  </div>

  @if(empty($transactions) || count($transactions) === 0)
    @include('dashboard.client._ui.empty', [
      'icon' => 'ri-bank-card-line',
      'title' => 'Belum ada transaksi',
      'desc' => 'Transaksi akan muncul setelah ada pembayaran.',
      'actionUrl' => route('client.orders.index'),
      'actionLabel' => 'Ke Orders'
    ])
  @else
    <div data-client-pager data-page-size="10" class="space-y-4">
      <div class="grid grid-cols-1 xl:grid-cols-2 gap-5" data-pager-list>
        @foreach($transactions as $t)
          <div data-pager-item class="bg-white border border-slate-200 rounded-[18px] p-5 hover:shadow-lg transition-all">
            <div class="flex items-start justify-between gap-4">
              <div>
                <p class="font-extrabold text-slate-900">Transaction #{{ $t->id }}</p>
                <p class="text-slate-500 text-[13px] mt-1">Order #{{ $t->order_id ?? '-' }}</p>
                <p class="text-slate-400 text-[11px] font-bold mt-2">
                  {{ optional($t->created_at)->format('d M Y H:i') }}
                </p>
              </div>

              @if(isset($t->status))
                @include('dashboard.client._ui.status-badge', ['status' => $t->status])
              @endif
            </div>

            <div class="flex gap-2 mt-4">
              @if(!empty($t->order_id))
                <a href="{{ route('client.payments.show', $t->order_id) }}"
                   class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all text-center">
                  Detail
                </a>
                <a href="{{ route('client.orders.show', $t->order_id) }}"
                   class="flex-1 px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px]
                          hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
                  Order
                </a>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-slate-500 text-[12.5px] font-bold" data-pager-info></p>
        <div class="flex items-center gap-2">
          <button type="button" data-pager-prev class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] disabled:opacity-40 transition-all">Prev</button>
          <div class="flex items-center gap-2" data-pager-numbers></div>
          <button type="button" data-pager-next class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] disabled:opacity-40 transition-all">Next</button>
        </div>
      </div>
    </div>

    @include('dashboard.client._ui.client-pager')
  @endif
</section>
@endsection