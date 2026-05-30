@php
    $service = $service ?? null;
    $selectedCategoryId = old('category_id', $service->category_id ?? '');
    $priceMinValue = old('price_min', $service->price_min ?? '');
    $priceMaxValue = old('price_max', $service->price_max ?? '');
@endphp

<div class="space-y-6">
    {{-- Category Select --}}
    <div>
        <label for="category_id" class="block text-sm font-semibold text-slate-700 mb-2">
            Kategori <span class="text-red-500">*</span>
        </label>
        @if (!empty($categoryLocked))
            <input type="hidden" name="category_id" value="{{ $selectedCategoryId }}">
            <div class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-700 flex items-center justify-between gap-3">
                <span>{{ $service->category->name ?? 'Kategori tidak tersedia' }}</span>
                <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Terkunci</span>
            </div>
        @else
            <select id="category_id" name="category_id" required
                class="w-full px-4 py-3 border border-slate-200 rounded-lg font-sans text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0f766e] focus:border-transparent {{ $errors->has('category_id') ? 'border-red-300 bg-red-50' : '' }}">
                <option value="">Pilih kategori layanan</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ (string) $selectedCategoryId === (string) $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('category_id'))
                <p class="mt-2 text-sm text-red-600">
                    <i class="ri-error-warning-line mr-1"></i>
                    {{ $errors->first('category_id') }}
                </p>
            @endif
        @endif
    </div>

    {{-- Title --}}
    <div>
        <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">
            Judul <span class="text-red-500">*</span>
        </label>
        <input type="text" id="title" name="title" value="{{ old('title', $service->title ?? '') }}" required
            placeholder="Masukkan judul layanan"
            class="w-full px-4 py-3 border border-slate-200 rounded-lg font-sans text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0f766e] focus:border-transparent {{ $errors->has('title') ? 'border-red-300 bg-red-50' : '' }}" />
        @if ($errors->has('title'))
            <p class="mt-2 text-sm text-red-600">
                <i class="ri-error-warning-line mr-1"></i>
                {{ $errors->first('title') }}
            </p>
        @endif
    </div>

    {{-- Price Range --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="price_min" class="block text-sm font-semibold text-slate-700 mb-2">
                Harga Min <span class="text-red-500">*</span>
            </label>
            <input type="text" id="price_min" name="price_min" data-rupiah-input inputmode="numeric"
                value="{{ $priceMinValue }}" required placeholder="1.000.000"
                class="w-full px-4 py-3 border border-slate-200 rounded-lg font-sans text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0f766e] focus:border-transparent {{ $errors->has('price_min') ? 'border-red-300 bg-red-50' : '' }}" />
        </div>
        <div>
            <label for="price_max" class="block text-sm font-semibold text-slate-700 mb-2">
                Harga Max <span class="text-red-500">*</span>
            </label>
            <input type="text" id="price_max" name="price_max" data-rupiah-input inputmode="numeric"
                value="{{ $priceMaxValue }}" required placeholder="1.000.000"
                class="w-full px-4 py-3 border border-slate-200 rounded-lg font-sans text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0f766e] focus:border-transparent {{ $errors->has('price_max') ? 'border-red-300 bg-red-50' : '' }}" />
            @if ($errors->has('price_max'))
                <p class="mt-2 text-sm text-red-600">
                    <i class="ri-error-warning-line mr-1"></i>
                    {{ $errors->first('price_max') }}
                </p>
            @endif
        </div>
    </div>

    {{-- Delivery Time --}}
    <div>
        <label for="delivery_time" class="block text-sm font-semibold text-slate-700 mb-2">
            Waktu Pengiriman (hari) <span class="text-red-500">*</span>
        </label>
        <input type="number" id="delivery_time" name="delivery_time"
            value="{{ old('delivery_time', $service->delivery_time ?? '') }}" min="1" required placeholder="1"
            class="w-full px-4 py-3 border border-slate-200 rounded-lg font-sans text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0f766e] focus:border-transparent {{ $errors->has('delivery_time') ? 'border-red-300 bg-red-50' : '' }}" />
        @if ($errors->has('delivery_time'))
            <p class="mt-2 text-sm text-red-600">
                <i class="ri-error-warning-line mr-1"></i>
                {{ $errors->first('delivery_time') }}
            </p>
        @endif
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">
            Deskripsi <span class="text-red-500">*</span>
        </label>
        <textarea id="description" name="description" required placeholder="Jelaskan layanan Anda secara detail..."
            rows="5"
            class="w-full px-4 py-3 border border-slate-200 rounded-lg font-sans text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0f766e] focus:border-transparent resize-none {{ $errors->has('description') ? 'border-red-300 bg-red-50' : '' }}">{{ old('description', $service->description ?? '') }}</textarea>
        @if ($errors->has('description'))
            <p class="mt-2 text-sm text-red-600">
                <i class="ri-error-warning-line mr-1"></i>
                {{ $errors->first('description') }}
            </p>
        @endif
    </div>
</div>