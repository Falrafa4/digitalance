@extends('layouts.dashboard')
@section('title', 'Edit Lowongan | Digitalance')

@section('content')
<div class="content-scroll flex-1 px-8 py-7 overflow-y-auto max-w-3xl relative overflow-hidden">
    {{-- Decorative Background Icon --}}
    <div class="absolute -right-12 -top-12 w-64 h-64 opacity-[0.03] pointer-events-none rotate-12">
        <i class="ri-briefcase-2-line text-[250px]"></i>
    </div>

    <div class="mb-8 animate-fadeUp relative z-10">
        <a href="{{ route('client.loker.index') }}"
            class="inline-flex items-center gap-1 text-slate-500 font-bold text-[13px] hover:text-slate-900 mb-3">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
        <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Edit Lowongan</h1>
    </div>

    <form action="{{ route('client.loker.update', $loker->id) }}" method="POST"
        class="bg-white border border-slate-200 rounded-[18px] p-6 animate-fadeUp">
        @csrf
        @method('PUT')

        <div class="flex flex-col gap-1.5 mb-5">
            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Judul Lowongan *</label>
            <input type="text" name="title" value="{{ old('title', $loker->title) }}" required
                class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white @error('title') border-red-400 @enderror" />
            @error('title')<p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-1.5 mb-5">
            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Deskripsi *</label>
            <textarea name="description" rows="6" required
                class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white resize-none @error('description') border-red-400 @enderror">{{ old('description', $loker->description) }}</textarea>
            @error('description')<p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Kategori</label>
                <select name="category_id"
                    class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white">
                    <option value="">Pilih kategori...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @if($loker->category_id == $cat->id) selected @endif>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline', $loker->deadline) }}"
                    class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Budget Minimal (Rp)</label>
                <input type="number" name="budget_min" value="{{ old('budget_min', $loker->budget_min) }}" min="0"
                    class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white" />
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Budget Maksimal (Rp)</label>
                <input type="number" name="budget_max" value="{{ old('budget_max', $loker->budget_max) }}" min="0"
                    class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white" />
            </div>
        </div>

        <div class="flex flex-col gap-1.5 mb-6">
            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-[.1em]">Status</label>
            <select name="status"
                class="py-[10px] px-[13px] bg-slate-50 border-[1.5px] border-slate-200 rounded-[11px] text-[13.5px] outline-none transition-all duration-200 focus:border-[#0f766e] focus:bg-white">
                <option value="Open" @if($loker->status === 'Open') selected @endif>Open</option>
                <option value="Closed" @if($loker->status === 'Closed') selected @endif>Closed</option>
            </select>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('client.loker.index') }}"
                class="px-5 py-2.5 rounded-[12px] border border-slate-200 text-slate-600 font-bold text-[13px] hover:bg-slate-50 transition-all">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2.5 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] transition-all shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
