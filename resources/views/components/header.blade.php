{{-- Header untuk dashboard --}}
<header class="flex items-center justify-between mb-9">
    <div class="relative w-[340px]" id="search-wrapper">
        <i class="ri-search-line absolute left-[15px] top-2 text-slate-400 text-[17px] pointer-events-none z-10"></i>
        <input id="search-input" type="text" placeholder="Search menu, halaman..." autocomplete="off"
            class="w-full py-[11px] pl-[42px] pr-4 bg-white border-[1.5px] border-slate-200 rounded-[13px] text-[13.5px] font-sans outline-none transition-all duration-200 text-slate-900 placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_3px_14px_rgba(15,118,110,0.1)]" />
        <!-- Dropdown -->
        <div id="search-dropdown"
            class="absolute top-[calc(100%+6px)] left-0 w-full bg-white border border-slate-200 rounded-[13px] shadow-lg z-50 overflow-hidden hidden">
            <div id="search-results" class="flex flex-col py-1.5 max-h-[320px] overflow-y-auto"></div>
        </div>
    </div>

    <div class="flex items-center gap-3.5">
        <button id="notif-btn"
            class="w-11 h-11 rounded-xl border-[1.5px] border-slate-200 bg-white cursor-pointer relative flex items-center justify-center text-slate-500 text-[19px] transition-all duration-200 hover:border-[#0f766e] hover:text-[#0f766e]">
            <i class="ri-notification-3-line"></i>
            <span
                class="notif-dot absolute top-[9px] right-[9px] w-2 h-2 bg-orange-500 border-2 border-white rounded-full"></span>
        </button>

        <div class="flex items-center gap-[11px] cursor-pointer">
            <img class="w-[42px] h-[42px] rounded-xl object-cover" src="https://picsum.photos/seed/admin/100/100"
                alt="Admin Profile" />
            <div class="flex flex-col">
                <span
                    class="font-bold text-[13.5px] text-slate-800">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                <span class="text-[11px] text-slate-500">
                    @if (Auth::user() instanceof App\Models\Administrator)
                        Administrator
                    @elseif (Auth::user() instanceof App\Models\Freelancer)
                        Freelancer
                    @else
                        Client
                    @endif
                </span>
            </div>
        </div>
    </div>
</header>
