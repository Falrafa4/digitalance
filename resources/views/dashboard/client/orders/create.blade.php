@extends('layouts.dashboard')
@section('title', 'Buat Order')

@section('content')
<div class="animate-fadeUp grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="xl:col-span-2 bg-white border border-slate-200 rounded-[18px] p-6">
    <h1 class="font-display text-[1.6rem] font-extrabold text-slate-900">Buat Order</h1>
    <p class="text-slate-500 mt-1">Isi brief kebutuhanmu.</p>

    <form method="POST" action="{{ route('client.orders.store') }}" class="mt-5 space-y-3" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
      @csrf
      <input type="hidden" name="service_id" value="{{ $service->id }}"/>

      <textarea name="brief" rows="8"
                class="w-full px-4 py-3 rounded-[14px] bg-slate-50 border border-slate-200 focus:border-[#0f766e] outline-none transition-all
                @error('brief') border-red-500 @enderror"
                placeholder="Tulis brief..." aria-label="Brief detail">{{ old('brief') }}</textarea>
      @error('brief') <p class="text-red-600 text-[12px] font-bold">{{ $message }}</p> @enderror

      <div class="flex pt-2">
        <button type="submit" 
                :disabled="isSubmitting"
                class="px-8 py-3.5 rounded-[14px] bg-slate-900 text-white font-bold text-[14px] hover:bg-black hover:shadow-lg hover:shadow-slate-200 transition-all disabled:opacity-50 flex items-center justify-center gap-3 min-w-[160px]">
          <i x-show="isSubmitting" class="ri-loader-4-line animate-spin text-lg"></i>
          <span x-text="isSubmitting ? 'Memproses...' : 'Buat Order'">Buat Order</span>
        </button>
      </div>
    </form>

  </div>

  <div class="bg-white border border-slate-200 rounded-[18px] p-6">
    <p class="text-slate-400 text-[12px] font-extrabold uppercase tracking-widest">Jasa</p>
    <p class="font-extrabold text-slate-900 mt-2">{{ $service->title ?? '-' }}</p>
    <p class="text-slate-500 text-[13px] mt-1">Kategori: {{ $service->service_category->name ?? '-' }}</p>
  </div>
</div>
@endsection