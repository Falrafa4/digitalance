@props([
    'type' => 'error', // error | success | warning | info
    'title' => null,
    'dismissible' => true,
])

@php
    $styles = match($type) {
        'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800',
        default => 'bg-red-50 border-red-200 text-red-800',
    };

    $icon = match($type) {
        'success' => 'ri-checkbox-circle-fill text-emerald-500',
        'warning' => 'ri-alert-fill text-amber-500',
        'info' => 'ri-information-fill text-blue-500',
        default => 'ri-error-warning-fill text-red-500',
    };
@endphp

<div role="alert"
     {{ $attributes->merge(['class' => "alert alert-{$type} rounded-xl border px-4 py-3 flex items-start gap-3 {$styles}"]) }}>
    <i class="ri-xl {{ $icon }} flex-shrink-0 mt-0.5"></i>
    <div class="flex-1 text-sm font-semibold">
        @if($title)
            <p class="font-extrabold mb-0.5">{{ $title }}</p>
        @endif
        {{ $slot }}
    </div>
    @if($dismissible)
        <button type="button"
                onclick="this.closest('[role=alert]').remove()"
                class="text-current opacity-60 hover:opacity-100 transition-opacity flex-shrink-0"
                aria-label="Tutup">
            <i class="ri-close-line"></i>
        </button>
    @endif
</div>
