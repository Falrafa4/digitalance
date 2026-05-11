@php
    // Detect active guard + role (multi-guard safe)
    $isAdmin = Auth::guard('administrator')->check();
    $isClient = Auth::guard('client')->check();
    $isFreelancer = Auth::guard('freelancer')->check();

    $role = $isAdmin ? 'admin' : ($isClient ? 'client' : ($isFreelancer ? 'freelancer' : null));

    // Get the actual logged-in user from the active guard
    $user =
        Auth::guard('administrator')->user()
        ?? Auth::guard('client')->user()
        ?? Auth::guard('freelancer')->user();

    // Fetch from DB
    $dbNotifications = \App\Models\Notification::where(function($q) use ($role, $user) {
        $q->where('role', $role);
        if ($user) {
            $q->orWhere('user_id', $user->id);
        }
    })
    ->latest()
    ->take(10)
    ->get();
@endphp



{{-- Slide-in Notification Drawer --}}
<div id="notif-drawer" class="fixed inset-0 z-[9998] hidden" aria-hidden="true">

    {{-- Backdrop --}}
    <div id="notif-backdrop"
        class="absolute inset-0 bg-slate-900/30 backdrop-blur-[2px] opacity-0 transition-opacity duration-200"></div>

    {{-- Panel --}}
    <aside id="notif-panel"
        class="absolute top-0 right-0 h-full w-[380px] max-w-[92vw] bg-white border-l border-slate-200 shadow-2xl translate-x-full transition-transform duration-200 ease-out"
        role="dialog"
        aria-modal="true"
        aria-label="Notifications"
    >

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200">
            <div class="min-w-0">
                <h3 class="font-display text-[1.15rem] font-extrabold text-slate-900 truncate">
                    Notifications
                </h3>

                <p class="text-[12px] text-slate-500 mt-0.5">
                    @switch($role)
                        @case('administrator')
                            Update sistem terbaru untuk admin.
                            @break
                        @case('client')
                            Notifikasi pekerjaan dan penawaran Anda.
                            @break
                        @case('freelancer')
                            Notifikasi proyek dan pesan klien.
                            @break
                        @default
                            Notifikasi terbaru.
                    @endswitch
                </p>
            </div>

            <button id="notif-close" type="button"
                class="w-10 h-10 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-500 hover:text-slate-900 hover:border-slate-300 transition">
                <i class="ri-close-line text-[18px]"></i>
            </button>
        </div>

        {{-- Content --}}
        <div class="p-4 overflow-y-auto h-[calc(100%-80px)]">

            @if ($dbNotifications->count() > 0)

                <div class="flex flex-col gap-2.5">
                    @foreach ($dbNotifications as $n)
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 hover:border-[#10B981] transition cursor-pointer"
                            @if (!empty($n->link))
                                onclick="window.location.href='{{ url($n->link) }}'"
                            @endif
                        >
                            <div class="flex items-start gap-3">
                                <span class="w-9 h-9 rounded-xl bg-[#f0fdf9] text-[#0f766e] flex items-center justify-center">
                                    <i class="ri-notification-3-line"></i>
                                </span>

                                <div class="flex-1">
                                    <p class="text-[13.5px] font-bold text-slate-900">
                                        {{ $n->title }}
                                    </p>

                                    <p class="text-[12px] text-slate-500 mt-0.5">
                                        {{ $n->message }}
                                    </p>

                                    <p class="text-[11px] text-slate-400 mt-2">
                                        {{ $n->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            @else

                {{-- Empty --}}
                <div class="text-center py-14 px-6 bg-white border-2 border-dashed border-slate-200 rounded-3xl">
                    <i class="ri-notification-off-line text-[46px] text-slate-300 mb-3"></i>
                    <h4 class="font-bold text-slate-900">No Notifications</h4>
                    <p class="text-slate-400 text-[13px]">Belum ada update baru hari ini.</p>
                </div>

            @endif

        </div>
    </aside>
</div>