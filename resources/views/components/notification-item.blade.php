@props([
    'type' => 'info',
    'message' => '',
])

@php
    // Map types to classes and icons
    $typeClasses = [
        'success' => [
            'bg' => 'bg-emerald-50 to-white',
            'border' => 'border-emerald-200/60',
            'shadow' => 'shadow-emerald-500/10',
            'iconBg' => 'bg-emerald-500',
            'iconText' => 'text-white',
            'text' => 'text-emerald-800',
            'closeText' => 'text-emerald-400',
            'closeHoverBg' => 'hover:bg-emerald-100/50',
            'closeHoverText' => 'hover:text-emerald-700',
            'icon' => 'ri-check-line',
        ],
        'warning' => [
            'bg' => 'bg-amber-50 to-white',
            'border' => 'border-amber-200/60',
            'shadow' => 'shadow-amber-500/10',
            'iconBg' => 'bg-amber-500',
            'iconText' => 'text-white',
            'text' => 'text-amber-800',
            'closeText' => 'text-amber-400',
            'closeHoverBg' => 'hover:bg-amber-100/50',
            'closeHoverText' => 'hover:text-amber-700',
            'icon' => 'ri-alert-line',
        ],
        'error' => [
            'bg' => 'bg-red-50 to-white',
            'border' => 'border-red-200/60',
            'shadow' => 'shadow-red-500/10',
            'iconBg' => 'bg-red-500',
            'iconText' => 'text-white',
            'text' => 'text-red-800',
            'closeText' => 'text-red-400',
            'closeHoverBg' => 'hover:bg-red-100/50',
            'closeHoverText' => 'hover:text-red-700',
            'icon' => 'ri-error-warning-line',
        ],
        'validation' => [
            'bg' => 'bg-red-50 to-white',
            'border' => 'border-red-200/60',
            'shadow' => 'shadow-red-500/10',
            'iconBg' => 'bg-red-500',
            'iconText' => 'text-white',
            'text' => 'text-red-800',
            'closeText' => 'text-red-400',
            'closeHoverBg' => 'hover:bg-red-100/50',
            'closeHoverText' => 'hover:text-red-700',
            'icon' => 'ri-error-warning-line',
        ],
        'info' => [
            'bg' => 'bg-blue-50 to-white',
            'border' => 'border-blue-200/60',
            'shadow' => 'shadow-blue-500/10',
            'iconBg' => 'bg-blue-500',
            'iconText' => 'text-white',
            'text' => 'text-blue-800',
            'closeText' => 'text-blue-400',
            'closeHoverBg' => 'hover:bg-blue-100/50',
            'closeHoverText' => 'hover:text-blue-700',
            'icon' => 'ri-information-line',
        ],
    ];

    $classes = $typeClasses[$type] ?? $typeClasses['info'];
@endphp

<div data-flash="{{ $type }}"
     class="group max-w-sm flex items-start gap-3 px-5 py-4 rounded-2xl shadow-xl {{ $classes.shadow }} border {{ $classes.border }} bg-gradient-to-r {{ $classes.bg }} backdrop-blur-sm pointer-events-auto"
     role="{{ $type === 'error' || $type === 'validation' ? 'alert' : 'status' }}">
    <div class="w-8 h-8 rounded-xl {{ $classes.iconBg }} {{ $classes.iconText }} flex items-center justify-center flex-shrink-0 shadow-sm">
        <i class="{{ $classes.icon }} text-[16px]"></i>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-[13px] font-bold {{ $classes.text }} leading-snug">{{ $message }}</p>
    </div>
    <button type="button" aria-label="Tutup" onclick="this.closest('[data-flash]').remove()"
        class="w-7 h-7 rounded-lg flex items-center justify-center {{ $classes.closeText }} {{ $classes.closeHoverBg }} {{ $classes.closeHoverText }} transition-all flex-shrink-0 opacity-0 group-hover:opacity-100">
        <i class="ri-close-line text-[14px]"></i>
    </button>
</div>