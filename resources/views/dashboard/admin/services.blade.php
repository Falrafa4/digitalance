@extends('layouts.dashboard')
@section('title', 'Manajemen Layanan | Digitalance')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/services.css') }}">
    <style>
        .service-card {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px -10px rgba(15, 118, 110, 0.15);
            border-color: #0f766e;
        }
    </style>
@endsection

@section('content')
    <x-crud-header title="Services Management" subtitle="Kelola dan pantau seluruh layanan yang ditawarkan oleh freelancer."
        count="{{ $services->total() }}" countLabel="Total Layanan" />

    <div class="flex items-center justify-between gap-4 mb-8 flex-wrap animate-fadeUp-2">
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.services.index', array_merge(request()->query(), ['status' => ''])) }}"
                class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ !request('status') ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                Semua
            </a>
            <a href="{{ route('admin.services.index', array_merge(request()->query(), ['status' => 'Pending'])) }}"
                class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ request('status') == 'Pending' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                Pending
            </a>
            <a href="{{ route('admin.services.index', array_merge(request()->query(), ['status' => 'Approved'])) }}"
                class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ request('status') == 'Approved' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                Disetujui
            </a>
            <a href="{{ route('admin.services.index', array_merge(request()->query(), ['status' => 'Rejected'])) }}"
                class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ request('status') == 'Rejected' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                Rejected
            </a>
            <a href="{{ route('admin.services.index', array_merge(request()->query(), ['status' => 'Draft'])) }}"
                class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ request('status') == 'Draft' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                Draft
            </a>
        </div>

        <form action="{{ route('admin.services.index') }}" method="GET" class="relative">
            <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[15px]"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari layanan, freelancer..."
                class="pl-10 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] transition-all" />
        </form>
    </div>

    @if($services->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fadeUp-3">
            @foreach($services as $s)
                <div class="service-card bg-white border border-slate-200 rounded-xl p-6 flex flex-col"
                    onclick="window.openServiceDetail({{ $s->id }})">
                    <div class="flex justify-between items-start mb-5">
                        <div
                            class="w-12 h-12 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 text-xl border border-slate-100">
                            <i class="ri-tools-line"></i>
                        </div>
                        <x-ui.status-badge :status="$s->status" />
                    </div>

                    <h3 class="font-display font-black text-slate-900 text-lg mb-2 leading-tight truncate">{{ $s->title }}
                    </h3>

                    <div class="flex items-center gap-2 mb-6">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($s->freelancer->skomda_student->name ?? $s->freelancer->skomda_student->email ?? 'User') }}&background=0f766e&color=fff"
                            class="w-6 h-6 rounded-lg" />
                        <span
                            class="text-xs font-semibold text-slate-500">{{ $s->freelancer->skomda_student->name ?? 'Freelancer' }}</span>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 mb-6 flex-1">
                        <p class="text-slate-600 text-sm leading-relaxed line-clamp-2">{{ $s->description }}</p>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100 mt-auto">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Harga Mulai</p>
                            <p class="text-[#0f766e] font-bold text-lg">Rp{{ number_format($s->price_min ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <button
                            class="w-10 h-10 rounded-lg bg-[#f0fdfa] text-[#0f766e] flex items-center justify-center hover:bg-[#0f766e] hover:text-white transition-all">
                            <i class="ri-arrow-right-line font-bold"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 flex justify-center pagination-container">
            {{ $services->links() }}
        </div>
    @else
        <x-ui.empty-state icon="ri-tools-line" title="Tidak Ada Layanan"
            description="Tidak ada layanan yang sesuai kriteria pencarian." />
    @endif
@endsection

@section('modals')
    <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300"
        id="modal-service-overlay">
        <div class="bg-white rounded-[32px] w-full max-w-[600px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300"
            id="modal-service-box">
            <!-- Content via JS -->
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.__SERVICES_PAGE__ = {
            data: @json($services instanceof \Illuminate\Pagination\LengthAwarePaginator ? $services->items() : $services)
        };
        let serviceModalOutsideClickHandler = null;

        function bindServiceModalOutsideClick(overlay) {
            if (!overlay) return;
            if (serviceModalOutsideClickHandler) {
                overlay.removeEventListener('click', serviceModalOutsideClickHandler);
            }
            serviceModalOutsideClickHandler = (e) => {
                if (e.target === e.currentTarget) window.closeServiceDetail();
            };
            overlay.addEventListener('click', serviceModalOutsideClickHandler);
        }

        function unbindServiceModalOutsideClick() {
            const overlay = document.getElementById('modal-service-overlay');
            if (!overlay || !serviceModalOutsideClickHandler) return;
            overlay.removeEventListener('click', serviceModalOutsideClickHandler);
            serviceModalOutsideClickHandler = null;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function renderInlineAlert({ type = 'error', title = '', message = '', icon = '' }) {
            const styles = {
                success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
                warning: 'bg-amber-50 border-amber-200 text-amber-800',
                info: 'bg-blue-50 border-blue-200 text-blue-800',
                error: 'bg-rose-50 border-rose-200 text-rose-800',
            };

            const icons = {
                success: 'ri-checkbox-circle-fill text-emerald-500',
                warning: 'ri-alert-fill text-amber-500',
                info: 'ri-information-fill text-blue-500',
                error: 'ri-error-warning-fill text-rose-500',
            };

            const resolvedType = styles[type] ? type : 'error';
            const resolvedIcon = icon || icons[resolvedType];

            return `
                    <div role="alert" class="rounded-xl border px-4 py-3 flex items-start gap-3 ${styles[resolvedType]}">
                        <i class="ri-xl ${resolvedIcon} flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1 text-sm font-semibold">
                            ${title ? `<p class="font-extrabold mb-0.5">${escapeHtml(title)}</p>` : ''}
                            <span>${escapeHtml(message)}</span>
                        </div>
                    </div>
                `;
        }

        function askRejectReason(serviceId) {
            return new Promise((resolve) => {
                const overlay = document.createElement('div');
                overlay.className = 'fixed inset-0 z-[200] bg-slate-900/45 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-200';

                const box = document.createElement('div');
                box.className = 'w-full max-w-[460px] bg-white rounded-[24px] shadow-2xl border border-slate-200 overflow-hidden transform scale-95 transition-transform duration-200';

                box.innerHTML = `
                        <div class="px-6 py-5 bg-gradient-to-r from-rose-500 to-rose-600 text-white">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-1">Tinjau Layanan</p>
                            <h3 class="font-display text-[1.1rem] font-extrabold leading-tight">Tolak Layanan #SRV-${serviceId}</h3>
                        </div>
                        <form id="reject-service-form" class="p-6">
                            <label for="reject-reason-input" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Alasan Penolakan</label>
                            <textarea id="reject-reason-input" rows="4" maxlength="500" placeholder="Tuliskan alasan penolakan layanan agar freelancer bisa memperbaiki..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-[13px] text-slate-700 font-medium leading-relaxed outline-none focus:border-rose-300 focus:ring-2 focus:ring-rose-100 transition"></textarea>
                            <p class="text-[11px] text-slate-400 mt-2">Alasan wajib diisi dan akan dikirim ke freelancer.</p>
                            <div class="flex gap-3 mt-5">
                                <button type="button" id="btn-reject-cancel" class="flex-1 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-[13px] hover:bg-slate-200 transition">Batal</button>
                                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-rose-600 text-white font-bold text-[13px] hover:bg-rose-700 transition shadow-lg shadow-rose-100">
                                    <i class="ri-close-circle-line mr-1"></i>Kirim Penolakan
                                </button>
                            </div>
                        </form>
                    `;

                overlay.appendChild(box);
                document.body.appendChild(overlay);

                const textarea = box.querySelector('#reject-reason-input');
                const form = box.querySelector('#reject-service-form');
                const cancelBtn = box.querySelector('#btn-reject-cancel');

                let isClosed = false;
                const close = (value) => {
                    if (isClosed) return;
                    isClosed = true;
                    overlay.classList.add('opacity-0');
                    box.classList.remove('scale-100');
                    box.classList.add('scale-95');
                    setTimeout(() => {
                        overlay.remove();
                        resolve(value);
                    }, 180);
                };

                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) close(null);
                });

                cancelBtn.addEventListener('click', () => close(null));

                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const reason = textarea.value.trim();
                    if (!reason) {
                        window.showToast?.('Alasan penolakan wajib diisi.', 'warning');
                        textarea.focus();
                        return;
                    }
                    close(reason);
                });

                requestAnimationFrame(() => {
                    overlay.classList.remove('opacity-0');
                    box.classList.remove('scale-95');
                    box.classList.add('scale-100');
                    textarea.focus();
                });
            });
        }

        window.openServiceDetail = function (id) {
            const s = window.__SERVICES_PAGE__.data.find(x => x.id == id);
            if (!s) {
                return;
            }

            const box = document.getElementById('modal-service-box');
            const overlay = document.getElementById('modal-service-overlay');
            if (!box || !overlay) return;

            let actionButtons = '';
            let rejectionInfo = '';

            if (s.status === 'Draft') {
                actionButtons = renderInlineAlert({
                    type: 'warning',
                    title: 'Status Draft',
                    message: 'Service ini masih berstatus Draft. Tunggu freelancer mengajukan ke admin untuk direview.',
                    icon: 'ri-information-line',
                });
            } else if (s.status === 'Pending') {
                actionButtons = `
                                <div class="flex gap-3">
                                    <button onclick="window.updateServiceStatus(${s.id}, 'Rejected')" class="flex-1 py-4 bg-red-50 text-red-600 font-bold rounded-2xl text-[13px] hover:bg-red-600 hover:text-white transition-all">Tolak</button>
                                    <button onclick="window.confirmApprove(${s.id})" class="flex-1 py-4 bg-[#0f766e] text-white font-bold rounded-2xl text-[13px] hover:bg-[#0a5e58] transition-all shadow-lg shadow-teal-sm">Setujui</button>
                                </div>
                            `;
            } else if (s.status === 'Rejected') {
                rejectionInfo = `
                                ${renderInlineAlert({
                    type: 'error',
                    title: 'Layanan Ditolak',
                    message: 'Layanan ini ditolak dan dikembalikan ke freelancer.',
                    icon: 'ri-close-circle-line',
                })}
                                <div class="mt-4 rounded-xl border border-rose-100 bg-white/80 px-4 py-3 mb-6">
                                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-wider mb-2">Alasan Penolakan</p>
                                    <p class="text-[13px] text-slate-700 font-medium leading-relaxed">${escapeHtml(s.reject_reason || 'Tidak ada alasan.')}</p>
                                </div>
                            `;
                actionButtons = `
                                <div class="flex gap-3">
                                    <button onclick="window.updateServiceStatus(${s.id}, 'Rejected')" class="flex-1 py-4 bg-orange-50 text-orange-600 font-bold rounded-2xl text-[13px] hover:bg-orange-600 hover:text-white transition-all"><i class="ri-refresh-line"></i> Reject Ulang</button>
                                    <button onclick="window.updateServiceStatus(${s.id}, 'Approved')" class="flex-1 py-4 bg-[#0f766e] text-white font-bold rounded-2xl text-[13px] hover:bg-[#0a5e58] transition-all shadow-lg shadow-teal-sm">Setujui</button>
                                </div>
                            `;
            } else if (s.status === 'Approved') {
                actionButtons = renderInlineAlert({
                    type: 'success',
                    title: 'Layanan Disetujui',
                    message: 'Service ini sudah disetujui dan ditampilkan ke publik.',
                    icon: 'ri-checkbox-circle-line',
                });
            }

            box.innerHTML = `
                            <div class="relative">
                                <!-- Gradient Header -->
                                <div class="h-28 bg-gradient-to-r from-[#0f766e] to-[#10b981] flex items-center px-8 relative">
                                    <div class="flex-1">
                                        <h2 class="text-white font-black text-xl tracking-tight">Detail Layanan</h2>
                                        <p class="text-white/70 text-[10px] font-bold uppercase tracking-[0.2em]">ID Layanan: #SRV-${s.id}</p>
                                    </div>
                                    <button onclick="window.closeServiceDetail()" class="w-10 h-10 bg-white/20 text-white rounded-full flex items-center justify-center hover:bg-white/30 transition">
                                        <i class="ri-close-line text-xl"></i>
                                    </button>
                                </div>

                                <!-- Content -->
                                <div class="px-8 pb-8 -mt-8 relative z-10">
                                    <div class="bg-white rounded-2xl p-6 shadow-xl border border-slate-50 mb-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <h3 class="text-[1.5rem] font-black text-slate-900 leading-tight flex-1 pr-4">${s.title}</h3>
                                            <span class="px-3 py-1 bg-teal-50 text-[#0f766e] text-[10px] font-black rounded-lg uppercase tracking-wider border border-teal-100 shadow-sm">${s.status}</span>
                                        </div>
                                        <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                            <span class="text-[13px] font-bold text-slate-500">Harga Mulai</span>
                                            <span class="text-[1.5rem] font-black text-[#0f766e]">Rp${Number(s.price_min || s.base_price).toLocaleString('id-ID')}</span>
                                        </div>
                                    </div>

                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 mb-6">
        <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Penyedia Layanan</span>
                                        <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Deskripsi Layanan</span>
                                        <p class="text-[13px] text-slate-600 leading-relaxed font-medium max-h-[120px] overflow-y-auto pr-2 custom-scrollbar">${s.description || 'Tidak ada deskripsi.'}</p>
                                        </div>
                                    </div>

                                    ${rejectionInfo}
                                    ${actionButtons}
                                </div>
                            </div>
                        `;

            overlay.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
            bindServiceModalOutsideClick(overlay);
        };

        window.closeServiceDetail = function () {
            const overlay = document.getElementById('modal-service-overlay');
            const box = document.getElementById('modal-service-box');
            if (overlay) {
                overlay.classList.add('opacity-0', 'pointer-events-none');
            }
            if (box) {
                box.classList.add('scale-95');
            }
            unbindServiceModalOutsideClick();
        };

        window.confirmApprove = async function (id) {
            if (!(await customConfirm('Yakin ingin menyetujui layanan ini?\nLayanan akan langsung ditampilkan ke publik.'))) return;
            window.updateServiceStatus(id, 'Approved');
        };

        window.updateServiceStatus = async function (id, status, event) {
            const box = document.getElementById('modal-service-box');
            var actionBtn = event ? event.target : box.querySelector('button:not([disabled])');

            try {
                if (status === 'Rejected') {
                    const trimmedReason = await askRejectReason(id);
                    if (trimmedReason === null) return;
                    const bodyData = { status: status, reject_reason: trimmedReason };

                    if (actionBtn) {
                        actionBtn.disabled = true;
                        actionBtn.classList.add('btn-loading');
                    }

                    const response = await fetch(`/admin/services/${id}/status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(bodyData)
                    });

                    if (!response.ok) throw new Error('Gagal memperbarui status');

                    if (status === 'Approved') {
                        window.showToast?.('Layanan berhasil disetujui dan ditampilkan ke publik.', 'success');
                    } else if (status === 'Rejected') {
                        window.showToast?.('Layanan berhasil ditolak. Freelancer akan menerima notifikasi.', 'success');
                    } else {
                        window.showToast?.('Status layanan berhasil diperbarui.', 'success');
                    }
                    window.location.reload();
                    return;
                }

                if (actionBtn) {
                    actionBtn.disabled = true;
                    actionBtn.classList.add('btn-loading');
                }

                const bodyData = { status: status };

                const response = await fetch(`/admin/services/${id}/status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(bodyData)
                });

                if (!response.ok) throw new Error('Gagal memperbarui status');

                window.showToast?.('Status berhasil diperbarui!', 'success');
                window.location.reload();
            } catch (e) {
                if (actionBtn) {
                    actionBtn.disabled = false;
                    actionBtn.classList.remove('btn-loading');
                }
                if (window.showToast) {
                    window.showToast(e.message || 'Terjadi kesalahan.', 'danger');
                } else {
                    alert(e.message);
                }
            }
        };

    </script>
@endsection