{{-- Standardized CRUD List Page Header --}}
@props([
    'title' => 'Resource',
    'subtitle' => '',
    'count' => null,
    'countLabel' => 'Total',
    'actionUrl' => null,
    'actionLabel' => 'Create New',
    'actionIcon' => 'ri-add-line',
])

<div class="mb-8">
    <div class="flex items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold">{{ $title }}</h1>
            @if ($subtitle)
                <p class="text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            @if ($count !== null)
                <div class="bg-slate-50 border border-slate-200 rounded-lg px-4 py-3">
                    <p class="text-xs font-semibold text-slate-500 mb-1">{{ $countLabel }}</p>
                    <p class="text-2xl font-bold text-[#0f766e]">{{ $count }}</p>
                </div>
            @endif

            @if ($actionUrl)
                <a href="{{ $actionUrl }}"
                   class="inline-flex items-center gap-2 px-4 py-3 rounded-lg bg-[#0f766e] text-white font-semibold hover:bg-teal-800 transition-colors whitespace-nowrap">
                    <i class="text-lg {{ $actionIcon }}"></i>
                    {{ $actionLabel }}
                </a>
            @endif
        </div>
    </div>

    {{ $slot }}
</div>
