@extends('layouts.dashboard')
@section('title', 'Find Talent')

@section('content')
<section class="animate-fadeUp">
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
    <div>
      <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Find Talent</h1>
      <p class="text-slate-500 mt-1">Pilih freelancer dan lihat layanan mereka.</p>
    </div>

    <a href="{{ route('client.services.index') }}"
       class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
              hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
      Katalog Jasa <i class="ri-arrow-right-line ml-1"></i>
    </a>
  </div>

  @if(empty($freelancers) || count($freelancers) === 0)
    @include('dashboard.client._ui.empty', [
      'icon' => 'ri-user-search-line',
      'title' => 'Belum ada freelancer',
      'desc' => 'Data freelancer belum tersedia.'
    ])
  @else
    <div data-client-pager data-page-size="9" class="space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5" data-pager-list>
        @foreach($freelancers as $f)
          <div data-pager-item class="bg-white border border-slate-200 rounded-[18px] p-5 hover:shadow-lg transition-all">
            <div class="flex items-start gap-3">
              <div class="w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400">
                <i class="ri-user-3-line text-[20px]"></i>
              </div>
              <div class="min-w-0">
                <p class="font-extrabold text-slate-900 truncate">{{ optional($f->skomda_student)->name ?? 'Freelancer' }}</p>
                <p class="text-slate-500 text-[13px] mt-1 line-clamp-2">{{ $f->bio ?? 'Belum ada bio.' }}</p>
              </div>
            </div>

            <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
              <span class="text-slate-500 text-[12px] font-bold">
                Services: {{ $f->services_count ?? 0 }}
              </span>

              <a href="{{ route('client.talents.show', $f->id) }}"
                 class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">
                Profil
              </a>
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