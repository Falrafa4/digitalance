@extends('layouts.dashboard')
@section('title', 'Detail Jasa')

@section('content')
<section class="animate-fadeUp">
  <div class="flex flex-col lg:flex-row gap-6">
    <div class="flex-1 space-y-6">
      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <h1 class="font-display text-[1.65rem] font-extrabold text-slate-900 truncate">
              {{ $service->title ?? 'Service' }}
            </h1>
            <p class="text-slate-500 mt-1 text-[13.5px]">
              Kategori: <span class="font-bold">{{ $service->service_category->name ?? '-' }}</span>
            </p>
          </div>
          @include('dashboard.client._ui.status-badge', ['status' => $service->status ?? 'Approved'])
        </div>

        <p class="text-slate-600 mt-4 leading-relaxed text-[14px]">
          {{ $service->description ?? 'Belum ada deskripsi.' }}
        </p>

        <div class="flex flex-col sm:flex-row gap-3 mt-6">
          <a href="{{ route('client.orders.create', $service->id) }}"
             class="px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all text-center">
            Order Jasa Ini <i class="ri-arrow-right-line ml-2"></i>
          </a>
          <a href="{{ route('client.services.index') }}"
             class="px-5 py-3 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
                    hover:border-[#0f766e] hover:text-[#0f766e] transition-all text-center">
            Kembali <i class="ri-arrow-left-line ml-2"></i>
          </a>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <h2 class="font-display font-extrabold text-slate-900 text-[1.25rem]">Layanan Lain</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
          @forelse(($otherServices ?? []) as $os)
            <a href="{{ route('client.services.show', $os->id) }}"
               class="border border-slate-200 rounded-[18px] p-5 hover:shadow-md transition-all bg-white">
              <p class="font-extrabold text-slate-900 truncate">{{ $os->title ?? 'Service' }}</p>
              <p class="text-slate-500 text-[13px] mt-1">{{ $os->service_category->name ?? '-' }}</p>
            </a>
          @empty
            <div class="sm:col-span-2">
              @include('dashboard.client._ui.empty', [
                'icon' => 'ri-folder-unknow-line',
                'title' => 'Belum ada layanan lain',
                'desc' => 'Freelancer ini belum menambahkan layanan lain.'
              ])
            </div>
          @endforelse
        </div>
      </div>
    </div>

    <aside class="w-full lg:w-[360px] shrink-0 space-y-6">
      <div class="bg-white border border-slate-200 rounded-[18px] p-6">
        <h3 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Freelancer</h3>
        <div class="flex items-start gap-3 mt-4">
          <div class="w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400">
            <i class="ri-user-3-line text-[20px]"></i>
          </div>
          <div class="min-w-0">
            <p class="font-extrabold text-slate-900 truncate">
              {{ optional(optional($service->freelancer)->skomda_student)->name ?? 'Freelancer' }}
            </p>
            <p class="text-slate-500 text-[13px] mt-1">
              {{ optional($service->freelancer)->bio ?? 'Belum ada bio.' }}
            </p>
          </div>
        </div>

        <div class="mt-5 pt-5 border-t border-slate-100">
          <a href="{{ route('client.talents.index') }}"
             class="w-full inline-flex items-center justify-center px-5 py-3 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px]
                    hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
            Find Talent <i class="ri-user-search-line ml-2"></i>
          </a>
        </div>
      </div>
    </aside>
  </div>
</section>
@endsection