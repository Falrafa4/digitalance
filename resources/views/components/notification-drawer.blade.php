{{-- Slide-in Notification Drawer (right sidebar) --}}
<div id="notif-drawer"
    class="fixed inset-0 z-[9998] hidden"
    aria-hidden="true">

    {{-- Backdrop --}}
    <div id="notif-backdrop"
        class="absolute inset-0 bg-slate-900/30 backdrop-blur-[2px] opacity-0 transition-opacity duration-200"></div>

    {{-- Panel --}}
    <aside id="notif-panel"
        class="absolute top-0 right-0 h-full w-[380px] max-w-[92vw] bg-white border-l border-slate-200 shadow-2xl translate-x-full transition-transform duration-200 ease-out">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200">
            <div class="min-w-0">
                <h3 class="font-display text-[1.15rem] font-extrabold text-slate-900 truncate">Notifications</h3>
                <p class="text-[12px] text-slate-500 mt-0.5">Update sistem terbaru untuk admin.</p>
            </div>

            <button id="notif-close"
                type="button"
                class="w-10 h-10 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-500 hover:text-slate-900 hover:border-slate-300 transition"
                aria-label="Close notifications">
                <i class="ri-close-line text-[18px]"></i>
            </button>
        </div>

        <div class="p-4 overflow-y-auto h-[calc(100%-80px)]">
            {{-- Slot content (optional) --}}
            @if (isset($notifications) && count($notifications) > 0)
                <div class="flex flex-col gap-2.5">
                    @foreach ($notifications as $n)
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 hover:border-[#10B981] transition">
                            <div class="flex items-start gap-3">
                                <span class="w-9 h-9 rounded-xl bg-[#f0fdf9] text-[#0f766e] flex items-center justify-center flex-shrink-0">
                                    <i class="{{ $n['icon'] ?? 'ri-notification-3-line' }}"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[13.5px] font-bold text-slate-900 truncate">
                                        {{ $n['title'] ?? 'Notification' }}
                                    </p>
                                    <p class="text-[12px] text-slate-500 leading-relaxed mt-0.5">
                                        {{ $n['message'] ?? '' }}
                                    </p>
                                    @if (!empty($n['time']))
                                        <p class="text-[11px] text-slate-400 mt-2">{{ $n['time'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty state --}}
                <div class="text-center py-14 px-6 bg-white border-2 border-dashed border-slate-200 rounded-3xl">
                    <div class="flex items-center justify-center text-[46px] text-slate-300 mb-3">
                        <i class="ri-notification-off-line"></i>
                    </div>
                    <h4 class="font-display text-[1.15rem] text-slate-900 mb-1.5 font-bold">No Notifications</h4>
                    <p class="text-slate-400 text-[13.5px]">Belum ada update baru hari ini.</p>
                </div>
            @endif
        </div>
    </aside>
</div>