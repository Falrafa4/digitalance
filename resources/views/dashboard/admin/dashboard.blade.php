@extends('layouts.dashboard')
@section('title', 'Dasbor Administrator | Digitalance')

@section('content')
    {{-- Welcome --}}
    <section class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-8 animate-fadeUp">
        <div class="min-w-0 flex-1">
            <h1 class="font-display text-[1.85rem] sm:text-[2.1rem] font-extrabold text-slate-900 leading-tight">
                Selamat datang, {{ Auth::guard('administrator')->user()->name ?? 'Admin' }}!
                <span class="inline-block">👋</span>
            </h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">
                {{ now()->format('l, d F Y') }} — Berikut ringkasan aktivitas platform hari ini.
            </p>
        </div>
        <div class="flex items-center gap-3" id="dashboard-summary-cards">
            <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm flex flex-col items-end">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pesanan Hari Ini</span>
                <span class="text-lg font-black text-slate-900">{{ $todayOrders ?? 0 }}</span>
            </div>
            <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm flex flex-col items-end">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pendapatan Hari Ini</span>
                <span
                    class="text-lg font-black text-slate-900 text-[#0f766e]">Rp{{ number_format(($todayRevenue ?? 0), 0, ',', '.') }}</span>
            </div>
        </div>
    </section>

    {{-- Advanced Metrics --}}
    <section id="stats-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6 mb-10">
        {{-- Total Users --}}
        <div
            class="bg-white p-5 rounded-[22px] border border-slate-100 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1">
            <div
                class="w-12 h-12 flex items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 text-xl shadow-inner">
                <i class="ri-group-line"></i>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-widest">Total Pengguna</span>
                <div class="text-[1.85rem] font-extrabold text-slate-900 leading-none mt-1">
                    {{ number_format($totalUsers ?? 0) }}
                </div>
            </div>
        </div>

        {{-- Total Revenue (Platform Fee) --}}
        <div
            class="bg-white p-5 rounded-[22px] border border-slate-100 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1">
            <div
                class="w-12 h-12 flex items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 text-xl shadow-inner">
                <i class="ri-money-dollar-circle-line"></i>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-widest">Total Pendapatan</span>
                <div class="text-[1.85rem] font-extrabold text-slate-900 leading-none mt-1">
                    Rp{{ number_format(($totalRevenue ?? 0), 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Total Turnover --}}
        <div
            class="bg-white p-5 rounded-[22px] border border-slate-100 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1">
            <div
                class="w-12 h-12 flex items-center justify-center rounded-2xl bg-blue-50 text-blue-600 text-xl shadow-inner">
                <i class="ri-exchange-funds-line"></i>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-widest">Total Transaksi</span>
                <div class="text-[1.85rem] font-extrabold text-slate-900 leading-none mt-1">
                    Rp{{ number_format(($totalTurnover ?? 0), 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Open Disputes --}}
        <div
            class="bg-white p-5 rounded-[22px] border border-slate-100 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1">
            <div
                class="w-12 h-12 flex items-center justify-center rounded-2xl bg-amber-50 text-amber-600 text-xl shadow-inner">
                <i class="ri-time-line"></i>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-widest">Sengketa Terbuka</span>
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
                    <p class="text-slate-500 text-xs">Visualisasi pendapatan platform (10% fee).</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex bg-slate-100 rounded-lg p-1">
                        <button type="button" onclick="switchChartView('weekly')" id="chart-weekly-btn"
                            class="px-3 py-1.5 rounded-md text-xs font-bold text-slate-500 hover:text-[#0f766e] transition-all">Mingguan</button>
                        <button type="button" onclick="switchChartView('monthly')" id="chart-monthly-btn"
                            class="px-3 py-1.5 rounded-md text-xs font-bold bg-white text-[#0f766e] shadow-sm transition-all">Bulanan</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#0f766e]"></span>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Revenue Growth</span>
                    </div>
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
                        <h2 class="font-display text-[1.4rem] font-extrabold text-slate-900">Pesanan Revisi</h2>
                        <p class="text-slate-500 text-xs">Pesanan dalam status Revision yang memerlukan pantauan.</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}"
                        class="text-[11px] font-bold text-[#0f766e] uppercase tracking-wider hover:underline">View All
                        Orders</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($disputedOrders as $order)
                        <div
                            class="bg-white border border-slate-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <span
                                    class="px-2 py-1 bg-amber-100 text-amber-700 text-[9px] font-bold uppercase rounded-lg">Revisi
                                    / Sengketa</span>
                                <span class="text-[10px] font-mono text-slate-400">#ORD-{{ $order->id }}</span>
                            </div>
                            <h3 class="font-bold text-slate-800 text-[14px] mb-1 truncate">
                                {{ $order->service->title ?? 'Tidak tersedia' }}
                            </h3>
                            <div class="flex items-center gap-2 mb-4 text-[12px]">
                                <span class="text-slate-500">Klien:</span>
                                <span class="font-bold text-slate-700">{{ $order->client->name ?? 'Pengguna' }}</span>
                            </div>
                            <button onclick="window.openDisputeDetail({{ $order->id }})"
                                class="w-full py-2.5 bg-slate-50 text-slate-600 rounded-xl text-[12px] font-bold hover:bg-[#0f766e] hover:text-white transition-all">Lihat
                                Detail Pengajuan</button>
                        </div>
                    @empty
                        <div
                            class="md:col-span-2 py-10 bg-slate-50/50 rounded-2xl border-2 border-dashed border-slate-100 flex flex-col items-center justify-center text-center">
                            <i class="ri-checkbox-circle-line text-4xl text-slate-200 mb-2"></i>
                            <p class="text-slate-400 text-sm font-medium">Tidak ada sengketa saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Pending Verifications --}}
            <section>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-display text-[1.4rem] font-extrabold text-slate-900">Verifikasi Freelancer</h2>
                        <p class="text-slate-500 text-xs">Calon talent baru yang menunggu persetujuan admin.</p>
                    </div>
                    @if($totalPendingCount > 3)
                        <a href="{{ route('admin.freelancers.index') }}?status=Pending"
                            class="text-[11px] font-bold text-[#0f766e] uppercase tracking-wider hover:underline">Lihat
                            Selengkapnya ({{ $totalPendingCount }})</a>
                    @endif
                </div>

                <div id="verification-container" data-verify-url="{{ url('/admin/verify-freelancer/__ID__') }}"
                    data-reject-url="{{ url('/admin/reject-freelancer/__ID__') }}"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    @forelse(($pendingVerifications ?? []) as $v)
                        <div class="approval-card bg-white border border-slate-100 rounded-2xl p-5 hover:shadow-md transition-all group"
                            data-id="{{ $v->id }}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3.5">
                                    <img class="w-11 h-11 rounded-xl object-cover border-2 border-slate-50 shadow-sm"
                                           alt="Avatar"
                                           src="{{ $v->skomda_student?->avatar ? asset('storage/' . $v->skomda_student->avatar) : 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($v->skomda_student->name ?? $v->skomda_student->email ?? 'User') . '&background=0f766e&color=fff&size=44' }}" />
                                    <div class="min-w-0">
                                        <span
                                            class="font-bold text-[14px] text-slate-900 user-name block truncate">{{ $v->skomda_student->name ?? 'Freelancer' }}</span>
                                        <p class="text-[11px] text-slate-400 font-medium uppercase tracking-tight">
                                            {{ $v->skomda_student->major ?? 'Siswa Skomda' }}
                                        </p>
                                    </div>
                                </div>
                                <button data-action="detail"
                                    class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-[#0f766e] hover:text-white transition-all">
                                    <i class="ri-information-line text-lg"></i>
                                </button>
                            </div>
                            <div class="flex gap-2 pt-3.5 border-t border-slate-50">
                                <button type="button" data-action="approve"
                                    class="flex-1 py-2 bg-emerald-50 text-emerald-600 text-[11px] font-bold rounded-lg hover:bg-emerald-600 hover:text-white transition-all">Setujui</button>
                                <button type="button" data-action="reject"
                                    class="flex-1 py-2 bg-red-50 text-red-600 text-[11px] font-bold rounded-lg hover:bg-red-600 hover:text-white transition-all">Tolak</button>
                            </div>
                        </div>
                    @empty
                        <div
                            class="md:col-span-2 py-10 bg-slate-50/50 rounded-2xl border-2 border-dashed border-slate-100 flex flex-col items-center justify-center text-center">
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
                        <div
                            class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 p-5 rounded-2xl shadow-sm animate-pulse-slow">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-amber-600">
                                    <i class="ri-alert-fill text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-[13px] text-amber-900 font-extrabold uppercase">Critical Alert</h4>
                                    <p class="text-[11px] text-amber-700 font-bold uppercase opacity-60">High Priority</p>
                                </div>
                            </div>
                            <p class="text-[13px] text-amber-800 leading-relaxed font-medium">
                                Ada <strong class="text-amber-900">{{ $openDisputes }}</strong> sengketa aktif yang memerlukan
                                mediasi segera.
                            </p>
                        </div>
                    @endif

                    <div class="bg-white border border-slate-100 p-5 rounded-2xl">
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Recent Transactions
                        </h4>
                        <div class="space-y-4">
                            @forelse($recentTransactions as $trx)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400">
                                            <i class="ri-arrow-right-up-line"></i>
                                        </div>
                                        <div>
                                            <p class="text-[12px] font-bold text-slate-800">#TRX-{{ $trx->id }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $trx->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-[12px] font-extrabold text-emerald-600">+Rp{{ number_format($trx->amount, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-center text-slate-400 text-xs py-4">Belum ada transaksi.</p>
                            @endforelse
                        </div>
                        <a href="{{ route('admin.transactions.index') }}"
                            class="block text-center mt-6 py-2 border-t border-slate-50 text-[11px] font-bold text-[#0f766e] uppercase hover:opacity-70 transition-all">View
                            All Transactions</a>
                    </div>
                </div>
            </section>
        </div>
    </div>

    {{-- MODALS: Dispute Detail & Verification --}}
    <div class="modal-overlay fixed inset-0 z-[60] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300"
        id="modal-dispute-overlay">
        <div class="modal-box bg-white rounded-[28px] w-full max-w-[600px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300"
            id="modal-dispute-box">
            <div class="p-8 text-center py-20">
                <div class="animate-spin w-8 h-8 border-4 border-[#0f766e] border-t-transparent rounded-full mx-auto mb-4">
                </div>
                <p class="text-slate-500 font-bold">Memuat detail sengketa...</p>
            </div>
        </div>
    </div>

    <div class="modal-overlay fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300"
        id="modal-verify-overlay">
        <div class="bg-white rounded-[32px] w-full max-w-[550px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300"
            id="modal-verify-box">
        </div>
    </div>

    <div class="modal-overlay fixed inset-0 z-[110] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300"
        id="modal-reject-verify-overlay">
        <div class="modal-box bg-white rounded-[28px] w-full max-w-[480px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300">
            <div class="px-7 py-6 bg-gradient-to-r from-rose-500 to-red-600 text-white">
                <p class="text-[10px] font-black uppercase tracking-[0.22em] opacity-80 mb-2">Verifikasi Freelancer</p>
                <h3 class="font-display text-[1.2rem] font-extrabold leading-tight">Tolak Pengajuan Freelancer</h3>
                <p class="text-white/80 text-[12px] mt-1.5">Sertakan alasan yang jelas agar freelancer tahu apa yang perlu diperbaiki.</p>
            </div>

            <form id="reject-verification-form" class="p-7">
                <label for="reject-verification-reason" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                    Alasan Penolakan
                </label>
                <textarea id="reject-verification-reason" rows="5" maxlength="500" required
                    placeholder="Tuliskan alasan penolakan berkas freelancer..."
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13px] text-slate-700 font-medium leading-relaxed outline-none transition focus:border-rose-300 focus:ring-2 focus:ring-rose-100"></textarea>
                <p class="text-[11px] text-slate-400 mt-2">
                    Alasan wajib diisi dan akan dikirim ke freelancer.
                </p>

                <div class="flex gap-3 mt-6">
                    <button type="button" id="reject-verification-cancel"
                        class="flex-1 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold text-[13px] hover:bg-slate-200 transition-all">
                        Batal
                    </button>
                    <button type="submit" id="reject-verification-submit"
                        class="flex-1 py-3 rounded-xl bg-rose-600 text-white font-bold text-[13px] hover:bg-rose-700 transition-all shadow-lg shadow-rose-100">
                        Kirim Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Admin content area placeholder --}}
    <section id="admin-content" class="mt-10 animate-fadeUp-delay-2"></section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.__DASHBOARD_CHART_DATA__ = {
            monthly: @json($monthlyTurnover),
            weekly: @json($weeklyTurnover)
        };
        window.__PENDING_VERIFICATIONS__ = @json($pendingVerifications ?? []);
    </script>
    <script>

        let performanceChart = null;

        function initChart(viewType) {
            const ctx = document.getElementById('performanceChart');
            if (!ctx) return;

            const dataSet = window.__DASHBOARD_CHART_DATA__[viewType] || [];
            let labels = [];
            let dataValues = [];

            if (viewType === 'monthly') {
                const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
                labels = dataSet.map(item => item.month ? `${monthNames[item.month - 1]} ${item.year}` : `${item.year}`);
                dataValues = dataSet.map(item => item.total || 0);
            } else {
                labels = dataSet.map(item => item.week_label || `W-${item.yearweek}`);
                dataValues = dataSet.map(item => item.total || 0);
            }

            if (performanceChart) {
                performanceChart.destroy();
            }

            performanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: dataValues,
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15, 118, 110, 0.04)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#0f766e',
                        pointHoverRadius: 6,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (val) {
                                    return window.DigitalanceUtils.formatRupiah(val);
                                }
                            }
                        }
                    }
                }
            });
        }

        window.switchChartView = function (viewType) {
            const weeklyBtn = document.getElementById('chart-weekly-btn');
            const monthlyBtn = document.getElementById('chart-monthly-btn');

            if (viewType === 'weekly') {
                weeklyBtn.classList.add('bg-white', 'text-[#0f766e]', 'shadow-sm');
                weeklyBtn.classList.remove('text-slate-500');
                monthlyBtn.classList.remove('bg-white', 'text-[#0f766e]', 'shadow-sm');
                monthlyBtn.classList.add('text-slate-500');
            } else {
                monthlyBtn.classList.add('bg-white', 'text-[#0f766e]', 'shadow-sm');
                monthlyBtn.classList.remove('text-slate-500');
                weeklyBtn.classList.remove('bg-white', 'text-[#0f766e]', 'shadow-sm');
                weeklyBtn.classList.add('text-slate-500');
            }
            initChart(viewType);
        };

        document.addEventListener('DOMContentLoaded', () => {
            initChart('monthly');
        });
    </script>
    <script src="{{ asset('js/dashboard/admin/dashboard.js') }}"></script>
@endsection
