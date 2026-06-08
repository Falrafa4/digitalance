/**
 * Notification Drawer — handler lengkap.
 *
 * Fitur:
 *  - Buka/tutup drawer (animasi slide + ESC)
 *  - Tandai SATU notifikasi dibaca/belum dibaca
 *  - Toggle bookmark (disimpan/dilepas)
 *  - Arsipkan & batal arsip
 *  - Hapus permanen
 *  - Tandai semua dibaca
 *  - Bersihkan semua (soft archive / hard delete)
 *  - Aksi massal (bulk) dengan checkbox
 *  - Filter tab (semua, belum dibaca, disimpan, arsip) - stay in drawer
 *  - Search (stay in drawer)
 *  - Polling otomatis setiap 30 detik untuk sinkronisasi
 *  - Update badge unread di header
 *
 * Semua operasi filter dan search dilakukan dalam drawer tanpa navigasi ke halaman history.
 */
(function () {
    'use strict';

    var POLL_INTERVAL = 30000;
    var SEARCH_DEBOUNCE = 350;
    var seenIds = new Set();
    var bulkMode = false;
    var lastFetchAt = 0;
    var searchTimer = null;

    // =========================================================================
    // Util
    // =========================================================================
    function getCsrf() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta && meta.content) return meta.content;
        var input = document.querySelector('input[name="_token"]');
        return input ? input.value : '';
    }

    function $(sel, ctx) { return (ctx || document).querySelector(sel); }
    function $$(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

    function showToast(message, type) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type || 'info');
        }
    }

    function fetchJson(url, options) {
        options = options || {};
        options.headers = Object.assign({
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrf(),
        }, options.headers || {});
        return fetch(url, options).then(function (res) {
            var ct = res.headers.get('content-type') || '';
            if (ct.includes('application/json')) {
                return res.json().then(function (data) {
                    if (!res.ok) throw new Error((data && (data.message || data.error)) || ('Request gagal (' + res.status + ')'));
                    return data;
                });
            }
            if (!res.ok) throw new Error('Request gagal (' + res.status + ').');
            return null;
        });
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&')
            .replace(/</g, '<')
            .replace(/>/g, '>')
            .replace(/"/g, '"')
            .replace(/'/g, '&#039;');
    }

    function iconForType(type) {
        switch (type) {
            case 'success': case 'approved': return 'ri-checkbox-circle-fill';
            case 'danger': case 'rejected': return 'ri-error-warning-fill';
            case 'warning': return 'ri-alert-fill';
            case 'sent': return 'ri-send-plane-fill';
            default: return 'ri-information-fill';
        }
    }

    function colorForType(type) {
        switch (type) {
            case 'success': case 'approved': return 'bg-emerald-100 text-emerald-600';
            case 'danger': case 'rejected': return 'bg-red-100 text-red-500';
            case 'warning': return 'bg-amber-100 text-amber-600';
            case 'sent': return 'bg-indigo-100 text-indigo-600';
            default: return 'bg-blue-100 text-blue-600';
        }
    }

    // =========================================================================
    // Badge + header status update
    // =========================================================================
    function updateBadge(unread) {
        var notifBtn = document.getElementById('notif-btn');
        if (!notifBtn) return;
        var existing = notifBtn.querySelector('.has-unread');
        if (unread > 0 && !existing) {
            var span = document.createElement('span');
            span.className = 'has-unread absolute top-[9px] right-[9px] w-2 h-2 bg-red-500 border-2 border-white rounded-full pointer-events-none';
            notifBtn.appendChild(span);
        } else if (unread === 0 && existing) {
            existing.remove();
        }
    }

    function updateHeaderStatus(unread) {
        var header = document.getElementById('notif-header-status');
        if (header) {
            if (unread > 0) {
                header.classList.remove('text-slate-400');
                header.classList.add('text-teal-600');
                header.textContent = unread + ' belum dibaca';
            } else {
                header.classList.remove('text-teal-600');
                header.classList.add('text-slate-400');
                header.textContent = 'Semua sudah dibaca';
            }
        }
        var tabCount = document.getElementById('notif-tab-unread-count');
        if (tabCount) tabCount.textContent = unread;
        var markAllBtn = document.getElementById('notif-mark-all-btn');
        if (markAllBtn) {
            if (unread > 0) {
                markAllBtn.classList.remove('hidden');
            } else {
                markAllBtn.classList.add('hidden');
            }
        }
    }

    function updateTabCounts(data) {
        if (data.unread_count !== undefined) {
            var el = document.getElementById('notif-tab-unread-count');
            if (el) el.textContent = data.unread_count;
        }
        if (data.kept_count !== undefined) {
            var el = document.querySelector('#notif-tabs .notif-tab[data-filter="kept"] .notif-tab-count');
            if (el) el.textContent = data.kept_count;
        }
        if (data.archived_count !== undefined) {
            var el = document.querySelector('#notif-tabs .notif-tab[data-filter="archived"] .notif-tab-count');
            if (el) el.textContent = data.archived_count;
        }
        if (data.all_count !== undefined) {
            var el = document.querySelector('#notif-tabs .notif-tab[data-filter="all"] .notif-tab-count');
            if (el) el.textContent = data.all_count;
        }
    }

    // =========================================================================
    // Build node (untuk item baru dari polling)
    // =========================================================================
    function buildNotificationNode(n) {
        var wrapper = document.createElement('div');
        var rowClass = n.is_read ? 'bg-white hover:bg-slate-50' : 'bg-teal-50 hover:bg-teal-100';
        wrapper.className = 'notif-item group px-5 py-4 border-b border-slate-50 transition-all duration-200 ' + rowClass;
        wrapper.dataset.id = n.id;
        wrapper.dataset.link = n.link || '';
        wrapper.dataset.read = n.is_read ? '1' : '0';
        wrapper.dataset.kept = n.is_kept ? '1' : '0';
        wrapper.dataset.archived = n.is_archived ? '1' : '0';
        wrapper.dataset.type = n.type || 'info';
        wrapper.dataset.category = n.category || '';

        var icon = n.icon || iconForType(n.type);
        var iconColor = colorForType(n.type);
        var link = n.link || '#';

        // Create checkbox wrapper
        var checkboxWrapper = document.createElement('label');
        checkboxWrapper.className = 'notif-checkbox-wrap hidden flex-shrink-0 mt-2 cursor-pointer';
        var checkboxInput = document.createElement('input');
        checkboxInput.type = 'checkbox';
        checkboxInput.className = 'notif-checkbox w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500';
        checkboxWrapper.appendChild(checkboxInput);

        // Create icon container
        var iconContainer = document.createElement('div');
        iconContainer.className = 'w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 ' + iconColor;
        var iconElement = document.createElement('i');
        iconElement.className = icon + ' text-lg';
        iconContainer.appendChild(iconElement);

        // Create body container
        var bodyContainer = document.createElement('div');
        bodyContainer.className = 'flex-1 min-w-0';

        // Create header
        var headerDiv = document.createElement('div');
        headerDiv.className = 'flex items-start justify-between gap-2';

        var titleP = document.createElement('p');
        titleP.className = 'font-bold text-[13px] text-slate-900 leading-tight';
        titleP.textContent = n.title || '';

        var actionDiv = document.createElement('div');
        actionDiv.className = 'flex items-center gap-1 flex-shrink-0';

        var keepBtn = n.is_kept
            ? '<button data-notif-id="' + n.id + '" class="notif-keep-btn w-6 h-6 rounded-md flex items-center justify-center text-amber-500 hover:bg-amber-50 transition-all" title="Lepas dari simpanan" aria-label="Lepas dari simpanan"><i class="ri-bookmark-fill text-sm"></i></button>'
            : '<button data-notif-id="' + n.id + '" class="notif-keep-btn w-6 h-6 rounded-md flex items-center justify-center text-slate-300 hover:bg-slate-50 hover:text-amber-500 transition-all" title="Simpan notifikasi" aria-label="Simpan notifikasi"><i class="ri-bookmark-line text-sm"></i></button>';

        var unreadDot = n.is_read ? '' : '<span class="notif-unread-dot w-2 h-2 rounded-full bg-teal-500 flex-shrink-0 mt-1.5" title="Belum dibaca"></span>';

        actionDiv.innerHTML = keepBtn + unreadDot;

        headerDiv.appendChild(titleP);
        headerDiv.appendChild(actionDiv);

        var messageP = document.createElement('p');
        messageP.className = 'text-[12px] text-slate-500 mt-1 leading-relaxed line-clamp-2';
        messageP.textContent = n.message || '';

        bodyContainer.appendChild(headerDiv);
        bodyContainer.appendChild(messageP);

        // Create meta + actions container
        var metaActionsDiv = document.createElement('div');
        metaActionsDiv.className = 'flex items-center justify-between gap-2 mt-2';

        var timeSpan = document.createElement('span');
        timeSpan.className = 'text-[10px] text-slate-400 font-semibold notif-time';
        timeSpan.textContent = n.created_human || '';

        var actionsDiv = document.createElement('div');
        actionsDiv.className = 'notif-actions flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity';

        var readBtn = n.is_read
            ? '<button type="button" data-notif-action="unread" data-notif-id="' + n.id + '" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-all" title="Tandai belum dibaca" aria-label="Tandai belum dibaca"><i class="ri-mail-unread-line text-[14px]"></i></button>'
            : '<button type="button" data-notif-action="read" data-notif-id="' + n.id + '" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-teal-50 hover:text-teal-600 transition-all" title="Tandai dibaca" aria-label="Tandai dibaca"><i class="ri-check-line text-[14px]"></i></button>';

        var archiveBtn = n.is_archived
            ? '<button type="button" data-notif-action="unarchive" data-notif-id="' + n.id + '" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all" title="Kembalikan dari arsip" aria-label="Kembalikan dari arsip"><i class="ri-inbox-unarchive-line text-[14px]"></i></button>'
            : '<button type="button" data-notif-action="archive" data-notif-id="' + n.id + '" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all" title="Arsipkan" aria-label="Arsipkan"><i class="ri-archive-line text-[14px]"></i></button>';

        var deleteBtn = '<button type="button" data-notif-action="delete" data-notif-id="' + n.id + '" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-600 transition-all" title="Hapus" aria-label="Hapus"><i class="ri-delete-bin-line text-[14px]"></i></button>';

        actionsDiv.innerHTML = readBtn + archiveBtn + deleteBtn;

        metaActionsDiv.appendChild(timeSpan);
        metaActionsDiv.appendChild(actionsDiv);

        bodyContainer.appendChild(metaActionsDiv);

        // Assemble everything
        wrapper.appendChild(checkboxWrapper);
        wrapper.appendChild(iconContainer);
        wrapper.appendChild(bodyContainer);

        return wrapper;
    }

// =========================================================================
    // Polling sinkronisasi - realtime untuk semua filter
    // =========================================================================
    function syncNotifications() {
        var list = document.getElementById('notif-list');
        var overlay = document.getElementById('notif-overlay');
        var isDrawerOpen = overlay && !overlay.classList.contains('hidden');

        // Always fetch latest unread count for the badge
        fetchJson('/notifications/poll?limit=15', { method: 'GET' })
            .then(function (data) {
                if (!data) return;
                updateBadge(data.unread_count || 0);
                updateHeaderStatus(data.unread_count || 0);

                // If drawer is open, refresh the current filter view for freshness
                if (isDrawerOpen && list) {
                    var filter = list.dataset.filter || 'all';
                    var q = list.dataset.q || '';
                    var category = list.dataset.category || '';
                    fetchFilteredNotifications(filter, q, category);
                }
            })
            .catch(function () { /* silent */ });
    }
    
    // =========================================================================
    // Refresh notification list based on current filter/search/category
    // =========================================================================
    function refreshNotificationList() {
        var list = document.getElementById('notif-list');
        if (!list) return;

        var filter = list.dataset.filter || 'all';
        var q = list.dataset.q || '';
        var category = list.dataset.category || '';
        fetchFilteredNotifications(filter, q, category);
    }
    
    // =========================================================================
    // Drawer open/close
    // =========================================================================
    function openNotificationDrawer() {
        var overlay = document.getElementById('notif-overlay');
        var panel = document.getElementById('notif-panel');
        if (!overlay || !panel) return;
        overlay.classList.remove('hidden');
        requestAnimationFrame(function () {
            panel.style.transform = 'translateX(0)';
        });
        document.body.style.overflow = 'hidden';

        // Segarkan notifikasi saat dibuka berdasarkan filter yang aktif
        refreshNotificationList();
    }

    function closeNotificationDrawer() {
        var overlay = document.getElementById('notif-overlay');
        var panel = document.getElementById('notif-panel');
        if (!overlay || !panel) return;
        panel.style.transform = 'translateX(100%)';
        setTimeout(function () {
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
        if (bulkMode) toggleNotificationBulk();
    }

    // =========================================================================
    // Tandai semua dibaca
    // =========================================================================
    function markAllNotificationsRead(e) {
        if (!e) return;
        var btn = e.currentTarget;
        var original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-0.5"></i> Memproses...';

        fetchJson('/notifications/mark-all-read', { method: 'POST' })
            .then(function (data) {
                if (!data || !data.success) {
                    btn.disabled = false;
                    btn.innerHTML = original;
                    return;
                }
                $$('.notif-item').forEach(function (el) {
                    el.classList.remove('bg-teal-50/40', 'hover:bg-teal-50/70');
                    el.classList.add('bg-white', 'hover:bg-slate-50');
                    el.dataset.read = '1';
                    var dot = el.querySelector('.notif-unread-dot');
                    if (dot) dot.remove();
                    // Ganti tombol unread -> read
                    var action = el.querySelector('[data-notif-action="read"], [data-notif-action="unread"]');
                    if (action && action.dataset.notifAction === 'read') {
                        action.outerHTML = '<button type="button" data-notif-action="unread" data-notif-id="' + (action.dataset.notifId || el.dataset.id) + '" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-all" title="Tandai belum dibaca" aria-label="Tandai belum dibaca"><i class="ri-mail-unread-line text-[14px]"></i></button>';
                    }
                });
                updateBadge(0);
                updateHeaderStatus(0);
            })
            .catch(function (err) {
                btn.disabled = false;
                btn.innerHTML = original;
                showToast((err && err.message) || 'Gagal menandai notifikasi.', 'danger');
            });
    }

    // =========================================================================
    // Toggle bookmark (disimpan/dilepas)
    // =========================================================================
    function toggleNotificationKeep(id, btn) {
        var item = btn.closest('.notif-item');
        var isKept = item && item.dataset.kept === '1';

        fetchJson('/notifications/' + id + '/keep', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({})
        }).then(function (data) {
            if (!data || !data.success) return;
            if (item) item.dataset.kept = data.is_kept ? '1' : '0';
            if (data.is_kept) {
                btn.innerHTML = '<i class="ri-bookmark-fill text-sm"></i>';
                btn.classList.remove('text-slate-300', 'hover:bg-slate-50', 'hover:text-amber-500');
                btn.classList.add('text-amber-500', 'hover:bg-amber-50');
                btn.title = 'Lepas dari simpanan';
                btn.setAttribute('aria-label', 'Lepas dari simpanan');
                showToast('Notifikasi disimpan.', 'success');
            } else {
                btn.innerHTML = '<i class="ri-bookmark-line text-sm"></i>';
                btn.classList.remove('text-amber-500', 'hover:bg-amber-50');
                btn.classList.add('text-slate-300', 'hover:bg-slate-50', 'hover:text-amber-500');
                btn.title = 'Simpan notifikasi';
                btn.setAttribute('aria-label', 'Simpan notifikasi');
                showToast('Notifikasi dilepas dari simpanan.', 'info');
            }
        }).catch(function (err) {
            showToast((err && err.message) || 'Gagal memperbarui bookmark.', 'danger');
        });
    }

    // =========================================================================
   
    // =========================================================================
    // Aksi per item (read, unread, archive, unarchive, delete)
    // =========================================================================
    function performItemAction(id, action, btn) {
        var url = '/notifications/' + id;
        var method = 'POST';
        switch (action) {
            case 'read': url += '/read'; break;
            case 'unread': url += '/unread'; break;
            case 'archive': url += '/archive'; break;
            case 'unarchive': url += '/unarchive'; break;
            case 'delete': method = 'DELETE'; break;
            default: return;
        }

        var originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line animate-spin text-[14px]"></i>';

        fetchJson(url, { method: method, headers: { 'Content-Type': 'application/json' }, body: '{}' })
            .then(function (data) {
                if (!data || !data.success) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    showToast('Gagal memperbarui notifikasi.', 'danger');
                    return;
                }

                if (action === 'delete') {
                    var item = btn.closest('.notif-item');
                    if (item) {
                        item.style.transition = 'opacity 200ms, transform 200ms';
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(20px)';
                        setTimeout(function () { item.remove(); }, 200);
                    }
                    showToast('Notifikasi dihapus.', 'success');
                    return;
                }

                var item = btn.closest('.notif-item');
                if (!item) return;

                if (action === 'read') {
                    item.dataset.read = '1';
                    item.classList.remove('bg-teal-50/40', 'hover:bg-teal-50/70');
                    item.classList.add('bg-white', 'hover:bg-slate-50');
                    var dot = item.querySelector('.notif-unread-dot');
                    if (dot) dot.remove();
                    btn.outerHTML = '<button type="button" data-notif-action="unread" data-notif-id="' + id + '" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-all" title="Tandai belum dibaca" aria-label="Tandai belum dibaca"><i class="ri-mail-unread-line text-[14px]"></i></button>';
                } else if (action === 'unread') {
                    item.dataset.read = '0';
                    item.classList.add('bg-teal-50/40', 'hover:bg-teal-50/70');
                    item.classList.remove('bg-white', 'hover:bg-slate-50');
                    if (!item.querySelector('.notif-unread-dot')) {
                        var dot = document.createElement('span');
                        dot.className = 'notif-unread-dot w-2 h-2 rounded-full bg-teal-500 flex-shrink-0 mt-1.5';
                        dot.title = 'Belum dibaca';
                        item.querySelector('.flex.items-center.gap-1.flex-shrink-0').appendChild(dot);
                    }
                    btn.outerHTML = '<button type="button" data-notif-action="read" data-notif-id="' + id + '" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-400 hover:bg-teal-50 hover:text-teal-600 transition-all" title="Tandai dibaca" aria-label="Tandai dibaca"><i class="ri-check-line text-[14px]"></i></button>';
                } else if (action === 'archive') {
                    item.dataset.archived = '1';
                    showToast('Notifikasi diarsipkan.', 'info');
                } else if (action === 'unarchive') {
                    item.dataset.archived = '0';
                    showToast('Notifikasi dikembalikan dari arsip.', 'success');
                }

                // Refresh badge count
                refreshUnreadCount();
            })
            .catch(function (err) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                showToast((err && err.message) || 'Gagal memperbarui notifikasi.', 'danger');
            });
    }

    function refreshUnreadCount() {
        fetchJson('/notifications/poll?limit=1', { method: 'GET' })
            .then(function (data) {
                if (!data) return;
                updateBadge(data.unread_count || 0);
                updateHeaderStatus(data.unread_count || 0);
            })
            .catch(function () { /* silent */ });
    }

    // =========================================================================
    // Bulk select
    // =========================================================================
    function toggleNotificationBulk() {
        bulkMode = !bulkMode;
        var bar = document.getElementById('notif-bulk-bar');
        document.body.classList.toggle('notif-bulk-mode', bulkMode);
        $$('.notif-checkbox-wrap').forEach(function (el) {
            if (bulkMode) el.classList.remove('hidden');
            else el.classList.add('hidden');
        });
        if (bar) {
            if (bulkMode) {
                bar.classList.remove('hidden');
                bar.classList.add('flex');
            } else {
                bar.classList.add('hidden');
                bar.classList.remove('flex');
                $$('.notif-checkbox').forEach(function (cb) { cb.checked = false; });
                updateBulkCount();
            }
        }
    }

    function updateBulkCount() {
        var checked = $$('.notif-checkbox:checked').length;
        var countEl = document.getElementById('notif-bulk-count');
        if (countEl) countEl.textContent = checked;
    }

    function bulkNotificationAction(action) {
        var ids = $$('.notif-checkbox:checked').map(function (cb) {
            var item = cb.closest('.notif-item');
            return item ? item.dataset.id : null;
        }).filter(Boolean).map(Number);

        if (ids.length === 0) {
            showToast('Pilih minimal satu notifikasi.', 'warning');
            return;
        }

        var confirmMsg = null;
        if (action === 'delete') confirmMsg = 'Hapus ' + ids.length + ' notifikasi secara permanen?';
        else if (action === 'archive') confirmMsg = 'Arsipkan ' + ids.length + ' notifikasi?';
        if (confirmMsg && !window.confirm(confirmMsg)) return;

        fetchJson('/notifications/bulk', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: action, ids: ids })
        }).then(function (data) {
            if (!data || !data.success) {
                showToast('Gagal menjalankan aksi.', 'danger');
                return;
            }

            if (action === 'delete') {
                $$('.notif-checkbox:checked').forEach(function (cb) {
                    var item = cb.closest('.notif-item');
                    if (item) item.remove();
                });
                showToast(data.updated + ' notifikasi dihapus.', 'success');
            } else if (action === 'archive') {
                $$('.notif-checkbox:checked').forEach(function (cb) {
                    var item = cb.closest('.notif-item');
                    if (item) {
                        item.dataset.archived = '1';
                        var cbWrap = item.querySelector('.notif-checkbox-wrap input');
                        if (cbWrap) cbWrap.checked = false;
                    }
                });
                showToast(data.updated + ' notifikasi diarsipkan.', 'success');
            } else if (action === 'mark_read') {
                $$('.notif-checkbox:checked').forEach(function (cb) {
                    var item = cb.closest('.notif-item');
                    if (item) {
                        item.dataset.read = '1';
                        item.classList.remove('bg-teal-50/40', 'hover:bg-teal-50/70');
                        item.classList.add('bg-white', 'hover:bg-slate-50');
                        var dot = item.querySelector('.notif-unread-dot');
                        if (dot) dot.remove();
                        var cbWrap = item.querySelector('.notif-checkbox-wrap input');
                        if (cbWrap) cbWrap.checked = false;
                    }
                });
                showToast(data.updated + ' notifikasi ditandai dibaca.', 'success');
            } else if (action === 'keep') {
                $$('.notif-checkbox:checked').forEach(function (cb) {
                    var item = cb.closest('.notif-item');
                    if (item) {
                        item.dataset.kept = '1';
                        var btn = item.querySelector('.notif-keep-btn');
                        if (btn) {
                            btn.innerHTML = '<i class="ri-bookmark-fill text-sm"></i>';
                            btn.classList.remove('text-slate-300', 'hover:bg-slate-50', 'hover:text-amber-500');
                            btn.classList.add('text-amber-500', 'hover:bg-amber-50');
                        }
                        var cbWrap = item.querySelector('.notif-checkbox-wrap input');
                        if (cbWrap) cbWrap.checked = false;
                    }
                });
                showToast(data.updated + ' notifikasi disimpan.', 'success');
            }
            updateBulkCount();
            refreshUnreadCount();
        }).catch(function (err) {
            showToast((err && err.message) || 'Gagal menjalankan aksi.', 'danger');
        });
    }

    // =========================================================================
    // Bersihkan semua (soft archive)
    // =========================================================================
    function clearAllNotifications() {
        if (!window.confirm('Arsipkan semua notifikasi aktif? Notifikasi tetap ada di tab Arsip dan bisa dikembalikan.')) return;

        fetchJson('/notifications/clear-all', { method: 'POST' })
            .then(function (data) {
                if (!data || !data.success) return;
                showToast(data.updated + ' notifikasi diarsipkan.', 'success');
                // Reload halaman setelah 800ms agar sinkron dengan server
                setTimeout(function () { window.location.reload(); }, 800);
            })
            .catch(function (err) {
                showToast((err && err.message) || 'Gagal membersihkan notifikasi.', 'danger');
            });
    }

    // =========================================================================
    // Tab filter (All / Unread / Kept / Archived) - stay in drawer
    // =========================================================================
    // Tab active color map: each filter has its own active color
    var TAB_ACTIVE_CLASSES = {
        'all':      'bg-slate-900 text-white shadow-sm',
        'unread':   'bg-teal-600 text-white shadow-sm',
        'kept':     'bg-amber-500 text-white shadow-sm',
        'archived': 'bg-slate-700 text-white shadow-sm'
    };
    var TAB_INACTIVE_CLASSES = 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-800';

    function applyTabStyling(filter) {
        var tabs = document.querySelectorAll('#notif-tabs .notif-tab');
        tabs.forEach(function (tab) {
            var isActive = tab.dataset.filter === filter;
            // Remove all possible active classes first
            Object.values(TAB_ACTIVE_CLASSES).forEach(function (cls) {
                cls.split(' ').forEach(function (c) { tab.classList.remove(c); });
            });
            TAB_INACTIVE_CLASSES.split(' ').forEach(function (c) { tab.classList.remove(c); });

            if (isActive) {
                (TAB_ACTIVE_CLASSES[filter] || TAB_ACTIVE_CLASSES['all']).split(' ').forEach(function (c) {
                    tab.classList.add(c);
                });
            } else {
                TAB_INACTIVE_CLASSES.split(' ').forEach(function (c) { tab.classList.add(c); });
            }
        });
    }

    function renderNotificationList(data, list) {
        if (!data || !data.data) return;

        if (data.unread_count !== undefined) {
            updateBadge(data.unread_count);
            updateHeaderStatus(data.unread_count);
        }

        // Update all tab counts (all, unread, kept, archived)
        updateTabCounts(data);

        list.innerHTML = '';
        var items = data.data || [];
        if (items.length === 0) {
            // Show empty state based on current filter
            var filter = list.dataset.filter || 'all';
            var q = list.dataset.q || '';
            var emptyMsg = '';
            if (filter === 'unread') {
                emptyMsg = 'Semua notifikasi sudah ditandai dibaca.';
            } else if (filter === 'kept') {
                emptyMsg = 'Belum ada notifikasi yang disimpan.';
            } else if (filter === 'archived') {
                emptyMsg = 'Belum ada notifikasi di arsip.';
            } else if (q) {
                emptyMsg = 'Tidak ada hasil untuk "' + escapeHtml(q) + '".';
            } else {
                emptyMsg = 'Update terbaru akan muncul di sini.';
            }
            list.innerHTML =
                '<div class="flex flex-col items-center justify-center py-20 text-center px-8">' +
                    '<div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center mb-5">' +
                        '<i class="ri-notification-off-line text-4xl text-slate-300"></i>' +
                    '</div>' +
                    '<h4 class="font-display font-bold text-slate-700 text-[1rem] mb-1">Tidak Ada Notifikasi</h4>' +
                    '<p class="text-slate-400 text-[13px]">' + emptyMsg + '</p>' +
                '</div>';
            return;
        }

        items.forEach(function (n) {
            if (!seenIds.has(String(n.id))) {
                var node = buildNotificationNode(n);
                list.appendChild(node);
                seenIds.add(String(n.id));
            }
        });
    }

    function fetchFilteredNotifications(filter, q, category) {
        var list = document.getElementById('notif-list');
        if (!list) return;

        list.innerHTML = '<div class="px-5 py-4 text-center text-slate-400"><i class="ri-loader-4-line animate-spin mr-1"></i> Memuat...</div>';
        seenIds.clear();

        var url = '/notifications/list?filter=' + encodeURIComponent(filter) +
                  (q ? '&q=' + encodeURIComponent(q) : '') +
                  (category ? '&category=' + encodeURIComponent(category) : '');

        fetchJson(url, { method: 'GET' })
            .then(function (data) { renderNotificationList(data, list); })
            .catch(function () {
                list.innerHTML = '<div class="px-5 py-4 text-center text-slate-400">Gagal memuat notifikasi.</div>';
            });
    }

    function switchNotificationFilter(filter) {
        var list = document.getElementById('notif-list');
        if (!list) return;

        // Update active tab styling with correct per-tab colors
        applyTabStyling(filter);

        // Update filter dataset
        list.dataset.filter = filter;

        // Fetch filtered notifications
        var q = list.dataset.q || '';
        var category = list.dataset.category || '';
        fetchFilteredNotifications(filter, q, category);
    }

    function submitNotificationSearch(e) {
        if (e) e.preventDefault();
        var input = document.getElementById('notif-search-input');
        if (!input) return false;
        var q = input.value;
        var list = document.getElementById('notif-list');
        if (!list) return false;

        // Update search dataset
        list.dataset.q = q;

        // Fetch filtered notifications using helper
        var filter = list.dataset.filter || 'all';
        var category = list.dataset.category || '';
        fetchFilteredNotifications(filter, q, category);

        return false;
    }

    // =========================================================================
    // Click delegation
    // =========================================================================
    function onDrawerClick(e) {
        var target = e.target;
        if (!target) return;

        // Bookmark toggle
        var keepBtn = target.closest && target.closest('.notif-keep-btn');
        if (keepBtn) {
            e.preventDefault();
            e.stopPropagation();
            var keepId = keepBtn.getAttribute('data-notif-id');
            if (keepId) toggleNotificationKeep(keepId, keepBtn);
            return;
        }

        // Per-item action (read/unread/archive/unarchive/delete)
        var actionBtn = target.closest && target.closest('[data-notif-action]');
        if (actionBtn) {
            e.preventDefault();
            e.stopPropagation();
            var action = actionBtn.dataset.notifAction;
            var actionId = actionBtn.dataset.notifId;
            if (action && actionId) performItemAction(actionId, action, actionBtn);
            return;
        }

        // Checkbox bulk
        var checkbox = target.closest && target.closest('.notif-checkbox');
        if (checkbox) {
            updateBulkCount();
            return;
        }

        // Klik utama -> buka link + tandai dibaca
        var item = target.closest && target.closest('.notif-item');
        if (item && !bulkMode) {
            var link = item.dataset.link;
            if (link && link !== '#') {
                // Tandai dibaca dulu (best-effort, fire-and-forget)
                if (item.dataset.read === '0') {
                    fetchJson('/notifications/' + item.dataset.id + '/read', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: '{}'
                    }).catch(function () { /* silent */ });
                }
                window.location.href = link;
            }
        }
    }

    // =========================================================================
    // Boot
    // =========================================================================
    function init() {
        // Seed seenIds dari DOM existing
        $$('.notif-item').forEach(function (el) { if (el.dataset.id) seenIds.add(el.dataset.id); });

        // Delegasi click di body (cukup sekali)
        document.addEventListener('click', onDrawerClick);

        // Tutup drawer dengan ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                var overlay = document.getElementById('notif-overlay');
                if (overlay && !overlay.classList.contains('hidden')) closeNotificationDrawer();
            }
        });

        // Polling periodik
        setInterval(syncNotifications, POLL_INTERVAL);
        // Kick off awal
        syncNotifications();

        // Real-time listener via Echo/Reverb
        setupEchoListener();
    }

    // =========================================================================
    // Real-time Echo/Reverb listener
    // =========================================================================
    function setupEchoListener() {
        // Wait for Echo to be available (loaded with defer)
        function tryListen() {
            if (typeof window.Echo === 'undefined' || typeof window.Echo.private !== 'function') {
                setTimeout(tryListen, 1000);
                return;
            }

            // Determine current user role and ID from the page
            var metaRole = document.querySelector('meta[name="notif-role"]');
            var metaUserId = document.querySelector('meta[name="notif-user-id"]');
            if (!metaRole || !metaUserId) return;

            var role = metaRole.content;
            var userId = metaUserId.content;
            var channelName = 'notifications.' + role + '.' + userId;

            window.Echo.private(channelName)
                .listen('.notification.created', function (e) {
                    var n = e.notification;
                    if (!n) return;

                    // Update badge
                    refreshUnreadCount();

                    // If drawer is open, add the new notification to the list
                    var list = document.getElementById('notif-list');
                    var overlay = document.getElementById('notif-overlay');
                    if (list && overlay && !overlay.classList.contains('hidden')) {
                        if (!seenIds.has(String(n.id))) {
                            var node = buildNotificationNode(n);
                            if (list.firstChild) {
                                list.insertBefore(node, list.firstChild);
                            } else {
                                list.appendChild(node);
                            }
                            seenIds.add(String(n.id));
                        }
                    }

                    // Show toast notification
                    showToast(n.title + ': ' + n.message, n.type || 'info');
                });
        }

        tryListen();
    }

    // Expose ke window agar onclick="" di Blade bisa memanggil
    window.openNotificationDrawer = openNotificationDrawer;
    window.closeNotificationDrawer = closeNotificationDrawer;
    window.markAllNotificationsRead = markAllNotificationsRead;
    window.toggleNotificationKeep = toggleNotificationKeep;
    window.toggleNotificationBulk = toggleNotificationBulk;
    window.bulkNotificationAction = bulkNotificationAction;
    window.clearAllNotifications = clearAllNotifications;
    window.switchNotificationFilter = switchNotificationFilter;
    window.submitNotificationSearch = submitNotificationSearch;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();