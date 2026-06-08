@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center !justify-center gap-2 flex-wrap">
        @if ($paginator->onFirstPage())
            <span
                class="min-w-[132px] h-11 px-4 rounded-xl border border-slate-200 bg-white text-[13px] font-bold text-slate-300 cursor-not-allowed select-none inline-flex items-center justify-center">
                Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="min-w-[132px] h-11 px-4 rounded-xl border border-slate-200 bg-white text-[13px] font-bold text-slate-500 hover:bg-slate-50 transition-all inline-flex items-center justify-center">
                Sebelumnya
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="w-11 h-11 flex items-center justify-center text-slate-400 text-[13px] font-bold">
                    {{ $element }}
                </span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span
                            class="w-11 h-11 rounded-xl border border-[#0f766e] bg-[#0f766e] text-white text-[13px] font-bold inline-flex items-center justify-center">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                            class="w-11 h-11 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-[13px] font-bold transition-all inline-flex items-center justify-center">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="min-w-[132px] h-11 px-4 rounded-xl border border-slate-200 bg-white text-[13px] font-bold text-slate-500 hover:bg-slate-50 transition-all inline-flex items-center justify-center">
                Berikutnya
            </a>
        @else
            <span
                class="min-w-[132px] h-11 px-4 rounded-xl border border-slate-200 bg-white text-[13px] font-bold text-slate-300 cursor-not-allowed select-none inline-flex items-center justify-center">
                Berikutnya
            </span>
        @endif
    </nav>
@endif
