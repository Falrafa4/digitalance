@props([
    'type' => 'submit',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'href' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 font-bold text-[13px] transition-all duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed';

$variantClasses = match($variant) {
    'primary' => 'px-5 py-2.5 rounded-[12px] bg-slate-900 text-white hover:bg-black',
    'secondary' => 'px-5 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 hover:border-[#0f766e] hover:text-[#0f766e]',
    'danger' => 'px-5 py-2.5 rounded-[12px] bg-red-50 border border-red-200 text-red-600 hover:bg-red-100',
    'teal' => 'px-5 py-2.5 rounded-[12px] bg-[#0f766e] text-white hover:bg-[#0d6b63]',
    'ghost' => 'px-4 py-2 text-slate-600 hover:text-slate-900',
    default => 'px-5 py-2.5 rounded-[12px] bg-slate-900 text-white hover:bg-black',
};

$sizeClasses = match($size) {
    'sm' => 'px-3 py-1.5 text-[12px]',
    'md' => 'px-5 py-2.5 text-[13px]',
    'lg' => 'px-6 py-3 text-[14px]',
    default => 'px-5 py-2.5 text-[13px]',
};

$classes = trim($baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses);
@endphp

@if($href)
    <a href="{{ $href }}"
       class="{{ $classes }}"
       {{ $disabled ? 'aria-disabled="true"' : '' }}
       {{ $attributes->class($loading ? 'btn-loading' : '') }}>
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $disabled || $loading ? 'disabled' : '' }}
        class="{{ $classes }} {{ $loading ? 'btn-loading' : '' }}"
        {{ $attributes }}>
        {{ $slot }}
    </button>
@endif
