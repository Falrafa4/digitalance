@extends('layouts.dashboard')
@section('title', 'Talent Detail')

@section('content')
<div class="animate-fadeUp space-y-6">
  <a href="{{ route('client.talents.index') }}" class="text-slate-500 font-bold text-[13px] hover:text-slate-900">
    <i class="ri-arrow-left-line mr-1"></i> Kembali
  </a>

  <div class="bg-white border border-slate-200 rounded-[18px] p-6">
    <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900">
      {{ optional($freelancer->skomda_student)->name ?? 'Freelancer' }}
    </h1>
    <p class="text-slate-600 mt-2">{{ $freelancer->bio ?? 'Belum ada bio.' }}</p>
  </div>

  <div class="bg-white border border-slate-200 rounded-[18px] p-6">
    <h2 class="font-display font-extrabold text-slate-900 text-[1.2rem]">Layanan</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
      @forelse($services as $s)
        <a href="{{ route('client.services.show', $s->id) }}"
           class="border border-slate-200 rounded-[18px] p-5 hover:shadow-md transition">
          <p class="font-extrabold text-slate-900">{{ $s->title ?? 'Service' }}</p>
          <p class="text-slate-500 text-[13px] mt-1">{{ $s->service_category->name ?? '-' }}</p>
        </a>
      @empty
        <div class="sm:col-span-2 border-2 border-dashed border-slate-200 rounded-[18px] p-8 text-center bg-white">
          <p class="font-extrabold text-slate-900">Belum ada layanan</p>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection