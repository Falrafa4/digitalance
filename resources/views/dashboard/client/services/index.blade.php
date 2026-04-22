@extends('layouts.dashboard')
@section('title', 'Katalog Jasa')

@section('content')
<section class="animate-fadeUp">
    <div class="mb-6">
        <h1 class="font-display text-[1.8rem] font-extrabold text-slate-900">Katalog Jasa</h1>
        <p class="text-slate-500 mt-1">Pilih jasa lalu buat order.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($services as $s)
            <div class="bg-white border border-slate-200 rounded-[18px] p-5">
                <p class="font-extrabold text-slate-900 truncate">{{ $s->title ?? 'Service' }}</p>
                <p class="text-slate-500 text-[13px] mt-1">
                    Kategori: <span class="font-bold">{{ $s->service_category->name ?? '-' }}</span>
                </p>
                <p class="text-slate-500 text-[13px] mt-1">
                    Freelancer: <span class="font-bold">{{ optional(optional($s->freelancer)->skomda_student)->name ?? '-' }}</span>
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
            </div>
        @empty
            <div class="xl:col-span-3 sm:col-span-2">
                @include('dashboard.client._empty', [
                    'icon' => 'ri-tools-line',
                    'title' => 'Belum ada jasa',
                    'desc' => 'Data jasa belum tersedia.'
                ])
            </div>
        @endforelse
    </div>
</section>
@endsection