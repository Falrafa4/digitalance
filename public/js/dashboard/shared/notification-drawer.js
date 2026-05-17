/**
 * Notification Drawer logic — extracted from notification-drawer.blade.php
 * Handles slide-in drawer open/close, mark-all-read, keep/unkeep, and ESC key.
 */

function openNotificationDrawer() {
    var overlay = document.getElementById('notif-overlay');
    var panel = document.getElementById('notif-panel');
    if (!overlay || !panel) return;

    overlay.classList.remove('hidden');
    requestAnimationFrame(function () {
        panel.style.transform = 'translateX(0)';
    });
    document.body.style.overflow = 'hidden';
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
}

function markAllNotificationsRead(e) {
    if (!e) return;
    var btn = e.currentTarget;
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-0.5"></i> Memproses...';

    var token = document.querySelector('meta[name="csrf-token"]');
    token = token ? token.content : (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '');

    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
        }
    }).then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                document.querySelectorAll('.notif-item').forEach(function (el) {
                    el.classList.remove('bg-teal-50/40', 'hover:bg-teal-50/70');
                    el.classList.add('bg-white', 'hover:bg-slate-50');
                    var dot = el.querySelector('span.bg-teal-500');
                    if (dot) dot.remove();
                });
                var bellBadge = document.querySelector('#notif-btn .has-unread');
                if (bellBadge) bellBadge.remove();
                var headerText = document.querySelector('#notif-panel h3 + p');
                if (headerText) {
                    headerText.classList.remove('text-teal-600');
                    headerText.classList.add('text-slate-400');
                    headerText.textContent = 'Semua sudah dibaca';
                }
                var btnParent = btn.closest('div');
                if (btnParent) btnParent.remove();
            }
        })
        .catch(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-check-double-line mr-0.5"></i> Tandai Baca';
        });
}

function toggleNotificationKeep(id, btn) {
    var token = document.querySelector('meta[name="csrf-token"]');
    token = token ? token.content : (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '');

    var isKept = btn.querySelector('.ri-bookmark-fill') !== null;

    fetch('/notifications/' + id + '/keep', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ is_kept: !isKept })
    }).then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                if (isKept) {
                    btn.innerHTML = '<i class="ri-bookmark-line text-sm"></i>';
                    btn.classList.remove('text-amber-500');
                    btn.classList.add('text-slate-300');
                    btn.title = 'Simpan notifikasi';
                } else {
                    btn.innerHTML = '<i class="ri-bookmark-fill text-sm"></i>';
                    btn.classList.remove('text-slate-300');
                    btn.classList.add('text-amber-500');
                    btn.title = 'Lepas notifikasi';
                }
                window.showToast?.(isKept ? 'Notifikasi dilepas.' : 'Notifikasi disimpan.', 'success');
            }
        })
        .catch(function () {
            window.showToast?.('Gagal memperbarui notifikasi.', 'danger');
        });
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeNotificationDrawer();
});
