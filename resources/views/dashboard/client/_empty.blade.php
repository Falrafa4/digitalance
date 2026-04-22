<div class="border-2 border-dashed border-slate-200 rounded-[18px] p-10 text-center bg-white">
    <div class="text-slate-300 text-[44px] mb-3">
        <i class="{{ $icon ?? 'ri-inbox-2-line' }}"></i>
    </div>
    <p class="text-slate-900 font-extrabold text-[1.25rem]">{{ $title ?? 'Kosong' }}</p>
    @if(!empty($desc))
        <p class="text-slate-500 mt-2">{{ $desc }}</p>
    @endif

    @if(!empty($actionUrl) && !empty($actionLabel))
        <a href="{{ $actionUrl }}"
           class="inline-flex items-center justify-center mt-5 px-5 py-3 rounded-[12px] bg-slate-900 text-white font-bold text-[13px] hover:bg-black transition-all">
            {{ $actionLabel }}
            <i class="ri-arrow-right-line ml-2"></i>
        </a>
    @endif
</div>