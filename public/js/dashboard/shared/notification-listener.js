(function () {
    'use strict';

    var POLL_INTERVAL = 15000; // 15s
    var seenIds = new Set(Array.from(document.querySelectorAll('.notif-item')).map(function (el) { return el.dataset.id; }));

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

    function updateHeaderText(unread) {
        var headerText = document.querySelector('#notif-panel h3 + p');
        if (!headerText) return;
        if (unread > 0) {
            headerText.classList.remove('text-slate-400');
            headerText.classList.add('text-teal-600');
            headerText.textContent = unread + ' belum dibaca';
        } else {
            headerText.classList.remove('text-teal-600');
            headerText.classList.add('text-slate-400');
            headerText.textContent = 'Semua sudah dibaca';
        }
    }

    function buildNotificationNode(n) {
        var wrapper = document.createElement('div');
        wrapper.className = 'notif-item px-5 py-4 border-b border-slate-50 transition-all duration-200 cursor-pointer ' + (n.is_read ? 'bg-white hover:bg-slate-50' : 'bg-teal-50/40 hover:bg-teal-50/70');
        if (n.link) wrapper.setAttribute('onclick', "window.location.href='" + n.link + "'");
        wrapper.dataset.id = n.id;

        var inner = '';
        inner += '<div class="flex items-start gap-3">';
        inner += '<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-100 text-blue-600">';
        inner += '<i class="ri-information-fill text-lg"></i>';
        inner += '</div>';
        inner += '<div class="flex-1 min-w-0">';
        inner += '<div class="flex items-start justify-between gap-2">';
        inner += '<p class="font-bold text-[13px] text-slate-900 leading-tight">' + (n.title || '') + '</p>';
        inner += '<div class="flex items-center gap-1 flex-shrink-0">';
        if (n.is_kept) {
            inner += '<button onclick="event.stopPropagation(); toggleNotificationKeep(' + n.id + ', this)" class="w-6 h-6 rounded-md flex items-center justify-center text-amber-500 hover:bg-amber-50 transition-all" title="Lepas notifikasi"><i class="ri-bookmark-fill text-sm"></i></button>';
        } else {
            inner += '<button onclick="event.stopPropagation(); toggleNotificationKeep(' + n.id + ', this)" class="w-6 h-6 rounded-md flex items-center justify-center text-slate-300 hover:bg-slate-50 hover:text-amber-500 transition-all" title="Simpan notifikasi"><i class="ri-bookmark-line text-sm"></i></button>';
        }
        if (!n.is_read) inner += '<span class="w-2 h-2 rounded-full bg-teal-500 flex-shrink-0 mt-1.5"></span>';
        inner += '</div></div>';
        inner += '<p class="text-[12px] text-slate-500 mt-1 leading-relaxed line-clamp-2">' + (n.message || '') + '</p>';
        inner += '<span class="text-[10px] text-slate-400 font-semibold mt-2 block">' + (n.created_at || '') + '</span>';
        inner += '</div></div>';

        wrapper.innerHTML = inner;
        return wrapper;
    }

    function syncNotifications() {
        fetch('/notifications/poll', { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data) return;
                updateBadge(data.unread_count || 0);
                updateHeaderText(data.unread_count || 0);

                var list = document.getElementById('notif-list');
                if (!list) return;

                (data.notifications || []).forEach(function (n) {
                    if (!seenIds.has(String(n.id))) {
                        // prepend
                        var node = buildNotificationNode(n);
                        list.insertBefore(node, list.firstChild);
                        seenIds.add(String(n.id));
                    }
                });
            })
            .catch(function () {
                // silent
            });
    }

    // initial sync
    syncNotifications();
    setInterval(syncNotifications, POLL_INTERVAL);
})();
