@extends('layouts.dashboard')
@section('title', 'History')

@section('content')
    <div class="animate-fadeUp">
        <div class="mb-6">
            <h1 class="font-display text-[1.8rem] font-extrabold text-slate-900">History</h1>
            <p class="text-slate-500 mt-1">Order selesai / dibatalkan.</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-[18px] overflow-hidden">
            <div class="divide-y divide-slate-100">
                @forelse(($orders ?? []) as $o)
                    <div class="p-5 flex items-center justify-between gap-4">
                        <div>
                            <p class="font-extrabold text-slate-900">Order #{{ $o->id }}</p>
                            <p class="text-slate-500 text-[13px] mt-1">{{ $o->service->title ?? '-' }}</p>
                            <span
                                class="inline-flex mt-2 px-3 py-1 rounded-full text-[12px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $o->status ?? '-' }}
                            </span>
                        </div>
                        <a href="{{ route('client.orders.show', $o->id) }}"
                            class="px-4 py-2.5 rounded-[12px] bg-slate-900 text-white font-bold text-[12.5px] hover:bg-black transition-all">
                            Detail
                        </a>
                    </div>
                @empty
                    <div class="p-10 text-center">
                        <p class="font-extrabold text-slate-900 text-[1.25rem]">Belum ada history</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection