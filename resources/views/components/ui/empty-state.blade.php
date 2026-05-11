<div class="text-center py-10 px-5">
    <div class="text-slate-300 text-[48px] mb-4">
        <i class="{{ $icon ?? 'ri-inbox-2-line' }}"></i>
    </div>
    <p class="text-slate-900 font-extrabold text-[1.1rem]">{{ $title ?? 'No data' }}</p>
    <p class="text-slate-500 text-[13.5px] mt-1">{{ $description ?? '' }}</p>
    @if($actionUrl ?? $slot->isNotEmpty())
        <div class="mt-5">
            {{ $slot->isNotEmpty() ? $slot : '' }}
            @if($actionUrl ?? false)
                <a href="{{ $actionUrl }}"
                   class="{{ $actionClass ?? 'inline-flex items-center justify-center px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all' }}">
                    @if(isset($actionIcon))
                        <i class="{{ $actionIcon }} mr-2"></i>
                    @endif
                    {{ $actionLabel ?? 'Action' }}
                </a>
            @endif
        </div>
    @endif
</div>
