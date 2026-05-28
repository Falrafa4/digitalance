@php
    $dbNotifications = isset($notifNotifications) ? $notifNotifications : collect();
    $unreadCount = isset($notifUnreadCount) ? $notifUnreadCount : 0;
@endphp

{{-- Slide-in Notification Drawer --}}
<div id="notif-overlay"
    class="fixed inset-0 z-[9998] hidden"
    aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/30 backdrop-blur-sm"
        onclick="closeNotificationDrawer()"></div>
</div>

<aside id="notif-panel"
    class="fixed top-0 right-0 h-full w-[400px] max-w-[95vw] bg-white shadow-[-8px_0_30px_rgba(0,0,0,0.08)] translate-x-full transition-transform duration-300 ease-out flex flex-col z-[9999]"
    role="dialog"
    aria-modal="true"
    aria-label="Notifikasi">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 flex items-center justify-center text-white shadow-sm">
                <i class="ri-notification-3-fill text-lg"></i>
            </div>
            <div>
                <h3 class="font-display text-[1.05rem] font-extrabold text-slate-900 leading-tight">Notifikasi</h3>
                <p class="text-[11px] font-semibold mt-0.5 {{ $unreadCount > 0 ? 'text-teal-600' : 'text-slate-400' }}">
                    {{ $unreadCount > 0 ? $unreadCount.' belum dibaca' : 'Semua sudah dibaca' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if ($unreadCount > 0)
                <button onclick="markAllNotificationsRead(event)"
                    class="px-3 py-1.5 rounded-lg text-[11px] font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-100 transition-all">
                    <i class="ri-check-double-line mr-0.5"></i> Tandai Baca
                </button>
            @endif
            <button onclick="closeNotificationDrawer()"
                class="w-9 h-9 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-400 hover:text-slate-700 hover:border-slate-300 transition-all shadow-sm">
                <i class="ri-close-line text-[16px]"></i>
            </button>
        </div>
    </div>

    {{-- Notification List --}}
    <div class="flex-1 overflow-y-auto" id="notif-list">
        @if ($dbNotifications->count() > 0)
            @foreach ($dbNotifications as $n)
                <div class="notif-item px-5 py-4 border-b border-slate-50 transition-all duration-200 cursor-pointer
                    {{ $n->is_read ? 'bg-white hover:bg-slate-50' : 'bg-teal-50/40 hover:bg-teal-50/70' }}"
                    @if (!empty($n->link))
                        onclick="window.location.href='{{ url($n->link) }}'"
                    @endif
                    data-id="{{ $n->id }}">

            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                    @if ($n->type === 'success' || $n->type === 'approved') bg-emerald-100 text-emerald-600
                    @elseif ($n->type === 'danger' || $n->type === 'rejected') bg-red-100 text-red-500
                    @elseif ($n->type === 'warning') bg-amber-100 text-amber-600
                    @else bg-blue-100 text-blue-600 @endif">
                    @if ($n->type === 'success' || $n->type === 'approved')
                        <i class="ri-checkbox-circle-fill text-lg"></i>
                    @elseif ($n->type === 'danger' || $n->type === 'rejected')
                        <i class="ri-error-warning-fill text-lg"></i>
                    @elseif ($n->type === 'warning')
                        <i class="ri-alert-fill text-lg"></i>
                    @else
                        <i class="ri-information-fill text-lg"></i>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-bold text-[13px] text-slate-900 leading-tight">
                            {{ $n->title }}
                        </p>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            @if ($n->is_kept)
                                <button data-notif-id="{{ $n->id }}" class="notif-keep-btn w-6 h-6 rounded-md flex items-center justify-center text-amber-500 hover:bg-amber-50 transition-all" title="Lepas notifikasi">
                                    <i class="ri-bookmark-fill text-sm"></i>
                                </button>
                            @else
                                <button data-notif-id="{{ $n->id }}" class="notif-keep-btn w-6 h-6 rounded-md flex items-center justify-center text-slate-300 hover:bg-slate-50 hover:text-amber-500 transition-all" title="Simpan notifikasi">
                                    <i class="ri-bookmark-line text-sm"></i>
                                </button>
                            @endif
                            @if (!$n->is_read)
                                <span class="w-2 h-2 rounded-full bg-teal-500 flex-shrink-0 mt-1.5"></span>
                            @endif
                        </div>
                    </div>
                    <p class="text-[12px] text-slate-500 mt-1 leading-relaxed line-clamp-2">
                        {{ $n->message }}
                    </p>
                    <span class="text-[10px] text-slate-400 font-semibold mt-2 block">
                        {{ $n->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
                </div>
            @endforeach
        @else
            <div class="flex flex-col items-center justify-center py-20 text-center px-8">
                <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center mb-5">
                    <i class="ri-notification-off-line text-4xl text-slate-300"></i>
                </div>
                <h4 class="font-display font-bold text-slate-700 text-[1rem] mb-1">Tidak Ada Notifikasi</h4>
                <p class="text-slate-400 text-[13px]">Update terbaru akan muncul di sini.</p>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="px-6 py-4 border-t border-slate-100 flex-shrink-0">
        <p class="text-[11px] text-center text-slate-400 font-bold uppercase tracking-widest">
            Digitalance Notifications
        </p>
    </div>
</aside>

