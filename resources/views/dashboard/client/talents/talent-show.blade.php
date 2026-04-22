@extends('layouts.dashboard')
@section('title', 'Profil Talent')

@section('content')
<section class="animate-fadeUp">
  <div class="mb-6">
    <a href="{{ route('client.talents.index') }}" class="text-slate-500 font-bold text-[13px] hover:text-slate-900">
      <i class="ri-arrow-left-line mr-1"></i> Kembali
    </a>

    <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900 mt-2">
      {{ optional($freelancer->skomda_student)->name ?? 'Freelancer' }}
    </h1>
    <p class="text-slate-500 mt-1">{{ $freelancer->bio ?? 'Belum ada bio.' }}</p>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2">
      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <div class="flex items-end justify-between gap-4">
          <div>
            <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem]">Daftar Layanan</h2>
            <p class="text-slate-500 text-[13.5px] mt-1">Klik layanan untuk lihat detail.</p>
          </div>
          <a href="{{ route('client.services.index') }}"
             class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px]
                    hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
            Katalog
          </a>
        </div>

        @if(empty($services) || count($services) === 0)
          <div class="mt-5">
            @include('dashboard.client._ui.empty', [
              'icon' => 'ri-tools-line',
              'title' => 'Belum ada layanan',
              'desc' => 'Freelancer ini belum punya layanan.'
            ])
          </div>
        @else
          <div data-client-pager data-page-size="6" class="space-y-4 mt-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" data-pager-list>
              @foreach($services as $s)
                <a data-pager-item href="{{ route('client.services.show', $s->id) }}"
                   class="bg-white border border-slate-200 rounded-[18px] p-5 hover:shadow-lg transition-all">
                  <p class="font-extrabold text-slate-900 truncate">{{ $s->title ?? 'Service' }}</p>
                  <p class="text-slate-500 text-[13px] mt-1">Kategori: <span class="font-bold">{{ $s->service_category->name ?? '-' }}</span></p>
                  <div class="mt-3">
                    @include('dashboard.client._ui.status-badge', ['status' => $s->status ?? 'Approved'])
                  </div>
                </a>
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
      </div>
    </div>

    <aside>
      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <h3 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Info</h3>
        <p class="text-slate-500 text-[13.5px] mt-3">
          Freelancer ID: <span class="font-extrabold text-slate-900">#{{ $freelancer->id }}</span>
        </p>
        <p class="text-slate-500 text-[13.5px] mt-1">
          Email: <span class="font-extrabold text-slate-900">{{ optional($freelancer->skomda_student)->email ?? '-' }}</span>
        </p>

        <div class="mt-5 pt-5 border-t border-slate-100">
          <a href="{{ route('client.orders.index') }}"
             class="w-full inline-flex items-center justify-center px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
            Ke Orders <i class="ri-arrow-right-line ml-2"></i>
          </a>
        </div>
      </div>
    </aside>
  </div>
</section>
@endsection