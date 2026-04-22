@extends('layouts.dashboard')
@section('title', 'Katalog Jasa')

@section('content')
<section class="animate-fadeUp">
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
    <div>
      <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Katalog Jasa</h1>
      <p class="text-slate-500 mt-1">Browse layanan dan langsung buat order.</p>
    </div>

    <div class="flex gap-2">
      <a href="{{ route('client.orders.index') }}"
         class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
        Orders <i class="ri-arrow-right-line ml-1"></i>
      </a>
    </div>
  </div>

  @if(empty($services) || count($services) === 0)
    @include('dashboard.client._ui.empty', [
      'icon' => 'ri-tools-line',
      'title' => 'Belum ada jasa',
      'desc' => 'Belum ada layanan yang bisa ditampilkan saat ini.',
      'actionUrl' => route('client.dashboard'),
      'actionLabel' => 'Kembali'
    ])
  @else
    <div data-client-pager data-page-size="9" class="space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5" data-pager-list>
        @foreach($services as $s)
          <div data-pager-item class="bg-white border border-slate-200 rounded-[18px] p-5 hover:shadow-lg transition-all">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <p class="font-extrabold text-slate-900 text-[1.02rem] truncate">
                  {{ $s->title ?? 'Service' }}
                </p>
                <p class="text-slate-500 text-[13px] mt-1">
                  Kategori: <span class="font-bold">{{ $s->service_category->name ?? '-' }}</span>
                </p>
                <p class="text-slate-500 text-[13px] mt-1">
                  Oleh: <span class="font-bold">{{ optional(optional($s->freelancer)->skomda_student)->name ?? 'Freelancer' }}</span>
                </p>
              </div>

              @include('dashboard.client._ui.status-badge', ['status' => $s->status ?? 'Approved'])
            </div>

            <p class="text-slate-600 text-[13.5px] leading-relaxed mt-3 line-clamp-3">
              {{ $s->description ?? 'Belum ada deskripsi.' }}
            </p>

            <div class="flex gap-2 mt-4">
              <a href="{{ route('client.services.show', $s->id) }}"
                 class="flex-1 px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all text-center">
                Detail
              </a>
              <a href="{{ route('client.orders.create', $s->id) }}"
                 class="flex-1 px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px]
                        hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
                Order
              </a>
            </div>

            <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                    <button type="button"
                      onclick="navigator.clipboard.writeText(@json(route('client.services.show', $s->id))); alert('Link copied!')"
                      class="text-slate-500 hover:text-slate-900 text-[12.5px] font-bold transition-all">
                <i class="ri-share-line mr-1"></i> Share
              </button>

              <span class="text-slate-400 text-[12px] font-bold">#{{ $s->id }}</span>
            </div>
          </div>
        @endforeach
      </div>

      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-slate-500 text-[12.5px] font-bold" data-pager-info></p>
        <div class="flex items-center gap-2">
          <button type="button" data-pager-prev
                  class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px]
                         hover:border-[#0f766e] hover:text-[#0f766e] disabled:opacity-40 disabled:hover:border-slate-200 disabled:hover:text-slate-700 transition-all">
            Prev
          </button>
          <div class="flex items-center gap-2" data-pager-numbers></div>
          <button type="button" data-pager-next
                  class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px]
                         hover:border-[#0f766e] hover:text-[#0f766e] disabled:opacity-40 disabled:hover:border-slate-200 disabled:hover:text-slate-700 transition-all">
            Next
          </button>
        </div>
      </div>
    </div>

    @include('dashboard.client._ui.client-pager')
  @endif
</section>
@endsection