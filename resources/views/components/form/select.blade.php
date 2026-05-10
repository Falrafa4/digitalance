<select
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    {{ $required ?? false ? 'required' : '' }}
    {{ $disabled ?? false ? 'disabled' : '' }}
    class="w-full px-4 py-2.5 rounded-[12px] border border-slate-200 text-[14px] font-semibold text-slate-700 bg-white outline-none transition-all duration-200 focus:border-[#0f766e] focus:shadow-[0_0_0_3px_rgba(15,118,110,0.08)]
    {{ $errors && $errors->has($name) ? 'input-error' : '' }}"
    {{ $attributes ?? '' }}
>
    @if($placeholder ?? false)
        <option value="" disabled {{ !$value ? 'selected' : '' }}>{{ $placeholder }}</option>
    @endif
    {{ $slot }}
</select>
@if($errors && $errors->has($name))
    <p class="text-red-600 text-[12px] font-bold mt-1.5">{{ $errors->first($name) }}</p>
@endif
