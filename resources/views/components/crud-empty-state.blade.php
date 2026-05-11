{{-- Standardized CRUD Empty State --}}
@props([
    'icon' => 'ri-inbox-line',
    'title' => 'No Data',
    'description' => '',
    'actionUrl' => null,
    'actionLabel' => 'Create New',
    'actionIcon' => 'ri-add-line',
])

<div class="flex flex-col items-center justify-center py-16 px-4">
    <div class="mb-4">
        <i class="text-5xl text-slate-300 {{ $icon }}"></i>
    </div>

    <h3 class="text-lg font-semibold text-slate-700 mb-2">{{ $title }}</h3>

    @if ($description)
        <p class="text-slate-500 text-center mb-6 max-w-sm">{{ $description }}</p>
    @endif

    @if ($actionUrl)
        <a href="{{ $actionUrl }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#0f766e] text-white font-semibold hover:bg-teal-800 transition-colors">
            <i class="{{ $actionIcon }}"></i>
            {{ $actionLabel }}
        </a>
    @endif
</div>
