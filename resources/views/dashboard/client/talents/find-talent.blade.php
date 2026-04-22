@extends('layouts.dashboard')
@section('title', 'Find Talent')

@section('content')
<div class="animate-fadeUp">
  <div class="mb-6">
    <h1 class="font-display text-[1.8rem] font-extrabold text-slate-900">Find Talent</h1>
    <p class="text-slate-500 mt-1">Daftar freelancer.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse($freelancers as $f)
      <div class="bg-white border border-slate-200 rounded-[18px] p-5">
        <p class="font-extrabold text-slate-900">{{ optional($f->skomda_student)->name ?? 'Freelancer' }}</p>
        <p class="text-slate-500 text-[13px] mt-1">Services: {{ $f->services_count ?? 0 }}</p>

        <a href="{{ route('client.talents.show', $f->id) }}"
           class="mt-4 inline-flex w-full justify-center px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">
          Lihat Profil
        </a>
      </div>
    @empty
      <div class="col-span-full bg-white border-2 border-dashed border-slate-200 rounded-[18px] p-10 text-center">
        <p class="font-extrabold text-slate-900 text-[1.25rem]">Belum ada freelancer</p>
      </div>
    @endforelse
  </div>
</div>
@endsection