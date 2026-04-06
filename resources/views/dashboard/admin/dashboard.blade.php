@php
    use App\Models\Client;
    use App\Models\Freelancer;
    use App\Models\SkomdaStudent;

    $totalUsers = Client::count() + Freelancer::count() + SkomdaStudent::count();
    $totalClients = Client::count();
    $totalFreelancers = Freelancer::count();
    $totalSkomda = SkomdaStudent::count();
@endphp

@extends('layouts.dashboard')
@section('title', 'Admin Dashboard | Digitalance')

@section('content')
    {{-- Welcome --}}
    <section class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-8 animate-fadeUp">
        <div class="min-w-0">
            <h1 class="font-display text-[1.85rem] sm:text-[2.1rem] font-extrabold text-slate-900 leading-tight">
                Hi, {{ Auth::user()->name }}!
                <span class="inline-block">👋</span>
            </h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">
                Here's what's happening with your work today.
            </p>
        </div>
    </section>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 sm:gap-6 mb-10">

    {{-- Total Users --}}
    <div class="bg-white px-6 py-6 rounded-3xl border border-slate-200 flex items-center gap-4">
        <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 text-xl">
            <i class="ri-group-line"></i>
        </div>
        <div>
            <span class="text-slate-500 text-[11px] font-bold uppercase">Total Users</span>
            <div class="text-2xl font-extrabold text-slate-900">
                {{ number_format($totalUsers ?? 0) }}
            </div>
        </div>
    </div>

    {{-- Clients --}}
    <div class="bg-white px-6 py-6 rounded-3xl border border-slate-200 flex items-center gap-4">
        <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 text-xl">
            <i class="ri-briefcase-4-line"></i>
        </div>
        <div>
            <span class="text-slate-500 text-[11px] font-bold uppercase">Clients</span>
            <div class="text-2xl font-extrabold text-slate-900">
                {{ number_format($totalClients ?? 0) }}
            </div>
        </div>
    </div>

    {{-- Freelancers --}}
    <div class="bg-white px-6 py-6 rounded-3xl border border-slate-200 flex items-center gap-4">
        <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-orange-50 text-orange-600 text-xl">
            <i class="ri-vip-crown-line"></i>
        </div>
        <div>
            <span class="text-slate-500 text-[11px] font-bold uppercase">Freelancers</span>
            <div class="text-2xl font-extrabold text-slate-900">
                {{ number_format($totalFreelancers ?? 0) }}
            </div>
        </div>
    </div>

    {{-- Skomda Students --}}
    <div class="bg-white px-6 py-6 rounded-3xl border border-slate-200 flex items-center gap-4">
        <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-teal-50 text-teal-600 text-xl">
            <i class="ri-graduation-cap-line"></i>
        </div>
        <div>
            <span class="text-slate-500 text-[11px] font-bold uppercase">Skomda Students</span>
            <div class="text-2xl font-extrabold text-slate-900">
                {{ number_format($totalSkomda ?? 0) }}
            </div>
        </div>
    </div>

</section>

    {{-- Pending Verifications + System Alerts --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-10">
        {{-- Pending Verifications --}}
        <section class="xl:col-span-2">
            <div class="flex justify-between items-center mb-5">
                <h2 class="font-display text-[1.3rem] font-bold">Pending Verifications</h2>
            </div>

            <div id="verification-container" data-verify-url="{{ url('/admin/verify-freelancer/__ID__') }}"
                data-reject-url="{{ url('/admin/reject-freelancer/__ID__') }}" class="flex flex-col gap-3">

                @forelse(($pendingVerifications ?? []) as $v)
                    @php
                        $userName = data_get($v, 'user.name', 'User');
                        $avatarName = urlencode($userName ?: 'F');
                    @endphp

                    <div class="approval-card bg-white border border-slate-200 rounded-2xl p-[18px_20px] animate-fadeUp"
                        data-id="{{ $v->id }}">
                        <div class="flex items-start gap-3.5 mb-4">
                            <img class="w-11 h-11 rounded-xl object-cover border" alt="Avatar {{ $userName }}"
                                src="https://ui-avatars.com/api/?name={{ $avatarName }}&background=0f766e&color=fff" />

                            <div class="flex-1 min-w-0">
                                <span class="font-bold text-[14px] text-slate-900 user-name block truncate">
                                    {{ $userName }}
                                </span>
                                <p class="text-[12px] text-slate-500">
                                    {{ $v->category ?? 'Freelancer' }}
                                </p>
                            </div>
                        </div>

                        <div class="approval-actions flex flex-col sm:flex-row gap-2.5 pt-3.5 border-t border-slate-200">
                            <button type="button" data-action="approve"
                                class="flex-1 py-2 rounded-lg bg-emerald-100 text-emerald-900 text-xs font-bold transition-all hover:brightness-[0.98] disabled:opacity-60 disabled:cursor-not-allowed">
                                Approve
                            </button>
                            <button type="button" data-action="reject"
                                class="flex-1 py-2 rounded-lg bg-red-100 text-red-900 text-xs font-bold transition-all hover:brightness-[0.98] disabled:opacity-60 disabled:cursor-not-allowed">
                                Reject
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="text-center py-12 px-5 bg-white border-2 border-dashed border-slate-200 rounded-3xl animate-fadeUp">
                        <div class="flex items-center justify-center text-[44px] text-slate-300 mb-3">
                            <i class="ri-inbox-archive-line"></i>
                        </div>
                        <h3 class="font-display text-[1.15rem] text-slate-900 mb-1.5 font-bold">No Pending Requests</h3>
                        <p class="text-slate-400 text-[13.5px]">Semua pengajuan sudah bersih!</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- System Alerts --}}
        <section>
            <div class="flex justify-between items-center mb-5">
                <h2 class="font-display text-[1.3rem] font-bold">System Alerts</h2>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 flex flex-col gap-4">
                @php $hasAnyAlert = false; @endphp

                @if (($openDisputes ?? 0) > 0)
                    @php $hasAnyAlert = true; @endphp
                    <div class="flex gap-3 items-start pb-4 border-b border-slate-200">
                        <i class="ri-error-warning-line text-orange-500 text-[20px] flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h4 class="text-[14px] text-slate-900 mb-1 font-semibold">Open Disputes</h4>
                            <p class="text-[12px] text-slate-500 leading-relaxed">
                                Ada <strong>{{ $openDisputes }}</strong> dispute yang belum diselesaikan.
                            </p>
                        </div>
                    </div>
                @endif

                @if (isset($pendingVerifications) && count($pendingVerifications) >= 5)
                    @php $hasAnyAlert = true; @endphp
                    <div class="flex gap-3 items-start pb-4 border-b border-slate-200">
                        <i class="ri-user-received-line text-orange-400 text-[20px] flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h4 class="text-[14px] text-slate-900 mb-1 font-semibold">Antrian Verifikasi Penuh</h4>
                            <p class="text-[12px] text-slate-500 leading-relaxed">
                                Ada <strong>{{ count($pendingVerifications) }}</strong> pengajuan menunggu persetujuan.
                            </p>
                        </div>
                    </div>
                @endif

                @if (!$hasAnyAlert)
                    <div class="flex gap-3 items-start">
                        <i class="ri-shield-check-line text-[#10B981] text-[20px] flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h4 class="text-[14px] text-slate-900 mb-1 font-semibold">Semua Sistem Normal</h4>
                            <p class="text-[12px] text-slate-500 leading-relaxed">Tidak ada masalah terdeteksi.</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>

    {{-- Admin content area placeholder --}}
    <section id="admin-content" class="mt-10 animate-fadeUp-delay-2"></section>

    {{-- Flags: supaya JS tidak perlu Blade expression --}}
    <div id="page-flags"
        data-has-unread="{{ ((isset($pendingVerifications) && count($pendingVerifications) > 0) || (isset($openDisputes) && $openDisputes > 0)) ? 1 : 0 }}">
    </div>
@endsection

@section('scripts')
    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const icon = type === 'success' ? 'ri-check-double-line' : 'ri-close-circle-line';
            const toast = document.createElement('div');
            toast.className = `toast toast-${type === 'success' ? 'success' : 'danger'}`;
            toast.innerHTML = `
                        <i class="toast-icon ${icon}"></i>
                        <span>${message}</span>
                        <button class="toast-close" type="button" aria-label="Close toast">
                            <i class="ri-close-line"></i>
                        </button>
                    `;
            toast.querySelector('.toast-close')?.addEventListener('click', () => dismissToast(toast));

            container.appendChild(toast);
            setTimeout(() => dismissToast(toast), 3500);
        }

        function dismissToast(toast) {
            if (!toast || toast.classList.contains('toast-hide')) return;
            toast.classList.add('toast-hide');
            setTimeout(() => toast.remove(), 300);
        }

        function setCardLoading(card, isLoading) {
            if (!card) return;
            const buttons = card.querySelectorAll('button[data-action]');
            buttons.forEach(btn => (btn.disabled = isLoading));
        }

        async function postAction(url) {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            let data = null;
            try { data = await res.json(); } catch (e) { }

            if (!res.ok) throw new Error(data?.message || 'Request gagal. Coba lagi.');
            return data;
        }

        async function handleApprove(id) {
            const card = document.querySelector(`.approval-card[data-id="${id}"]`);
            const container = document.getElementById('verification-container');
            if (!card || !container) return;

            const name = card.querySelector('.user-name')?.textContent?.trim() ?? 'Freelancer';
            const verifyUrl = (container.dataset.verifyUrl || '').replace('__ID__', id);
            if (!verifyUrl) return;

            setCardLoading(card, true);

            try {
                await postAction(verifyUrl);

                card.classList.add('card-approved');
                showToast(`${name} berhasil diverifikasi!`, 'success');

                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => card.remove(), 300);
                }, 800);
            } catch (error) {
                showToast(error?.message || "Gagal memverifikasi. Coba lagi.", "danger");
                setCardLoading(card, false);
            }
        }

        async function handleReject(id) {
            const card = document.querySelector(`.approval-card[data-id="${id}"]`);
            const container = document.getElementById('verification-container');
            if (!card || !container) return;

            if (!confirm('Yakin ingin menolak verifikasi ini?')) return;

            const rejectUrl = (container.dataset.rejectUrl || '').replace('__ID__', id);
            if (!rejectUrl) return;

            setCardLoading(card, true);

            try {
                await postAction(rejectUrl);

                card.classList.add('card-rejected');
                showToast("Verifikasi ditolak.", "danger");

                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(-30px)';
                    setTimeout(() => card.remove(), 400);
                }, 800);
            } catch (error) {
                showToast(error?.message || "Gagal menolak verifikasi. Coba lagi.", "danger");
                setCardLoading(card, false);
            }
        }

        function initNotifDrawer() {
            const btn = document.getElementById('notif-btn');
            const drawer = document.getElementById('notif-drawer');
            const panel = document.getElementById('notif-panel');
            const backdrop = document.getElementById('notif-backdrop');
            const closeBtn = document.getElementById('notif-close');

            if (!btn || !drawer || !panel || !backdrop || !closeBtn) return;

            const open = () => {
                drawer.classList.remove('hidden');
                drawer.setAttribute('aria-hidden', 'false');

                requestAnimationFrame(() => {
                    backdrop.classList.remove('opacity-0');
                    panel.classList.remove('translate-x-full');
                });

                btn.classList.remove('has-unread');
                document.body.style.overflow = 'hidden';
            };

            const close = () => {
                backdrop.classList.add('opacity-0');
                panel.classList.add('translate-x-full');

                drawer.setAttribute('aria-hidden', 'true');

                setTimeout(() => {
                    drawer.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 200);
            };

            btn.addEventListener('click', open);
            closeBtn.addEventListener('click', close);
            backdrop.aFddEventListener('click', close);

            document.addEventListener('keydown', (e) => {
                if (drawer.classList.contains('hidden')) return;
                if (e.key === 'Escape') close();
            });
        }

        function initAdminDashboard() {
            // unread flag
            const flags = document.getElementById('page-flags');
            const hasUnreadMessages = flags ? flags.dataset.hasUnread === '1' : false;

            // notif dot
            const notifBtn = document.getElementById('notif-btn');
            if (notifBtn) {
                notifBtn.classList.toggle('has-unread', hasUnreadMessages);
                notifBtn.addEventListener('click', () => notifBtn.classList.remove('has-unread'));
            }

            // approve/reject delegation
            const verificationContainer = document.getElementById('verification-container');
            if (verificationContainer) {
                verificationContainer.addEventListener('click', (e) => {
                    const btn = e.target.closest('button[data-action]');
                    if (!btn) return;

                    const card = btn.closest('.approval-card');
                    const id = card?.getAttribute('data-id');
                    if (!id) return;

                    const action = btn.getAttribute('data-action');
                    if (action === 'approve') handleApprove(id);
                    if (action === 'reject') handleReject(id);
                });
            }

            initNotifDrawer();
        }

        document.addEventListener('DOMContentLoaded', initAdminDashboard);
    </script>
@endsection