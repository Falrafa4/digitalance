@extends('layouts.dashboard')
@section('title', 'Admin Dashboard | Digitalance')

@section('content')
    <!-- Welcome -->
    <section class="flex justify-between items-end mb-8 animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Hi, {{ Auth::user()->name }}!
                👋</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Here's what's happening with your work today.</p>
        </div>
    </section>

    <!-- Stats -->
    <section class="grid grid-cols-4 gap-6 mb-10">

        {{-- Total Users --}}
        <div
            class="bg-white px-8 py-7 rounded-3xl border border-slate-200 transition-all duration-300 hover:border-[#10B981] animate-fadeUp-delay-1">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Total
                Users</span>
            @isset($totalUsers)
                <span
                    class="font-display text-[2rem] font-extrabold text-slate-900">{{ number_format($totalUsers, 0, ',', '.') }}</span>
            @else
                <span class="font-display text-[1.2rem] font-bold text-slate-300">—</span>
            @endisset
        </div>

        {{-- Active Projects --}}
        <div class="bg-white px-8 py-7 rounded-3xl border border-slate-200 transition-all duration-300 hover:border-[#10B981] animate-fadeUp-delay-1"
            style="animation-delay:0.1s">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Active
                Projects</span>
            @isset($activeProjects)
                <span
                    class="font-display text-[2rem] font-extrabold text-slate-900">{{ number_format($activeProjects, 0, ',', '.') }}</span>
            @else
                <span class="font-display text-[1.2rem] font-bold text-slate-300">—</span>
            @endisset
        </div>

        {{-- Platform Revenue --}}
        <div class="bg-white px-8 py-7 rounded-3xl border border-slate-200 transition-all duration-300 hover:border-[#10B981] animate-fadeUp-delay-1"
            style="animation-delay:0.2s">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Platform
                Revenue</span>
            @isset($totalRevenue)
                <div class="flex items-baseline gap-1">
                    <span class="text-[1.1rem] font-bold text-[#0f766e]">Rp</span>
                    <span
                        class="font-display text-[2rem] font-extrabold text-slate-900">{{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
            @else
                <span class="font-display text-[1.2rem] font-bold text-slate-300">—</span>
            @endisset
        </div>

        {{-- Open Disputes --}}
        <div class="bg-white px-8 py-7 rounded-3xl border border-slate-200 transition-all duration-300 hover:border-[#10B981] animate-fadeUp-delay-1"
            style="animation-delay:0.3s">
            <span class="block text-slate-500 text-[11px] font-bold uppercase tracking-[0.6px] mb-2">Open
                Disputes</span>
            @isset($openDisputes)
                <span
                    class="font-display text-[2rem] font-extrabold text-red-500">{{ number_format($openDisputes, 0, ',', '.') }}</span>
            @else
                <span class="font-display text-[1.2rem] font-bold text-slate-300">—</span>
            @endisset
        </div>

    </section>

    <!-- Pending Verifications + System Alerts -->
    <div class="grid gap-6 mb-10" style="grid-template-columns: 2fr 1fr;">

        <!-- Pending Verifications -->
        <section>
            <div class="flex justify-between items-center mb-5">
                <h2 class="font-display text-[1.3rem] font-bold">Pending Verifications</h2>
            </div>

            <div id="verification-container" class="flex flex-col gap-3">
                @forelse($pendingVerifications as $v)
                    <div class="approval-card bg-white border border-slate-200 rounded-2xl p-[18px_20px] animate-fadeUp"
                        data-id="{{ $v->id }}">
                        <div class="flex items-start gap-3.5 mb-4">
                            <img class="w-11 h-11 rounded-xl object-cover border"
                                src="https://ui-avatars.com/api/?name={{ urlencode($v->user->name ?? 'F') }}&background=0f766e&color=fff" />
                            <div class="flex-1">
                                <span
                                    class="font-bold text-[14px] text-slate-900 user-name">{{ $v->user->name ?? 'User' }}</span>
                                <p class="text-[12px] text-slate-500">{{ $v->category ?? 'Freelancer' }}</p>
                            </div>
                        </div>

                        <div class="approval-actions flex gap-2.5 pt-3.5 border-t border-slate-200">
                            <button onclick="handleApprove('{{ $v->id }}')"
                                class="flex-1 py-2 rounded-lg bg-emerald-100 text-emerald-900 text-xs font-bold">
                                Approve
                            </button>
                            <button onclick="handleReject('{{ $v->id }}')"
                                class="flex-1 py-2 rounded-lg bg-red-100 text-red-900 text-xs font-bold">
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
                        <h3 class="font-display text-[1.15rem] text-slate-900 mb-1.5 font-bold">No Pending
                            Requests</h3>
                        <p class="text-slate-400 text-[13.5px]">Semua pengajuan sudah bersih!</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- System Alerts -->
        <section>
            <div class="flex justify-between items-center mb-5">
                <h2 class="font-display text-[1.3rem] font-bold">System Alerts</h2>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 flex flex-col gap-4">

                @php $hasAnyAlert = false; @endphp

                @if (isset($openDisputes) && $openDisputes > 0)
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
                            <h4 class="text-[14px] text-slate-900 mb-1 font-semibold">Antrian Verifikasi Penuh
                            </h4>
                            <p class="text-[12px] text-slate-500 leading-relaxed">
                                Ada <strong>{{ count($pendingVerifications) }}</strong> pengajuan menunggu
                                persetujuan.
                            </p>
                        </div>
                    </div>
                @endif

                @if (!$hasAnyAlert)
                    <div class="flex gap-3 items-start">
                        <i class="ri-shield-check-line text-[#10B981] text-[20px] flex-shrink-0 mt-0.5"></i>
                        <div>
                            <h4 class="text-[14px] text-slate-900 mb-1 font-semibold">Semua Sistem Normal</h4>
                            <p class="text-[12px] text-slate-500 leading-relaxed">Tidak ada masalah terdeteksi.
                            </p>
                        </div>
                    </div>
                @endif

            </div>
        </section>

    </div>{{-- END grid Pending + Alerts --}}

    <!-- Admin content area -->
    <section id="admin-content" class="mt-10 animate-fadeUp-delay-2"></section>
@endsection

@section('scripts')
    <script>
        const hasUnreadMessages =
            {{ (isset($pendingVerifications) && count($pendingVerifications) > 0) || (isset($openDisputes) && $openDisputes > 0) ? 'true' : 'false' }};

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const icon = type === 'success' ? 'ri-check-double-line' : 'ri-close-circle-line';
            const toast = document.createElement('div');
            toast.className = `toast toast-${type === 'success' ? 'success' : 'danger'}`;
            toast.innerHTML = `
    <i class="toast-icon ${icon}"></i>
    <span>${message}</span>
    <button class="toast-close" onclick="dismissToast(this.parentElement)"><i class="ri-close-line"></i></button>`;
            container.appendChild(toast);
            setTimeout(() => dismissToast(toast), 3500);
        }

        function dismissToast(toast) {
            if (!toast || toast.classList.contains('toast-hide')) return;
            toast.classList.add('toast-hide');
            setTimeout(() => toast.remove(), 300);
        }

        async function handleApprove(id) {
            const card = document.querySelector(`.approval-card[data-id="${id}"]`);
            if (!card) return;

            const name = card.querySelector('.user-name')?.textContent?.trim() ?? 'Freelancer';

            try {
                const response = await fetch(`{{ url('/admin/verify-freelancer') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('DB Update Failed');

                card.classList.add('card-approved');
                showToast(`${name} berhasil diverifikasi!`, 'success');

                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        card.remove();
                        const container = document.getElementById('verification-container');
                        if (container && container.querySelectorAll('.approval-card').length === 0) {
                            container.innerHTML = `
            <div class="text-center py-10 bg-white border-2 border-dashed border-slate-200 rounded-2xl animate-fadeUp">
              <p class="text-slate-400 font-medium">Semua pengajuan sudah diproses! ✅</p>
            </div>`;
                        }
                    }, 300);
                }, 800);

            } catch (error) {
                showToast("Gagal memverifikasi. Coba lagi.", "danger");
            }
        }

        async function handleReject(id) {
            const card = document.querySelector(`.approval-card[data-id="${id}"]`);
            if (!card) return;

            if (!confirm('Yakin ingin menolak verifikasi ini?')) return;

            try {
                const response = await fetch(`{{ url('/admin/reject-freelancer') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Gagal update database');

                card.classList.add('card-rejected');
                showToast("Verifikasi ditolak.", "danger");

                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(-30px)';
                    setTimeout(() => card.remove(), 400);
                }, 800);

            } catch (error) {
                showToast("Gagal menolak verifikasi.", "danger");
            }
        }

        function initAdminDashboard() {
            const notifBtn = document.getElementById('notif-btn');
            if (!notifBtn) return;
            notifBtn.classList.toggle('has-unread', hasUnreadMessages);
            notifBtn.addEventListener('click', () => {
                notifBtn.classList.remove('has-unread');
            });
        }

        // ─── SEARCH ───
        const searchMenus = [{
                label: 'Dashboard',
                sub: 'Halaman utama admin',
                icon: 'ri-dashboard-fill',
                url: '{{ route('admin.dashboard') }}',
                available: true
            },
            {
                label: 'Clients',
                sub: 'User › Daftar client',
                icon: 'ri-user-line',
                url: '{{ route('admin.clients.index') }}',
                available: true
            },
            {
                label: 'Freelancers',
                sub: 'User › Daftar freelancer',
                icon: 'ri-user-star-line',
                url: '{{ route('admin.freelancers.index') }}',
                available: true
            },
            {
                label: 'Skomda Students',
                sub: 'User › Data siswa skomda',
                icon: 'ri-user-line',
                url: '{{ route('admin.skomda-students.index') }}',
                available: true
            },
            {
                label: 'Administrators',
                sub: 'Admin › Daftar admin',
                icon: 'ri-user-star-line',
                url: '{{ route('admin.admins.index') }}',
                available: true
            },
            {
                label: 'Services',
                sub: 'Daftar layanan',
                icon: 'ri-tools-line',
                url: '{{ route('admin.services.index') }}',
                available: true
            },
            {
                label: 'Service Categories',
                sub: 'Kategori layanan',
                icon: 'ri-layout-grid-line',
                url: '{{ route('admin.service-categories.index') }}',
                available: true
            },
            {
                label: 'Transactions',
                sub: 'Riwayat transaksi',
                icon: 'ri-bank-card-line',
                url: '{{ route('admin.transactions.index') }}',
                available: true
            },
            {
                label: 'Orders',
                sub: 'Belum tersedia',
                icon: 'ri-file-list-3-line',
                url: null,
                available: false
            },
            {
                label: 'Reviews',
                sub: 'Belum tersedia',
                icon: 'ri-star-line',
                url: null,
                available: false
            },
            {
                label: 'Portofolios',
                sub: 'Belum tersedia',
                icon: 'ri-folder-user-line',
                url: null,
                available: false
            },
            {
                label: 'Offers',
                sub: 'Belum tersedia',
                icon: 'ri-price-tag-3-line',
                url: null,
                available: false
            },
            {
                label: 'Working',
                sub: 'Belum tersedia',
                icon: 'ri-hammer-line',
                url: null,
                available: false
            },
            {
                label: 'Negotiations',
                sub: 'Belum tersedia',
                icon: 'ri-discuss-line',
                url: null,
                available: false
            },
            {
                label: 'Profile Admin',
                sub: 'Akun › Profil administrator',
                icon: 'ri-user-line',
                url: '{{ url('admin/profile') }}',
                available: true
            },
        ];

        const searchInput = document.getElementById('search-input');
        const searchDropdown = document.getElementById('search-dropdown');
        const searchResults = document.getElementById('search-results');

        function renderSearch(query) {
            const q = query.trim().toLowerCase();
            const filtered = q === '' ?
                searchMenus :
                searchMenus.filter(m =>
                    m.label.toLowerCase().includes(q) ||
                    m.sub.toLowerCase().includes(q)
                );

            if (filtered.length === 0) {
                searchResults.innerHTML = `
      <div class="px-4 py-5 text-center text-slate-400 text-[13px]">
        <i class="ri-search-2-line text-2xl mb-1 block"></i>
        Tidak ada hasil untuk "<strong>${query}</strong>"
      </div>`;
            } else {
                searchResults.innerHTML = filtered.map(m => `
      ${m.available
        ? `<a href="${m.url}"
                          class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors cursor-pointer group">
                          <span class="w-8 h-8 rounded-lg bg-[#f0fdf9] flex items-center justify-center text-[#0f766e] text-[15px] flex-shrink-0">
                            <i class="${m.icon}"></i>
                          </span>
                          <div class="flex-1 min-w-0">
                            <p class="text-[13.5px] font-semibold text-slate-800 group-hover:text-[#0f766e]">${m.label}</p>
                            <p class="text-[11px] text-slate-400 truncate">${m.sub}</p>
                          </div>
                          <i class="ri-arrow-right-s-line text-slate-300 group-hover:text-[#0f766e] text-[17px]"></i>
                        </a>`
        : `<div class="flex items-center gap-3 px-4 py-2.5 opacity-40 cursor-not-allowed">
                          <span class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-[15px] flex-shrink-0">
                            <i class="${m.icon}"></i>
                          </span>
                          <div class="flex-1 min-w-0">
                            <p class="text-[13.5px] font-semibold text-slate-500">${m.label}</p>
                            <p class="text-[11px] text-slate-400 truncate">${m.sub}</p>
                          </div>
                          <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-0.5 rounded-full font-medium">Soon</span>
                        </div>`
      }
    `).join('');
            }
            searchDropdown.classList.remove('hidden');
        }

        searchInput.addEventListener('focus', () => renderSearch(searchInput.value));
        searchInput.addEventListener('input', () => renderSearch(searchInput.value));

        document.addEventListener('click', (e) => {
            if (!document.getElementById('search-wrapper').contains(e.target)) {
                searchDropdown.classList.add('hidden');
            }
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                searchDropdown.classList.add('hidden');
                searchInput.blur();
            }
        });

        document.addEventListener('DOMContentLoaded', initAdminDashboard);
    </script>
@endsection
