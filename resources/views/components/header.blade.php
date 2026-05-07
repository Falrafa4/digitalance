@php
    $user = Auth::guard('administrator')->user()
        ?? Auth::guard('freelancer')->user()
        ?? Auth::guard('client')->user();

    $segment = request()->segment(1);

    if ($segment === 'admin') {
        $urlProfil = route('admin.profile');
    } elseif ($segment === 'client') {
        $urlProfil = route('client.profile');
    } elseif ($segment === 'freelancer') {
        $urlProfil = route('freelancer.profile');
    } else {
        $urlProfil = route('login');
    }
@endphp

<header class="flex items-center justify-between mb-9">
    <form action="{{ url($segment . '/search') }}" method="GET" class="relative w-[380px] flex items-center gap-2">
        <div class="relative flex-1">
            <i class="ri-search-line absolute left-[15px] top-[11px] text-slate-400 text-[17px] pointer-events-none z-10"></i>
            <input type="text" name="q" placeholder="Cari data (global search)..." autocomplete="off" value="{{ request('q') }}"
                class="w-full py-[11px] pl-[42px] pr-4 bg-white border-[1.5px] border-slate-200 rounded-[13px] text-[13.5px] font-sans outline-none transition-all duration-200 text-slate-900 placeholder:text-slate-400 focus:border-[#0f766e] focus:shadow-[0_3px_14px_rgba(15,118,110,0.1)]" />
        </div>
        <button type="submit" class="w-[45px] h-[45px] bg-teal-600 text-white rounded-[13px] flex items-center justify-center hover:bg-teal-700 hover:shadow-teal-sm transition-all duration-200 shrink-0">
            <i class="ri-search-line text-[18px]"></i>
        </button>
    </form>

    <div class="flex items-center gap-3.5">

        <div class="relative">
            <button id="notif-btn"
                class="w-11 h-11 rounded-xl border-[1.5px] border-slate-200 bg-white cursor-pointer flex items-center justify-center text-slate-500 text-[19px] transition-all duration-200 hover:border-[#0f766e] hover:text-[#0f766e]">
                <i class="ri-notification-3-line pointer-events-none"></i>
                <span
                    class="has-unread absolute top-[9px] right-[9px] w-2 h-2 bg-orange-500 border-2 border-white rounded-full pointer-events-none"></span>
            </button>
        </div>

        <a href="{{ $urlProfil }}"
            class="flex items-center gap-[11px] cursor-pointer hover:opacity-80 transition-opacity">

            <img class="w-[42px] h-[42px] rounded-xl object-cover"
                src="{{ $user?->profile_photo
    ? asset('storage/' . $user->profile_photo)
    : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? $user?->email) . '&background=0f766e&color=fff' }}" alt="{{ $user?->name ?? 'Profile' }}" />

            <div class="hidden sm:flex flex-col">
                <span class="font-bold text-[13.5px] text-slate-800 leading-none">
                    {{ $user?->name ?? $user?->email }}
                </span>
                <span class="text-[11px] text-slate-500 mt-1">
                    @if ($user instanceof App\Models\Administrator)
                        Administrator
                    @elseif ($user instanceof App\Models\Freelancer)
                        Freelancer
                    @elseif ($user instanceof App\Models\Client)
                        Client
                    @else
                        Guest
                    @endif
                </span>
            </div>

        </a>
    </div>
</header>