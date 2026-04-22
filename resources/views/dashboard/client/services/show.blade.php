@extends('layouts.dashboard')
@section('title', 'Detail Jasa')

@section('content')
<div class="animate-fadeUp space-y-6">
  <div class="bg-white border border-slate-200 rounded-[18px] p-6">
    <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900">{{ $service->title ?? 'Service' }}</h1>
    <p class="text-slate-500 mt-1 text-[13.5px]">
      Kategori: <span class="font-bold">{{ $service->service_category->name ?? '-' }}</span>
    </p>

    <p class="text-slate-600 mt-4">{{ $service->description ?? 'Belum ada deskripsi.' }}</p>

    <div class="flex gap-2 mt-5">
      <a href="{{ route('client.orders.create', $service->id) }}"
         class="px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
        Order Jasa
      </a>
      <a href="{{ route('client.services.index') }}"
         class="px-5 py-3 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
        Kembali
      </a>
    </div>
  </div>

  <div class="bg-white border border-slate-200 rounded-[18px] p-6">
    <h2 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Profil Freelancer</h2>
    <p class="text-slate-500 mt-2">
      {{ optional(optional($service->freelancer)->skomda_student)->name ?? 'Freelancer' }}
    </p>
    <p class="text-slate-600 mt-2">{{ optional($service->freelancer)->bio ?? 'Belum ada bio.' }}</p>
  </div>

  <div class="bg-white border border-slate-200 rounded-[18px] p-6">
    <h2 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Layanan Lain</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
      @forelse(($otherServices ?? []) as $os)
        <a href="{{ route('client.services.show', $os->id) }}"
           class="border border-slate-200 rounded-[18px] p-5 hover:shadow-md transition">
          <p class="font-extrabold text-slate-900">{{ $os->title ?? 'Service' }}</p>
          <p class="text-slate-500 text-[13px] mt-1">{{ $os->service_category->name ?? '-' }}</p>
        </a>
      @empty
        <div class="sm:col-span-2 border-2 border-dashed border-slate-200 rounded-[18px] p-8 text-center bg-white">
          <p class="font-extrabold text-slate-900">Tidak ada layanan lain</p>
          <p class="text-slate-500 mt-1">Freelancer ini belum menambahkan layanan lain.</p>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection