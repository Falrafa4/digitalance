@extends('layouts.dashboard')
@section('title', 'History')

@section('content')
<section class="animate-fadeUp">
  <div class="mb-6">
    <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">History</h1>
    <p class="text-slate-500 mt-1">Riwayat order yang sudah selesai / dibatalkan.</p>
  </div>

  @if(empty($orders) || count($orders) === 0)
    @include('dashboard.client._ui.empty', [
      'icon' => 'ri-history-line',
      'title' => 'Belum ada history',
      'desc' => 'Order yang selesai/dibatalkan akan muncul di sini.',
      'actionUrl' => route('client.services.index'),
      'actionLabel' => 'Cari Jasa'
    ])
  @else
    <div data-client-pager data-page-size="8" class="space-y-4">
      <div class="grid grid-cols-1 xl:grid-cols-2 gap-5" data-pager-list>
        @foreach($orders as $o)
          <div data-pager-item class="bg-white border border-slate-200 rounded-[18px] p-5 hover:shadow-lg transition-all">
            <div class="flex items-start justify-between gap-4">
              <div class="min-w-0">
                <p class="font-extrabold text-slate-900">Order #{{ $o->id }}</p>
                <p class="text-slate-500 text-[13px] mt-1 truncate">
                  {{ $o->service->title ?? '-' }}
                </p>
                <p class="text-slate-400 text-[12px] font-bold mt-2">
                  Agreed: Rp {{ number_format((float)($o->agreed_price ?? 0), 0, ',', '.') }}
                </p>
              </div>
              @include('dashboard.client._ui.status-badge', ['status' => $o->status ?? '-'])
            </div>

            <div class="flex gap-2 mt-4">
              <a href="{{ route('client.orders.show', $o->id) }}"
                 class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all text-center">
                Detail
              </a>

              @if(!empty($o->review))
                <span class="flex-1 px-4 py-2.5 rounded-[12px] bg-emerald-50 border border-emerald-100 text-emerald-700 font-extrabold text-[12.5px] text-center">
                  Reviewed
                </span>
              @else
                <span class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200 text-slate-600 font-extrabold text-[12.5px] text-center">
                  No Review
                </span>
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