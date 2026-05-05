@extends('layouts.dashboard')
@section('title', 'Edit Service | Digitalance')

@section('content')
    <div class="animate-fadeUp max-w-4xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('freelancer.services.show', $service->id) }}"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-semibold text-sm">
                <i class="ri-arrow-left-line"></i> Kembali ke Detail
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-[22px] p-7">
            <h1 class="font-display text-[1.7rem] font-extrabold text-slate-900 mb-5">Edit Service</h1>

            <form action="{{ route('freelancer.services.update', $service->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[12px] font-bold text-slate-500 uppercase tracking-[.1em] mb-2">Title</label>
                    <input type="text" name="title" value="{{ old('title', $service->title) }}" required
                        class="w-full py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-bold text-slate-500 uppercase tracking-[.1em] mb-2">Min
                            Price</label>
                        <input type="number" name="price_min" value="{{ old('price_min', $service->price_min) }}" min="0"
                            class="w-full py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-slate-500 uppercase tracking-[.1em] mb-2">Max
                            Price</label>
                        <input type="number" name="price_max" value="{{ old('price_max', $service->price_max) }}" min="0"
                            class="w-full py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-bold text-slate-500 uppercase tracking-[.1em] mb-2">Delivery Time
                        (days)</label>
                    <input type="number" name="delivery_time" value="{{ old('delivery_time', $service->delivery_time) }}"
                        min="1"
                        class="w-full py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white">
                </div>

                <div>
                    <label
                        class="block text-[12px] font-bold text-slate-500 uppercase tracking-[.1em] mb-2">Description</label>
                    <textarea name="description" rows="6"
                        class="w-full py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none focus:border-[#0f766e] focus:bg-white resize-none">{{ old('description', $service->description) }}</textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('freelancer.services.show', $service->id) }}"
                        class="px-4 py-2.5 rounded-[11px] border border-slate-200 text-slate-700 font-bold text-[12.5px] hover:bg-slate-50">Batal</a>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-[11px] bg-[#0f766e] text-white font-bold text-[12.5px] hover:bg-[#0c615a]">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection