/**
 * Admin Dashboard logic — extracted from dashboard/admin/dashboard.blade.php
 * Handles verification approve/reject, dispute detail modal, verification detail modal,
 * chart initialization, and delegation of approve/reject actions on approval cards.
 */

(function () {
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    csrfToken = csrfToken ? csrfToken.content : (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '');

    function setCardLoading(card, isLoading) {
        if (!card) return;
        card.querySelectorAll('button[data-action]').forEach(function (btn) { btn.disabled = isLoading; });
    }

    async function postAction(url) {
        var res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        var data = null;
        try { data = await res.json(); } catch (e) { }
        if (!res.ok) throw new Error(data?.message || 'Request gagal. Coba lagi.');
        return data;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    async function handleApprove(id) {
        var container = document.getElementById('verification-container');
        var card = document.querySelector('.approval-card[data-id="' + id + '"]');
        var name = card?.querySelector('.user-name')?.textContent?.trim() || 'Freelancer';
        var url = (container?.dataset.verifyUrl || '').replace('__ID__', id);
        if (!url) return;

        setCardLoading(card, true);
        try {
            await postAction(url);
            card.classList.add('card-approved');
            window.showToast(name + ' berhasil diverifikasi!', 'success');
            setTimeout(function () {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(function () { card.remove(); }, 300);
            }, 800);
        } catch (error) {
            window.showToast(error?.message || 'Gagal memverifikasi. Coba lagi.', 'danger');
            setCardLoading(card, false);
        }
    }

    async function handleReject(id) {
        var container = document.getElementById('verification-container');
        var card = document.querySelector('.approval-card[data-id="' + id + '"]');
        var url = (container?.dataset.rejectUrl || '').replace('__ID__', id);
        if (!url) return;

        var reason = prompt('Masukkan alasan penolakan:');
        if (reason === null) return;
        reason = reason.trim();
        if (!reason) {
            window.showToast('Alasan penolakan wajib diisi.', 'warning');
            return;
        }

        setCardLoading(card, true);
        try {
            var res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reason: reason })
            });
            var data = null;
            try { data = await res.json(); } catch (e) { }
            if (!res.ok) throw new Error(data?.message || 'Request gagal. Coba lagi.');

            card.classList.add('card-rejected');
            window.showToast('Verifikasi ditolak.', 'danger');
            setTimeout(function () {
                card.style.opacity = '0';
                card.style.transform = 'translateX(-30px)';
                setTimeout(function () { card.remove(); }, 400);
            }, 800);
        } catch (error) {
            window.showToast(error?.message || 'Gagal menolak verifikasi.', 'danger');
            setCardLoading(card, false);
        }
    }

    function initAdminDashboard() {
        var verificationContainer = document.getElementById('verification-container');
        if (verificationContainer) {
            verificationContainer.addEventListener('click', function (e) {
                var btn = e.target.closest('button[data-action]');
                if (!btn) return;
                var card = btn.closest('.approval-card');
                var id = card?.getAttribute('data-id');
                if (!id) return;
                var action = btn.getAttribute('data-action');
                if (action === 'approve') handleApprove(id);
                if (action === 'reject') handleReject(id);
            });
        }
    }

    window.openDisputeDetail = async function (id) {
        var overlay = document.getElementById('modal-dispute-overlay');
        var box = document.getElementById('modal-dispute-box');
        if (!overlay || !box) return;

        window.openModal('modal-dispute-overlay');

        try {
            var response = await fetch('/admin/orders/' + id + '/dispute');
            if (!response.ok) throw new Error('Gagal mengambil data');
            var data = await response.json();

            var client = data.client;
            var freelancer = data.freelancer;
            var order = data.order;
            var negos = data.negotiations || [];
            var results = data.results || [];

            box.innerHTML = '<div class="modal-header relative h-24 bg-gradient-to-r from-amber-500 to-orange-600 flex items-center px-8">' +
                '<div class="flex-1"><h2 class="text-white font-extrabold text-xl tracking-tight">Mediasi Dispute</h2><p class="text-white/80 text-[11px] font-bold uppercase tracking-wider">Order ID: #ORD-' + order.id + '</p></div>' +
                '<button onclick="window.closeModal(\'modal-dispute-overlay\')" class="w-10 h-10 bg-white/20 text-white rounded-full flex items-center justify-center hover:bg-white/30 transition"><i class="ri-close-line text-xl"></i></button>' +
                '</div>' +
                '<div class="p-8 max-h-[70vh] overflow-y-auto">' +
                '<div class="grid grid-cols-2 gap-6 mb-8">' +
                '<div class="p-4 bg-slate-50 rounded-2xl border border-slate-100"><span class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Klien</span><div class="flex items-center gap-3"><img src="https://ui-avatars.com/api/?name=' + encodeURIComponent(client.name) + '&background=0f766e&color=fff" class="w-8 h-8 rounded-lg" /><span class="text-[13px] font-bold text-slate-800">' + escapeHtml(client.name) + '</span></div></div>' +
                '<div class="p-4 bg-slate-50 rounded-2xl border border-slate-100"><span class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Freelancer</span><div class="flex items-center gap-3"><img src="https://ui-avatars.com/api/?name=' + encodeURIComponent(freelancer.name) + '&background=0f766e&color=fff" class="w-8 h-8 rounded-lg" /><span class="text-[13px] font-bold text-slate-800">' + escapeHtml(freelancer.name) + '</span></div></div>' +
                '</div>' +
                '<div class="space-y-8">' +
                '<section><h3 class="text-[12px] font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2"><i class="ri-history-line text-amber-500"></i> Negotiation History</h3>' +
                '<div class="space-y-3 border-l-2 border-slate-100 ml-2 pl-6">' +
                (negos.length ? negos.map(function (n) {
                    return '<div class="relative"><div class="absolute -left-[31px] top-1 w-2.5 h-2.5 rounded-full bg-white border-2 border-slate-200"></div>' +
                        '<div class="flex items-center justify-between mb-1"><span class="text-[11px] font-bold ' + (n.sender === 'Client' ? 'text-blue-600' : 'text-emerald-600') + ' uppercase">' + escapeHtml(n.sender) + '</span><span class="text-[10px] text-slate-400">' + new Date(n.created_at).toLocaleString('id-ID') + '</span></div>' +
                        '<p class="text-[13px] text-slate-700 leading-relaxed">' + escapeHtml(n.message || 'Tidak ada pesan') + '</p>' +
                        (n.proposed_price ? '<div class="mt-2 text-[11px] font-bold bg-slate-50 inline-block px-2 py-1 rounded">Tawaran: Rp' + n.proposed_price.toLocaleString() + '</div>' : '');
                }).join('') : '<p class="text-slate-400 text-xs italic">Belum ada riwayat negosiasi.</p>') +
                '</div></section>' +
                '<section><h3 class="text-[12px] font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2"><i class="ri-file-list-3-line text-emerald-500"></i> Hasil Proyek</h3>' +
                '<div class="space-y-3">' +
                (results.length ? results.map(function (r) {
                    return '<div class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl flex items-center justify-between"><div><p class="text-[12px] font-bold text-slate-800">' + escapeHtml(r.version || 'Versi') + '</p><p class="text-[10px] text-slate-500">' + new Date(r.created_at).toLocaleString('id-ID') + '</p></div><a href="/storage/' + r.file_url + '" target="_blank" class="px-3 py-1.5 bg-white text-[#0f766e] border border-[#0f766e] rounded-lg text-[10px] font-bold hover:bg-[#0f766e] hover:text-white transition-all">Unduh</a></div>';
                }).join('') : '<p class="text-slate-400 text-xs italic">Belum ada hasil yang dikirim.</p>') +
                '</div></section>' +
                '</div></div>' +
                '<div class="p-6 bg-slate-50 border-t border-slate-100 flex gap-3">' +
                '<button onclick="window.closeModal(\'modal-dispute-overlay\')" class="flex-1 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-100 transition-all">Tutup Detail</button>' +
                '<a href="/admin/orders" class="flex-1 py-3 bg-[#0f766e] text-white text-center font-bold rounded-xl text-sm hover:bg-[#0a5e58] transition-all shadow-lg shadow-teal-sm">Kelola di Halaman Pesanan</a>' +
                '</div>';

        } catch (error) {
            box.innerHTML = '<div class="p-12 text-center"><div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4"><i class="ri-error-warning-line text-3xl"></i></div><h3 class="text-lg font-bold text-slate-900 mb-2">Gagal Memuat Data</h3><p class="text-slate-500 text-sm mb-6">' + error.message + '</p><button onclick="window.closeModal(\'modal-dispute-overlay\')" class="px-8 py-2.5 bg-slate-900 text-white font-bold rounded-xl text-sm">Tutup</button></div>';
        }
    };

    window.openVerificationDetail = async function (id) {
        var overlay = document.getElementById('modal-verify-overlay');
        var box = document.getElementById('modal-verify-box');
        if (!overlay || !box) return;

        window.openModal('modal-verify-overlay');

        try {
            var response = await fetch('/admin/freelancers/' + id + '/detail', {
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Server error: ' + response.status);

            var data = await response.json();
            var student = data.skomda_student || {};

            box.innerHTML = '<div class="modal-header relative h-24 bg-gradient-to-r from-indigo-600 to-indigo-700 flex items-center px-8">' +
                '<div class="flex-1"><h2 class="text-white font-extrabold text-xl tracking-tight">Detail Verifikasi</h2><p class="text-white/80 text-[11px] font-bold uppercase tracking-wider">ID Freelancer: #FREELANCER-' + data.id + '</p></div>' +
                '<button onclick="window.closeModal(\'modal-verify-overlay\')" class="w-10 h-10 bg-white/20 text-white rounded-full flex items-center justify-center hover:bg-white/30 transition"><i class="ri-close-line text-xl"></i></button>' +
                '</div>' +
                '<div class="p-8">' +
                '<div class="flex items-center gap-5 mb-8">' +
                '<img src="https://ui-avatars.com/api/?name=' + encodeURIComponent(student.name || 'F') + '&background=4f46e5&color=fff&size=128" class="w-20 h-20 rounded-[22px] border-4 border-white shadow-lg" />' +
                '<div><h3 class="text-[1.3rem] font-black text-slate-900 leading-tight">' + escapeHtml(student.name || 'Tidak tersedia') + '</h3><p class="text-[13px] font-bold text-indigo-600 mt-1 uppercase tracking-wide">' + escapeHtml(student.major || 'Program Studi') + '</p></div>' +
                '</div>' +
                '<div class="space-y-5 mb-8">' +
                '<div class="grid grid-cols-2 gap-4">' +
                '<div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100"><span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">NIS</span><span class="text-[13.5px] font-extrabold text-slate-700 font-mono">' + escapeHtml(student.nis || '-') + '</span></div>' +
                '<div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100"><span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kelas</span><span class="text-[13.5px] font-extrabold text-slate-700">' + escapeHtml(student.class || '-') + '</span></div>' +
                '</div>' +
                '<div class="grid grid-cols-2 gap-4">' +
                '<div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100"><span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Layanan</span><span class="text-[13.5px] font-extrabold text-[#0f766e]">' + (data.services_count || 0) + ' Terdaftar</span></div>' +
                '<div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100"><span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Portofolio</span><span class="text-[13.5px] font-extrabold text-blue-600">' + (data.portofolios_count || 0) + ' Karya</span></div>' +
                '</div>' +
                '<div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100"><span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Email Sekolah</span><span class="text-[13.5px] font-bold text-slate-700">' + escapeHtml(student.email || '-') + '</span></div>' +
                '<div class="bg-slate-50 p-4 rounded-2xl border border-slate-100"><span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tentang Freelancer (Bio)</span><p class="text-[13px] text-slate-600 leading-relaxed">' + escapeHtml(data.bio || 'Tidak ada bio tertulis.') + '</p></div>' +
                '</div>' +
                '<div class="flex gap-3 pt-4">' +
                '<button onclick="handleReject(' + data.id + ')" class="flex-1 py-3.5 bg-red-50 text-red-600 font-bold rounded-xl text-sm hover:bg-red-600 hover:text-white transition-all">Tolak</button>' +
                '<button onclick="handleApprove(' + data.id + ')" class="flex-1 py-3.5 bg-emerald-600 text-white font-bold rounded-xl text-sm hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-sm">Setujui Akun</button>' +
                '</div>' +
                '</div>';

        } catch (error) {
            box.innerHTML = '<div class="p-12 text-center"><div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4"><i class="ri-error-warning-line text-3xl"></i></div><h3 class="text-lg font-bold text-slate-900 mb-2">Gagal Memuat Data</h3><p class="text-slate-500 text-sm mb-6">' + error.message + '</p><button onclick="window.closeModal(\'modal-verify-overlay\')" class="px-8 py-2.5 bg-slate-900 text-white font-bold rounded-xl text-sm">Tutup</button></div>';
        }
    };

    var chartInstance = null;
    var currentView = 'monthly';

    function initChart() {
        var ctx = document.getElementById('performanceChart');
        if (!ctx) return;
        if (typeof Chart === 'undefined') return;

        updateChart(ctx, currentView);
    }

    function updateChart(ctx, view) {
        var chartData = window.__DASHBOARD_CHART_DATA__ || {};
        var data = view === 'weekly' ? (chartData.weekly || []) : (chartData.monthly || []);
        var labels = [];
        var totals = [];

        if (view === 'weekly' && data.length > 0) {
            labels = data.map(function (d) { return d.week_label || (d.period_start && d.period_end ? d.period_start + ' - ' + d.period_end : 'Minggu'); });
            totals = data.map(function (d) { return parseFloat(d.total); });
        } else if (view === 'monthly' && data.length > 0) {
            labels = data.map(function (d) { return d.label || (d.month && d.year ? d.month + '/' + d.year : 'Bulan'); });
            totals = data.map(function (d) { return parseFloat(d.total); });
        }

        if (labels.length === 0) {
            var now = new Date();
            var periods = view === 'weekly' ? 12 : 6;
            for (var i = periods - 1; i >= 0; i--) {
                if (view === 'weekly') {
                    var start = new Date(now.getFullYear(), now.getMonth(), now.getDate() - (i * 7));
                    var end = new Date(start.getFullYear(), start.getMonth(), start.getDate() + 6);
                    labels.push(start.getDate() + ' ' + start.toLocaleString('id-ID', { month: 'long' }) + ' - ' + end.getDate() + ' ' + end.toLocaleString('id-ID', { month: 'long' }));
                } else {
                    var monthDate = new Date(now.getFullYear(), now.getMonth() - i, 1);
                    labels.push(monthDate.toLocaleString('id-ID', { month: 'long' }) + ' ' + monthDate.getFullYear());
                }
                totals.push(0);
            }
        }

        if (typeof Chart !== 'undefined' && Chart.getChart) {
            var existingChart = Chart.getChart(ctx);
            if (existingChart) {
                existingChart.destroy();
            }
        }

        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: view === 'weekly' ? 'Revenue Mingguan' : 'Revenue Bulanan',
                    data: totals,
                    borderColor: '#0f766e',
                    backgroundColor: function(context) {
                        var chart = context.chart;
                        var {ctx, chartArea} = chart;
                        if (!chartArea) return 'rgba(15, 118, 110, 0.05)';
                        var gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(15, 118, 110, 0)');
                        gradient.addColorStop(1, 'rgba(15, 118, 110, 0.15)');
                        return gradient;
                    },
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0f766e',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.35,
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
                            label: function (context) {
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
                            callback: function (value) {
                                if (value >= 1000000) return 'Rp' + (value / 1000000) + 'jt';
                                if (value >= 1000) return 'Rp' + (value / 1000) + 'rb';
                                return 'Rp' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { size: 10, family: 'Plus Jakarta Sans', weight: 'bold' },
                            color: '#64748b',
                            autoSkip: true,
                            maxTicksLimit: 8,
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            }
        });
    }

    window.switchChartView = function(view) {
        currentView = view;
        var ctx = document.getElementById('performanceChart');
        var weeklyBtn = document.getElementById('chart-weekly-btn');
        var monthlyBtn = document.getElementById('chart-monthly-btn');

        if (view === 'weekly') {
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

        if (ctx) {
            updateChart(ctx, view);
        }
    };

    function init() {
        initAdminDashboard();
        setTimeout(initChart, 100);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.handleApprove = handleApprove;
    window.handleReject = handleReject;
})();
