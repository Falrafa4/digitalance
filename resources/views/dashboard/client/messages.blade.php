@extends('layouts.dashboard')
@section('title', 'Messages')

@section('content')
<section class="animate-fadeUp">
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
    <div>
      <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Messages</h1>
      <p class="text-slate-500 mt-1">MVP: kumpulan pesan negosiasi (latest).</p>
    </div>
    <a href="{{ route('client.orders.index') }}"
       class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
              hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
      Ke Orders <i class="ri-arrow-right-line ml-1"></i>
    </a>
  </div>

  @if(empty($threads) || count($threads) === 0)
    @include('dashboard.client._ui.empty', [
      'icon' => 'ri-message-3-line',
      'title' => 'Belum ada pesan',
      'desc' => 'Pesan akan muncul setelah kamu mengirim negosiasi di halaman detail order.',
      'actionUrl' => route('client.orders.index'),
      'actionLabel' => 'Ke Orders'
    ])
  @else
    <div data-client-pager data-page-size="10" class="space-y-4">
      <div class="bg-white border border-slate-200 rounded-[18px] overflow-hidden" data-pager-list>
        <div class="divide-y divide-slate-100">
          @foreach($threads as $t)
            <div data-pager-item class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div class="min-w-0">
                <p class="font-extrabold text-slate-900">Order #{{ $t->order_id ?? '-' }}</p>
                <p class="text-slate-500 text-[13px] mt-1 truncate">{{ $t->message ?? '-' }}</p>
                <p class="text-slate-400 text-[11px] font-bold mt-2">
                  {{ optional($t->created_at)->format('d M Y H:i') }}
                </p>
              </div>

              @if(!empty($t->order_id))
                <a href="{{ route('client.orders.show', $t->order_id) }}"
                   class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">
                  Buka
                </a>
              @endif
            </div>
          @endforeach
        </div>
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