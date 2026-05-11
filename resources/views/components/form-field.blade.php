{{-- Standardized Form Field Input --}}
@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'errors' => null,
])

@php
    $hasError = $errors && $errors->has($name);
    $errorClass = $hasError ? 'border-red-300 bg-red-50' : 'border-slate-200';
@endphp

<div class="mb-6">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-slate-700 mb-2">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            class="w-full px-4 py-3 border rounded-lg font-sans text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0f766e] focus:border-transparent resize-none {{ $errorClass }}"
            rows="4">{{ $value }}</textarea>
    @elseif ($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            class="w-full px-4 py-3 border rounded-lg font-sans text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0f766e] focus:border-transparent {{ $errorClass }}">
            <option value="">{{ $placeholder ?: 'Select an option' }}</option>
            {{ $slot }}
        </select>
    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            class="w-full px-4 py-3 border rounded-lg font-sans text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0f766e] focus:border-transparent {{ $errorClass }}" />
    @endif

    @if ($hasError)
        <p class="mt-2 text-sm text-red-600">
            <i class="ri-error-warning-line mr-1"></i>
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
