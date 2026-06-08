@php
    $dbNotifications = isset($notifNotifications) ? $notifNotifications : collect();
    $unreadCount = isset($notifUnreadCount) ? (int) $notifUnreadCount : 0;

    $filter = isset($filter) ? $filter : 'all';
    $q = isset($q) ? $q : '';
    $category = isset($category) ? $category : '';
    $keptCount = isset($keptCount) ? (int) $keptCount : 0;
    $archivedCount = isset($archivedCount) ? (int) $archivedCount : 0;
    $allCount = isset($allCount) ? (int) $allCount : 0;
    if ($allCount === 0 && $dbNotifications instanceof \Illuminate\Contracts\Pagination\Paginator) {
        $allCount = (int) $dbNotifications->total();
    } elseif ($allCount === 0) {
        $allCount = $dbNotifications->count();
    }
@endphp

{{-- Overlay --}}
<div id="notif-overlay"
    class="fixed inset-0 z-[9998] hidden"
    aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/30 backdrop-blur-sm"
        onclick="closeNotificationDrawer()"></div>
</div>

{{-- Drawer --}}
<aside id="notif-panel"
    class="fixed top-0 right-0 h-full w-[420px] max-w-[95vw] bg-white shadow-[-8px_0_30px_rgba(0,0,0,0.08)] translate-x-full transition-transform duration-300 ease-out flex flex-col z-[9999]"
    role="dialog"
    aria-modal="true"
    aria-label="Notifikasi">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-slate-100 flex-shrink-0">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 flex items-center justify-center text-white shadow-sm flex-shrink-0">
                    <i class="ri-notification-3-fill text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-display text-[1.05rem] font-extrabold text-slate-900 leading-tight">Notifikasi</h3>
                    <p id="notif-header-status"
                        class="text-[11px] font-semibold mt-0.5 {{ $unreadCount > 0 ? 'text-teal-600' : 'text-slate-400' }}">
                        {{ $unreadCount > 0 ? $unreadCount.' belum dibaca' : 'Semua sudah dibaca' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1.5 flex-shrink-0">
                <button id="notif-bulk-toggle" type="button"
                    class="hidden md:inline-flex w-9 h-9 rounded-xl border border-slate-200 bg-white items-center justify-center text-slate-500 hover:text-slate-800 hover:border-slate-300 transition-all shadow-sm"
                    title="Pilih beberapa notifikasi"
                    onclick="toggleNotificationBulk()">
                    <i class="ri-checkbox-multiple-line text-[16px]"></i>
                </button>

                @if ($unreadCount > 0)
                    <button id="notif-mark-all-btn" type="button"
                        class="px-3 py-1.5 rounded-lg text-[11px] font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 border border-teal-100 transition-all"
                        onclick="markAllNotificationsRead(event)">
                        <i class="ri-check-double-line mr-0.5"></i> Tandai Dibaca
                    </button>
                @endif

                <button type="button" onclick="closeNotificationDrawer()"
                    class="w-9 h-9 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-400 hover:text-slate-700 hover:border-slate-300 transition-all shadow-sm"
                    aria-label="Tutup">
                    <i class="ri-close-line text-[16px]"></i>
                </button>
            </div>
        </div>

        {{-- Search --}}
        <form id="notif-search-form" class="mt-4" onsubmit="return submitNotificationSearch(event)">
            <div class="relative">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[15px] pointer-events-none"></i>
                <input id="notif-search-input" type="text" name="q" value="{{ $q }}"
                    placeholder="Cari notifikasi..."
                    class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-[13px] outline-none focus:border-[#0f766e] focus:bg-white focus:shadow-[0_2px_10px_rgba(15,118,110,0.08)] transition-all"
                    autocomplete="off">
            </div>
        </form>

        {{-- Tabs filter --}}
        <div class="mt-4 flex items-center gap-1 overflow-x-auto pb-1 -mx-1 px-1" id="notif-tabs">
            <button type="button" data-filter="all"
                onclick="switchNotificationFilter('all')"
                class="notif-tab flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all {{ $filter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
                <i class="ri-inbox-line text-[14px]"></i>
                <span>Semua</span>
                <span class="text-[10px] opacity-80 notif-tab-count">{{ $allCount }}</span>
            </button>
            <button type="button" data-filter="unread"
                onclick="switchNotificationFilter('unread')"
                class="notif-tab flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all {{ $filter === 'unread' ? 'bg-teal-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
                <i class="ri-mail-unread-line text-[14px]"></i>
                <span>Belum Dibaca</span>
                <span class="text-[10px] opacity-80 notif-tab-count" id="notif-tab-unread-count">{{ $unreadCount }}</span>
            </button>
            <button type="button" data-filter="kept"
                onclick="switchNotificationFilter('kept')"
                class="notif-tab flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all {{ $filter === 'kept' ? 'bg-amber-500 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
                <i class="ri-bookmark-line text-[14px]"></i>
                <span>Disimpan</span>
                <span class="text-[10px] opacity-80 notif-tab-count">{{ $keptCount }}</span>
            </button>
            <button type="button" data-filter="archived"
                onclick="switchNotificationFilter('archived')"
                class="notif-tab flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all {{ $filter === 'archived' ? 'bg-slate-700 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
                <i class="ri-archive-line text-[14px]"></i>
                <span>Arsip</span>
                <span class="text-[10px] opacity-80 notif-tab-count">{{ $archivedCount }}</span>
            </button>
        </div>

        {{-- Bulk action bar (hidden by default) --}}
        <div id="notif-bulk-bar"
            class="hidden mt-3 px-3 py-2 rounded-lg bg-slate-900 text-white flex items-center gap-2 text-[12px]">
            <span class="font-bold"><span id="notif-bulk-count">0</span> dipilih</span>
            <span class="opacity-40">|</span>
            <button type="button" onclick="bulkNotificationAction('mark_read')"
                class="hover:text-teal-300 font-semibold">Tandai dibaca</button>
            <button type="button" onclick="bulkNotificationAction('archive')"
                class="hover:text-amber-300 font-semibold">Arsipkan</button>
            <button type="button" onclick="bulkNotificationAction('keep')"
                class="hover:text-yellow-300 font-semibold">Simpan</button>
            <button type="button" onclick="bulkNotificationAction('delete')"
                class="hover:text-red-300 font-semibold">Hapus</button>
            <button type="button" onclick="toggleNotificationBulk()"
                class="ml-auto opacity-70 hover:opacity-100">
                <i class="ri-close-line"></i>
            </button>
        </div>
    </div>

    {{-- Notification List --}}
    <div class="flex-1 overflow-y-auto" id="notif-list"
        data-filter="{{ $filter }}"
        data-category="{{ $category }}"
        data-q="{{ $q }}">
        @if ($dbNotifications->count() > 0)
            @foreach ($dbNotifications as $n)
                @php
                    $icon = $n->icon ?? \App\Models\Notification::TYPES[0];
                    $iconColor = $n->icon_color ?? 'bg-blue-100 text-blue-600';
                    $rowClass = $n->is_read
                        ? 'bg-white hover:bg-slate-50'
                        : 'bg-teal-50 hover:bg-teal-100';
                    $href = $n->link ?: '#';
                @endphp
                <div class="notif-item group px-5 py-4 border-b border-slate-50 transition-all duration-200 {{ $rowClass }}"
                    data-id="{{ $n->id }}"
                    data-link="{{ $href }}"
                    data-read="{{ $n->is_read ? '1' : '0' }}"
                    data-kept="{{ $n->is_kept ? '1' : '0' }}"
                    data-archived="{{ $n->is_archived ? '1' : '0' }}"
                    data-type="{{ $n->type }}"
                    data-category="{{ $n->category ?? '' }}">

                    <div class="flex items-start gap-3">
                        {{-- Checkbox (muncul saat mode pilih) --}}
                        <label class="notif-checkbox-wrap hidden flex-shrink-0 mt-2 cursor-pointer">
                            <input type="checkbox" class="notif-checkbox w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500" />
                        </label>

                        {{-- Ikon --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $iconColor }}">
                            <i class="{{ $icon }} text-lg"></i>
                        </div>

                        {{-- Body --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-bold text-[13px] text-slate-900 leading-tight">
                                    {{ $n->title }}
                                </p>
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    @if ($n->is_kept)
                                        <button data-notif-id="{{ $n->id }}" type="button"
                                            class="notif-keep-btn w-6 h-6 rounded-md flex items-center justify-center text-amber-500 hover:bg-amber-50 transition-all"
                                            title="Lepas dari simpanan"
                                            aria-label="Lepas dari simpanan">
                                            <i class="ri-bookmark-fill text-sm"></i>
                                        </button>
                                    @else
                                        <button data-notif-id="{{ $n->id }}" type="button"
                                            class="notif-keep-btn w-6 h-6 rounded-md flex items-center justify-center text-slate-300 hover:bg-slate-50 hover:text-amber-500 transition-all"
                                            title="Simpan notifikasi"
                                            aria-label="Simpan notifikasi">
                                            <i class="ri-bookmark-line text-sm"></i>
                                        </button>
                                    @endif
                                    @if (!$n->is_read)
                                        <span class="notif-unread-dot w-2 h-2 rounded-full bg-teal-500 flex-shrink-0 mt-1.5" title="Belum dibaca"></span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-[12px] text-slate-500 mt-1 leading-relaxed line-clamp-2">
                                {{ $n->message }}
                            </p>

                            {{-- Meta + actions --}}
                            <div class="flex items-center justify-between gap-2 mt-2">
                                <span class="text-[10px] text-slate-400 font-semibold notif-time">
                                    {{ $n->created_at->diffForHumans() }}
                                </span>

                                <div class="notif-actions flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if (!$n->is_read)
                                        <button type="button"
                                            data-notif-action="read" data-notif-id="{{ $n->id }}"
                                            class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-teal-50 hover:text-teal-600 transition-all"
                                            title="Tandai dibaca"
                                            aria-label="Tandai dibaca">
                                            <i class="ri-check-line text-[14px]"></i>
                                        </button>
                                    @else
                                        <button type="button"
                                            data-notif-action="unread" data-notif-id="{{ $n->id }}"
                                            class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-all"
                                            title="Tandai belum dibaca"
                                            aria-label="Tandai belum dibaca">
                                            <i class="ri-mail-unread-line text-[14px]"></i>
                                        </button>
                                    @endif
                                    @if (!$n->is_archived)
                                        <button type="button"
                                            data-notif-action="archive" data-notif-id="{{ $n->id }}"
                                            class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all"
                                            title="Arsipkan"
                                            aria-label="Arsipkan">
                                            <i class="ri-archive-line text-[14px]"></i>
                                        </button>
                                    @else
                                        <button type="button"
                                            data-notif-action="unarchive" data-notif-id="{{ $n->id }}"
                                            class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all"
                                            title="Kembalikan dari arsip"
                                            aria-label="Kembalikan dari arsip">
                                            <i class="ri-inbox-unarchive-line text-[14px]"></i>
                                        </button>
                                    @endif
                                    <button type="button"
                                        data-notif-action="delete" data-notif-id="{{ $n->id }}"
                                        class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-600 transition-all"
                                        title="Hapus"
                                        aria-label="Hapus">
                                        <i class="ri-delete-bin-line text-[14px]"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Load more (infinite scroll trigger) --}}
            @if ($dbNotifications instanceof \Illuminate\Contracts\Pagination\Paginator && $dbNotifications->hasMorePages())
                <div id="notif-loadmore-trigger" class="p-4 text-center text-[11px] font-semibold text-slate-400 uppercase tracking-widest">
                    <div class="inline-flex items-center gap-2">
                        <i class="ri-loader-4-line animate-spin"></i> Memuat...
                    </div>
                </div>
            @endif
        @else
            <div class="flex flex-col items-center justify-center py-20 text-center px-8">
                <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center mb-5">
                    <i class="ri-notification-off-line text-4xl text-slate-300"></i>
                </div>
                <h4 class="font-display font-bold text-slate-700 text-[1rem] mb-1">Tidak Ada Notifikasi</h4>
                <p class="text-slate-400 text-[13px]">
                    @if ($filter === 'unread')
                        Semua notifikasi sudah ditandai dibaca.
                    @elseif ($filter === 'kept')
                        Belum ada notifikasi yang disimpan.
                    @elseif ($filter === 'archived')
                        Belum ada notifikasi di arsip.
                    @elseif (!empty($q))
                        Tidak ada hasil untuk "{{ $q }}".
                    @else
                        Update terbaru akan muncul di sini.
                    @endif
                </p>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="px-6 py-3 border-t border-slate-100 flex-shrink-0 flex items-center justify-end gap-2">
        <button type="button" id="notif-clear-all-btn"
            class="text-[11px] font-bold text-slate-400 hover:text-red-600 uppercase tracking-widest transition-colors"
            onclick="clearAllNotifications()"
            title="Bersihkan semua notifikasi aktif (akan diarsipkan)">
            <i class="ri-delete-bin-2-line mr-0.5"></i> Bersihkan
        </button>
    </div>
</aside>