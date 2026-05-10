<div class="w-full">
    <label for="{{ $id ?? $name }}"
           class="block text-[13px] font-bold text-slate-700 mb-1.5">
        {{ $label ?? '' }}
        @if($required ?? false)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">
        <input type="file"
               name="{{ $name }}"
               id="{{ $id ?? $name }}"
               {{ $required ?? false ? 'required' : '' }}
               {{ $multiple ?? false ? 'multiple' : '' }}
               accept="{{ $accept ?? '*' }}"
               class="w-full px-4 py-2.5 rounded-[12px] bg-slate-50 border border-slate-200 text-[14px] font-semibold text-slate-700 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0f766e] file:text-white hover:file:bg-[#0d6b63] cursor-pointer transition-all
               {{ $errors && $errors->has($name) ? 'input-error' : '' }}"
               {{ $attributes ?? '' }}>
    </div>

    @if($errors && $errors->has($name))
        <p class="text-red-600 text-[12px] font-bold mt-1.5">{{ $errors->first($name) }}</p>
    @endif

    @if($hint ?? false)
        <p class="text-slate-400 text-[12px] mt-1.5">{{ $hint }}</p>
    @endif
</div>
