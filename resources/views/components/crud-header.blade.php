{{-- Universal Page Header Component --}}
@props([
    'title' => '',
    'subtitle' => '',
    'count' => null,
    'countLabel' => null,
    'actionUrl' => null,
    'actionLabel' => null,
    'actionIcon' => 'ri-arrow-right-line',
])

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-8 animate-fadeUp">
    <div class="min-w-0 flex-1">
        <h1 class="font-display text-[1.85rem] sm:text-[2.1rem] font-extrabold text-slate-900 leading-tight">
            {{ $title }}
            @if($subtitle)
                <span class="block text-[0.95rem] font-normal text-slate-500 mt-1">{{ $subtitle }}</span>
            @endif
        </h1>
        @if($count !== null && $countLabel)
            <div class="mt-3 flex items-center gap-2">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[12px] font-bold">{{ $count }}</span>
                <span class="text-[12px] text-slate-500 font-semibold">{{ $countLabel }}</span>
            </div>
        @endif
    </div>

    @if($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}"
           class="px-5 py-2.5 bg-[#0f766e] text-white font-bold text-[13px] rounded-[12px] shadow-teal-sm hover:bg-[#0a5e58] transition-all flex items-center gap-2 shrink-0">
            {{ $actionLabel }} <i class="{{ $actionIcon }}"></i>
        </a>
    @endif
</div>