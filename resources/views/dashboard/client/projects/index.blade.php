@extends('layouts.dashboard')
@section('title', 'My Projects')

@section('content')
<div class="animate-fadeUp">
  <div class="mb-6">
    <h1 class="font-display text-[1.8rem] font-extrabold text-slate-900">My Projects</h1>
    <p class="text-slate-500 mt-1">Order aktif kamu.</p>
  </div>

  @if(empty($projects) || count($projects) === 0)
    <div class="bg-white border-2 border-dashed border-slate-200 rounded-[18px] p-10 text-center">
      <p class="font-extrabold text-slate-900 text-[1.25rem]">Belum ada project</p>
    </div>
  @else
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
      @foreach($projects as $p)
        <div class="bg-white border border-slate-200 rounded-[18px] p-5">
          <p class="font-extrabold text-slate-900">Order #{{ $p->id }}</p>
          <p class="text-slate-500 text-[13px] mt-1">{{ $p->service->title ?? '-' }}</p>
          <span class="inline-flex mt-2 px-3 py-1 rounded-full text-[12px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
            {{ $p->status ?? '-' }}
          </span>

          <a href="{{ route('client.orders.show', $p->id) }}"
             class="mt-4 inline-flex px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">
            Detail
          </a>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection