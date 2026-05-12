{{-- Universal Empty State for all modules --}}
@props([
    'icon' => 'ri-inbox-line',
    'title' => 'No Data',
    'description' => '',
    'actionUrl' => null,
    'actionLabel' => null,
    'actionIcon' => 'ri-arrow-right-line',
])

<div class="flex flex-col items-center justify-center py-14 px-5 text-center bg-white border-2 border-dashed border-slate-200 rounded-[18px]">
    <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center mb-4 text-slate-300 text-[42px]">
        <i class="{{ $icon }}"></i>
    </div>

    <h3 class="text-[1.15rem] font-extrabold text-slate-900 mb-2">{{ $title }}</h3>

    @if($description)
        <p class="text-slate-500 text-[13.5px] max-w-xs leading-relaxed mb-5">{{ $description }}</p>
    @endif

    @if($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-[12px] bg-[#0f766e] text-white font-bold text-[13px] hover:bg-[#0a5e58] hover:shadow-lg transition-all shadow-teal-sm">
            {{ $actionLabel }} <i class="{{ $actionIcon }}"></i>
        </a>
    @endif
</div>