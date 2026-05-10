<input
    type="{{ $type ?? 'text' }}"
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    value="{{ $value ?? old($name, $value ?? '') }}"
    placeholder="{{ $placeholder ?? '' }}"
    {{ $required ?? false ? 'required' : '' }}
    {{ $disabled ?? false ? 'disabled' : '' }}
    {{ $readonly ?? false ? 'readonly' : '' }}
    class="w-full px-4 py-2.5 rounded-[12px] border border-slate-200 text-[14px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 placeholder:font-normal placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]
    {{ $errors && $errors->has($name) ? 'input-error' : '' }}"
    {{ $attributes ?? '' }}
/>
@if($errors && $errors->has($name))
    <p class="text-red-600 text-[12px] font-bold mt-1.5">{{ $errors->first($name) }}</p>
@endif
