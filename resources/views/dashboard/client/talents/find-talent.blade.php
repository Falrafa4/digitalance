@extends('layouts.dashboard')
@section('title', 'Temukan Talenta')

@section('content')
  <section class="animate-fadeUp">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="font-display text-[1.85rem] font-extrabold text-slate-900">Temukan Talenta</h1>
        <p class="text-slate-500 mt-1">Pilih freelancer dan lihat layanan mereka.</p>
      </div>

      <a href="{{ route('client.services.index') }}" class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
                hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
        Katalog Jasa <i class="ri-arrow-right-line ml-1"></i>
      </a>
    </div>

    @if(empty($freelancers) || count($freelancers) === 0)
      <x-ui.empty-state icon="ri-user-search-line" title="Belum ada freelancer"
        description="Data freelancer belum tersedia." />
    @else
      <div data-client-pager data-page-size="9" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5" data-pager-list>
          @foreach($freelancers as $f)
            {{-- PERBAIKAN TASK 5: Menambahkan efek hover-highlight border teal dan shadow transisi smooth --}}
            <div data-pager-item
              class="bg-white border border-slate-200 rounded-[22px] p-6 hover:shadow-xl hover:border-[#0f766e] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
              <div class="flex items-start gap-4">
                <img src="{{ asset('storage/' . $f->profile_photo) }}"
                  loading="lazy" decoding="async"
                  class="w-16 h-16 rounded-xl bg-slate-100 object-cover flex-shrink-0" />
                <div class="min-w-0 flex-1">
                  <p class="font-black text-slate-900 text-base truncate">
                    {{ optional($f->skomda_student)->name ?? 'Freelancer' }}</p>
                  <p class="text-slate-400 text-[11px] font-bold uppercase tracking-tight mt-0.5">
                    {{ optional($f->skomda_student)->major ?? 'Siswa SKOMDA' }}</p>
                  <p class="text-slate-500 text-[13px] mt-3 line-clamp-2 leading-relaxed font-medium">
                    {{ $f->bio ?? 'Belum ada bio.' }}</p>
                </div>
              </div>

              <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-100">
                {{-- PERBAIKAN TASK 5: Tampilan Badge jumlah service dibuat lebih prominen menggunakan gaya pill modern --}}
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-50 border border-teal-100 text-[#0f766e] text-[11px] font-black rounded-full uppercase tracking-wider">
                  <i class="ri-briefcase-line"></i>
                  {{ $f->services_count ?? 0 }} Services
                </span>

                <a href="{{ route('client.talents.show', $f->id) }}"
                  class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all shadow-md">
                  Profil Talent
                </a>
              </div>
            </div>
          @endforeach
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4">
          <p class="text-slate-500 text-[12.5px] font-bold" data-pager-info></p>
          <div class="flex items-center gap-2">
            <button type="button" data-pager-prev
              class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] disabled:opacity-40 transition-all">Sebelumnya</button>
            <div class="flex items-center gap-2" data-pager-numbers></div>
            <button type="button" data-pager-next
              class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[12.5px] hover:border-[#0f766e] hover:text-[#0f766e] disabled:opacity-40 transition-all">Berikutnya</button>
          </div>
        </div>
      </div>
    @endif
  </section>
@endsection