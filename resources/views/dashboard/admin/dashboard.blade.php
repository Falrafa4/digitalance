
@extends('layouts.dashboard')
@section('title', 'Admin Dashboard | Digitalance')

@section('content')
    {{-- Welcome --}}
    <section class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-8 animate-fadeUp">
        <div class="min-w-0 flex-1">
            <h1 class="font-display text-[1.85rem] sm:text-[2.1rem] font-extrabold text-slate-900 leading-tight">
                Selamat datang, {{ Auth::user()->name }}!
                <span class="inline-block">👋</span>
            </h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">
                {{ now()->format('l, d F Y') }} — Berikut ringkasan aktivitas platform hari ini.
            </p>
        </div>
        <div class="flex items-center gap-3" id="dashboard-summary-cards">
            <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm flex flex-col items-end">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Today's Orders</span>
                <span class="text-lg font-black text-slate-900">{{ $todayOrders ?? 0 }}</span>
            </div>
            <div class="bg-[#0f766e] px-4 py-2 rounded-xl shadow-teal-sm flex flex-col items-end">
                <span class="text-[10px] font-bold text-white/70 uppercase tracking-widest">Est. Revenue</span>
                <span class="text-lg font-black text-white">Rp{{ number_format(($todayRevenue ?? 0), 0, ',', '.') }}</span>
            </div>
        </div>
    </section>

    {{-- Advanced Metrics --}}
    <section id="stats-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6 mb-10">
        {{-- Total Users --}}
        <div class="bg-white p-5 rounded-[22px] border border-slate-100 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1">
            <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 text-xl shadow-inner">
                <i class="ri-group-line"></i>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-widest">Total Users</span>
                <div class="text-[1.85rem] font-extrabold text-slate-900 leading-none mt-1">
                    {{ number_format($totalUsers ?? 0) }}
                </div>
            </div>
        </div>

        {{-- Total Revenue (Platform Fee) --}}
        <div class="bg-white p-5 rounded-[22px] border border-slate-100 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1">
            <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 text-xl shadow-inner">
                <i class="ri-money-dollar-circle-line"></i>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-widest">Platform Revenue</span>
                <div class="text-[1.85rem] font-extrabold text-slate-900 leading-none mt-1">
                    <span class="text-[0.6em] text-slate-400 mr-0.5">Rp</span>{{ number_format(($totalRevenue ?? 0) / 1000, 0) }}<span class="text-[0.4em] text-slate-400">jt</span>
                </div>
            </div>
        </div>

        {{-- Total Turnover --}}
        <div class="bg-white p-5 rounded-[22px] border border-slate-100 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1">
            <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-blue-50 text-blue-600 text-xl shadow-inner">
                <i class="ri-exchange-funds-line"></i>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-widest">Total Turnover</span>
                <div class="text-[1.85rem] font-extrabold text-slate-900 leading-none mt-1">
                    <span class="text-[0.6em] text-slate-400 mr-0.5">Rp</span>{{ number_format(($totalTurnover ?? 0) / 1000000, 1) }}<span class="text-[0.4em] text-slate-400">jt</span>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white p-5 rounded-[22px] border border-slate-100 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1">
            <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-amber-50 text-amber-600 text-xl shadow-inner">
                <i class="ri-time-line"></i>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-widest">Open Disputes</span>
                <div class="text-[1.85rem] font-extrabold text-slate-900 leading-none mt-1">
                    {{ $openDisputes ?? 0 }}
                </div>
            </div>
        </div>
    </section>

    {{-- Analytics Chart --}}
    <section class="mb-10 animate-fadeUp-delay-1">
        <div class="bg-white p-7 rounded-[28px] border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-display text-[1.4rem] font-extrabold text-slate-900">Platform Revenue</h2>
                    <p class="text-slate-500 text-xs">Visualisasi pendapatan platform (10% fee) dalam 6 bulan terakhir.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#0f766e]"></span>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Revenue Growth</span>
                </div>
            </div>
            <div class="h-[320px] w-full">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </section>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10">
        
        {{-- LEFT COLUMN: Disputed Orders & Verifications --}}
        <div class="lg:col-span-8 space-y-10">
            
            {{-- Disputed Orders (Revision) --}}
            <section>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-display text-[1.4rem] font-extrabold text-slate-900">Disputed Orders</h2>
                        <p class="text-slate-500 text-xs">Pesanan dalam status Revision yang memerlukan pantauan.</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="text-[11px] font-bold text-[#0f766e] uppercase tracking-wider hover:underline">View All Orders</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($disputedOrders as $order)
                        <div class="bg-white border border-slate-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[9px] font-bold uppercase rounded-lg">Revision / Dispute</span>
                                <span class="text-[10px] font-mono text-slate-400">#ORD-{{ $order->id }}</span>
                            </div>
                            <h3 class="font-bold text-slate-800 text-[14px] mb-1 truncate">{{ $order->service->title ?? 'N/A' }}</h3>
                            <div class="flex items-center gap-2 mb-4 text-[12px]">
                                <span class="text-slate-500">Client:</span>
                                <span class="font-bold text-slate-700">{{ $order->client->name ?? 'User' }}</span>
                            </div>
                            <button onclick="openDisputeDetail({{ $order->id }})" class="w-full py-2.5 bg-slate-50 text-slate-600 rounded-xl text-[12px] font-bold hover:bg-[#0f766e] hover:text-white transition-all">Lihat Detail Pengajuan</button>
                        </div>
                    @empty
                        <div class="md:col-span-2 py-10 bg-slate-50/50 rounded-2xl border-2 border-dashed border-slate-100 flex flex-col items-center justify-center text-center">
                            <i class="ri-checkbox-circle-line text-4xl text-slate-200 mb-2"></i>
                            <p class="text-slate-400 text-sm font-medium">Tidak ada dispute saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Pending Verifications --}}
            <section>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-display text-[1.4rem] font-extrabold text-slate-900">Freelancer Verifications</h2>
                        <p class="text-slate-500 text-xs">Calon talent baru yang menunggu persetujuan admin.</p>
                    </div>
                    @if($totalPendingCount > 3)
                        <a href="{{ route('admin.freelancers.index') }}?status=Pending" class="text-[11px] font-bold text-[#0f766e] uppercase tracking-wider hover:underline">Lihat Selengkapnya ({{ $totalPendingCount }})</a>
                    @endif
                </div>

                <div id="verification-container" 
                     data-verify-url="{{ url('/admin/verify-freelancer/__ID__') }}"
                     data-reject-url="{{ url('/admin/reject-freelancer/__ID__') }}" 
                     class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    @forelse(($pendingVerifications ?? []) as $v)
                        <div class="approval-card bg-white border border-slate-100 rounded-2xl p-5 hover:shadow-md transition-all group" data-id="{{ $v->id }}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3.5">
                                    <img class="w-11 h-11 rounded-xl object-cover border-2 border-slate-50 shadow-sm" alt="Avatar"
                                        src="https://ui-avatars.com/api/?name={{ urlencode($v->skomda_student->name ?? 'F') }}&background=0f766e&color=fff" />
                                    <div class="min-w-0">
                                        <span class="font-bold text-[14px] text-slate-900 user-name block truncate">{{ $v->skomda_student->name ?? 'Freelancer' }}</span>
                                        <p class="text-[11px] text-slate-400 font-medium uppercase tracking-tight">{{ $v->skomda_student->major ?? 'Siswa Skomda' }}</p>
                                    </div>
                                </div>
                                <button onclick="openVerificationDetail({{ $v->id }})" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                                    <i class="ri-information-line text-lg"></i>
                                </button>
                            </div>
                            <div class="flex gap-2 pt-3.5 border-t border-slate-50">
                                <button type="button" data-action="approve" class="flex-1 py-2 bg-emerald-50 text-emerald-600 text-[11px] font-bold rounded-lg hover:bg-emerald-600 hover:text-white transition-all">Approve</button>
                                <button type="button" data-action="reject" class="flex-1 py-2 bg-red-50 text-red-600 text-[11px] font-bold rounded-lg hover:bg-red-600 hover:text-white transition-all">Reject</button>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 py-10 bg-slate-50/50 rounded-2xl border-2 border-dashed border-slate-100 flex flex-col items-center justify-center text-center">
                            <i class="ri-user-smile-line text-4xl text-slate-200 mb-2"></i>
                            <p class="text-slate-400 text-sm font-medium">Semua talent sudah terverifikasi.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- RIGHT COLUMN: Transactions & Alerts --}}
        <div class="lg:col-span-4 space-y-10">
            
            {{-- System Alerts --}}
            <section>
                <h2 class="font-display text-[1.2rem] font-bold mb-6">Security & Alerts</h2>
                <div class="space-y-4">
                    @if (($openDisputes ?? 0) > 0)
                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 p-5 rounded-2xl shadow-sm animate-pulse-slow">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-amber-600">
                                    <i class="ri-alert-fill text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-[13px] text-amber-900 font-extrabold uppercase">Critical Alert</h4>
                                    <p class="text-[11px] text-amber-700 font-bold uppercase opacity-60">High Priority</p>
                                </div>
                            </div>
                            <p class="text-[13px] text-amber-800 leading-relaxed font-medium">
                                Ada <strong class="text-amber-900">{{ $openDisputes }}</strong> dispute aktif yang memerlukan mediasi segera.
                            </p>
                        </div>
                    @endif

                    <div class="bg-white border border-slate-100 p-5 rounded-2xl">
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Recent Transactions</h4>
                        <div class="space-y-4">
                            @forelse($recentTransactions as $trx)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400">
                                            <i class="ri-arrow-right-up-line"></i>
                                        </div>
                                        <div>
                                            <p class="text-[12px] font-bold text-slate-800">#TRX-{{ $trx->id }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $trx->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <span class="text-[12px] font-extrabold text-emerald-600">+Rp{{ number_format($trx->amount, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-center text-slate-400 text-xs py-4">Belum ada transaksi.</p>
                            @endforelse
                        </div>
                        <a href="{{ route('admin.transactions.index') }}" class="block text-center mt-6 py-2 border-t border-slate-50 text-[11px] font-bold text-[#0f766e] uppercase hover:opacity-70 transition-all">View All Transactions</a>
                    </div>
                </div>
            </section>
        </div>
    </div>

    {{-- MODALS: Dispute Detail & Verification --}}
    <div class="modal-overlay fixed inset-0 z-[60] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-dispute-overlay">
        <div class="modal-box bg-white rounded-[28px] w-full max-w-[600px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-dispute-box">
            <div class="p-8 text-center py-20">
                <div class="animate-spin w-8 h-8 border-4 border-[#0f766e] border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="text-slate-500 font-bold">Memuat detail dispute...</p>
            </div>
        </div>
    </div>

    <div class="modal-overlay fixed inset-0 z-[60] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-verify-overlay">
        <div class="modal-box bg-white rounded-[28px] w-full max-w-[500px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-verify-box">
            <div class="p-8 text-center py-20">
                <div class="animate-spin w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="text-slate-500 font-bold">Memuat data freelancer...</p>
            </div>
        </div>
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
        // Using global window.showToast and window.openModal/closeModal instead
        

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
                window.showToast(`${name} berhasil diverifikasi!`, 'success');

                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => card.remove(), 300);
                }, 800);
            } catch (error) {
                window.showToast(error?.message || "Gagal memverifikasi. Coba lagi.", "danger");
                setCardLoading(card, false);
            }
        }

        async function handleReject(id) {
            const card = document.querySelector(`.approval-card[data-id="${id}"]`);
            const container = document.getElementById('verification-container');
            if (!card || !container) return;

            if (!(await customConfirm('Yakin ingin menolak verifikasi ini?'))) return;

            const rejectUrl = (container.dataset.rejectUrl || '').replace('__ID__', id);
            if (!rejectUrl) return;

            setCardLoading(card, true);

            try {
                await postAction(rejectUrl);

                card.classList.add('card-rejected');
                window.showToast("Verifikasi ditolak.", "danger");

                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(-30px)';
                    setTimeout(() => card.remove(), 400);
                }, 800);
            } catch (error) {
                window.showToast(error?.message || "Gagal menolak verifikasi. Coba lagi.", "danger");
                setCardLoading(card, false);
            }
        }

        // Redundant definitions removed, using global openModal and closeModal

        window.openDisputeDetail = async function(id) {
            const overlay = document.getElementById('modal-dispute-overlay');
            const box = document.getElementById('modal-dispute-box');
            if (!overlay || !box) return;

            window.openModal('modal-dispute-overlay');

            try {
                const response = await fetch(`/admin/orders/${id}/dispute`);
                if (!response.ok) throw new Error('Gagal mengambil data');
                const data = await response.json();

                const client = data.client;
                const freelancer = data.freelancer;
                const order = data.order;
                const negos = data.negotiations || [];
                const results = data.results || [];

                box.innerHTML = `
                    <div class="modal-header relative h-24 bg-gradient-to-r from-amber-500 to-orange-600 flex items-center px-8">
                        <div class="flex-1">
                            <h2 class="text-white font-extrabold text-xl tracking-tight">Mediasi Dispute</h2>
                            <p class="text-white/80 text-[11px] font-bold uppercase tracking-wider">Order ID: #ORD-${order.id}</p>
                        </div>
                        <button onclick="window.closeModal('modal-dispute-overlay')" class="w-10 h-10 bg-white/20 text-white rounded-full flex items-center justify-center hover:bg-white/30 transition">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>

                    <div class="p-8 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-2 gap-6 mb-8">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Klien</span>
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(client.name)}&background=0f766e&color=fff" class="w-8 h-8 rounded-lg" />
                                    <span class="text-[13px] font-bold text-slate-800">${client.name}</span>
                                </div>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Freelancer</span>
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(freelancer.name)}&background=0f766e&color=fff" class="w-8 h-8 rounded-lg" />
                                    <span class="text-[13px] font-bold text-slate-800">${freelancer.name}</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <section>
                                <h3 class="text-[12px] font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i class="ri-history-line text-amber-500"></i> Negotiation History
                                </h3>
                                <div class="space-y-3 border-l-2 border-slate-100 ml-2 pl-6">
                                    ${negos.map(n => `
                                        <div class="relative">
                                            <div class="absolute -left-[31px] top-1 w-2.5 h-2.5 rounded-full bg-white border-2 border-slate-200"></div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-[11px] font-bold ${n.sender === 'Client' ? 'text-blue-600' : 'text-emerald-600'} uppercase">${n.sender}</span>
                                                <span class="text-[10px] text-slate-400">${new Date(n.created_at).toLocaleString('id-ID')}</span>
                                            </div>
                                            <p class="text-[13px] text-slate-700 leading-relaxed">${n.message || 'No message'}</p>
                                            ${n.proposed_price ? `<div class="mt-2 text-[11px] font-bold bg-slate-50 inline-block px-2 py-1 rounded">Tawaran: Rp${n.proposed_price.toLocaleString()}</div>` : ''}
                                        </div>
                                    `).join('') || '<p class="text-slate-400 text-xs italic">Belum ada riwayat negosiasi.</p>'}
                                </div>
                            </section>

                            <section>
                                <h3 class="text-[12px] font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i class="ri-file-list-3-line text-emerald-500"></i> Project Results
                                </h3>
                                <div class="space-y-3">
                                    ${results.map(r => `
                                        <div class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl flex items-center justify-between">
                                            <div>
                                                <p class="text-[12px] font-bold text-slate-800">${r.version || 'Version'}</p>
                                                <p class="text-[10px] text-slate-500">${new Date(r.created_at).toLocaleString('id-ID')}</p>
                                            </div>
                                            <a href="/storage/${r.file_url}" target="_blank" class="px-3 py-1.5 bg-white text-[#0f766e] border border-[#0f766e] rounded-lg text-[10px] font-bold hover:bg-[#0f766e] hover:text-white transition-all">Download</a>
                                        </div>
                                    `).join('') || '<p class="text-slate-400 text-xs italic">Belum ada hasil yang dikirim.</p>'}
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 border-t border-slate-100 flex gap-3">
                        <button onclick="window.closeModal('modal-dispute-overlay')" class="flex-1 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-100 transition-all">Tutup Detail</button>
                        <a href="/admin/orders" class="flex-1 py-3 bg-[#0f766e] text-white text-center font-bold rounded-xl text-sm hover:bg-[#0a5e58] transition-all shadow-lg shadow-teal-sm">Kelola di Halaman Order</a>
                    </div>
                `;

            } catch (error) {
                box.innerHTML = `
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-error-warning-line text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Gagal Memuat Data</h3>
                        <p class="text-slate-500 text-sm mb-6">${error.message}</p>
                        <button onclick="window.closeModal('modal-dispute-overlay')" class="px-8 py-2.5 bg-slate-900 text-white font-bold rounded-xl text-sm">Tutup</button>
                    </div>
                `;
            }
        }

        window.openVerificationDetail = async function(id) {
            const overlay = document.getElementById('modal-verify-overlay');
            const box = document.getElementById('modal-verify-box');
            if (!overlay || !box) return;

            window.openModal('modal-verify-overlay');

            try {
                const response = await fetch(`/admin/freelancers/${id}/detail`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errText = await response.text();
                    throw new Error('Server error: ' + response.status + '\n' + errText);
                }

                const data = await response.json();
                const student = data.skomda_student || {};

                box.innerHTML = `
                    <div class="modal-header relative h-24 bg-gradient-to-r from-indigo-600 to-indigo-700 flex items-center px-8">
                        <div class="flex-1">
                            <h2 class="text-white font-extrabold text-xl tracking-tight">Detail Verifikasi</h2>
                            <p class="text-white/80 text-[11px] font-bold uppercase tracking-wider">Freelancer ID: #FREELANCER-${data.id}</p>
                        </div>
                        <button onclick="window.closeModal('modal-verify-overlay')" class="w-10 h-10 bg-white/20 text-white rounded-full flex items-center justify-center hover:bg-white/30 transition">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>

                    <div class="p-8">
                        <div class="flex items-center gap-5 mb-8">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(student.name || 'F')}&background=4f46e5&color=fff&size=128" class="w-20 h-20 rounded-[22px] border-4 border-white shadow-lg" />
                            <div>
                                <h3 class="text-[1.3rem] font-black text-slate-900 leading-tight">${student.name || 'N/A'}</h3>
                                <p class="text-[13px] font-bold text-indigo-600 mt-1 uppercase tracking-wide">${student.major || 'Program Studi'}</p>
                            </div>
                        </div>

                        <div class="space-y-5 mb-8">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">NIS</span>
                                    <span class="text-[13.5px] font-extrabold text-slate-700 font-mono">${student.nis || '-'}</span>
                                </div>
                                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kelas</span>
                                    <span class="text-[13.5px] font-extrabold text-slate-700">${student.class || '-'}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Services</span>
                                    <span class="text-[13.5px] font-extrabold text-[#0f766e]">${data.services_count || 0} Terdaftar</span>
                                </div>
                                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Portofolio</span>
                                    <span class="text-[13.5px] font-extrabold text-blue-600">${data.portofolios_count || 0} Karya</span>
                                </div>
                            </div>
                            
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Email Sekolah</span>
                                <span class="text-[13.5px] font-bold text-slate-700">${student.email || '-'}</span>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tentang Freelancer (Bio)</span>
                                <p class="text-[13px] text-slate-600 leading-relaxed">${data.bio || 'Tidak ada bio tertulis.'}</p>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button onclick="handleReject(${data.id})" class="flex-1 py-3.5 bg-red-50 text-red-600 font-bold rounded-xl text-sm hover:bg-red-600 hover:text-white transition-all">Tolak</button>
                            <button onclick="handleApprove(${data.id})" class="flex-1 py-3.5 bg-emerald-600 text-white font-bold rounded-xl text-sm hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-sm">Setujui Akun</button>
                        </div>
                    </div>
                `;

            } catch (error) {
                box.innerHTML = `
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-error-warning-line text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Gagal Memuat Data</h3>
                        <p class="text-slate-500 text-sm mb-6">${error.message}</p>
                        <button onclick="window.closeModal('modal-verify-overlay')" class="px-8 py-2.5 bg-slate-900 text-white font-bold rounded-xl text-sm">Tutup</button>
                    </div>
                `;
            }
        }

        window.handleApprove = async function(id) {
            const container = document.getElementById('verification-container');
            const url = container.dataset.verifyUrl.replace('__ID__', id);
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (!response.ok) throw new Error('Gagal memproses verifikasi');
                
                const card = document.querySelector(`.approval-card[data-id="${id}"]`);
                if (card) {
                    card.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => card.remove(), 300);
                }
                window.showToast('Freelancer berhasil diverifikasi', 'success');
                window.closeModal('modal-verify-overlay');
            } catch (error) {
                window.showToast(error.message, 'error');
            }
        }

        window.handleReject = async function(id) {
            const container = document.getElementById('verification-container');
            const url = container.dataset.rejectUrl.replace('__ID__', id);
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (!response.ok) throw new Error('Gagal memproses penolakan');
                
                const card = document.querySelector(`.approval-card[data-id="${id}"]`);
                if (card) {
                    card.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => card.remove(), 300);
                }
                window.showToast('Freelancer telah ditolak', 'info');
                window.closeModal('modal-verify-overlay');
            } catch (error) {
                window.showToast(error.message, 'error');
            }
        }

        function initAdminDashboard() {
            // unread flag
            const flags = document.getElementById('page-flags');
            const hasUnreadMessages = flags ? flags.dataset.hasUnread === '1' : false;

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
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initPerformanceChart() {
            const ctx = document.getElementById('performanceChart');
            if (!ctx) return;

            const chartData = @json($monthlyTurnover);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            // Map the data correctly
            let labels = [];
            let totals = [];

            if (chartData && chartData.length > 0) {
                labels = chartData.map(d => months[d.month - 1] + ' ' + d.year);
                totals = chartData.map(d => parseFloat(d.total));
            }

            // Fallback for empty data
            if (labels.length === 0) {
                const now = new Date();
                for (let i = 5; i >= 0; i--) {
                    const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
                    labels.push(months[d.getMonth()] + ' ' + d.getFullYear());
                    totals.push(0);
                }
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Platform Revenue',
                        data: totals,
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15, 118, 110, 0.05)',
                        borderWidth: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0f766e',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { size: 13, family: 'Plus Jakarta Sans', weight: 'bold' },
                            bodyFont: { size: 13, family: 'Plus Jakarta Sans' },
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return ' Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: {
                                font: { size: 11, family: 'Plus Jakarta Sans' },
                                color: '#94a3b8',
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp' + (value / 1000000) + 'jt';
                                    if (value >= 1000) return 'Rp' + (value / 1000) + 'rb';
                                    return 'Rp' + value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                font: { size: 11, family: 'Plus Jakarta Sans', weight: 'bold' },
                                color: '#64748b'
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initAdminDashboard();
            setTimeout(initPerformanceChart, 100);
        });
    </script>
@endsection