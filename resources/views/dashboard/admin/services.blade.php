@extends('layouts.dashboard')
@section('title', 'Service Management | Digitalance')

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
                Approved
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
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($s->freelancer->skomda_student->name ?? 'F') }}&background=0f766e&color=fff"
                            class="w-6 h-6 rounded-lg" />
                        <span
                            class="text-xs font-semibold text-slate-500">{{ $s->freelancer->skomda_student->name ?? 'Freelancer' }}</span>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 mb-6 flex-1">
                        <p class="text-slate-600 text-sm leading-relaxed line-clamp-2">{{ $s->description }}</p>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100 mt-auto">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Starting Price</p>
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
        <x-ui.empty-state icon="ri-tools-line" title="No Services Found"
            description="No services match your search criteria." />
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
        window.__SERVICES_DATA__ = @json($services instanceof \Illuminate\Pagination\LengthAwarePaginator ? $services->items() : $services);
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

        window.openServiceDetail = function (id) {
            const s = window.__SERVICES_DATA__.find(x => x.id == id);
            if (!s) {
                return;
            }

            const box = document.getElementById('modal-service-box');
            const overlay = document.getElementById('modal-service-overlay');
            if (!box || !overlay) return;

            let actionButtons = '';
            let rejectionInfo = '';

            if (s.status === 'Draft') {
                actionButtons = `
                        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
                            <p class="text-[11px] text-amber-700 font-bold leading-relaxed flex items-start gap-2">
                                <i class="ri-information-line mt-0.5"></i>
                                <span>Service ini masih berstatus Draft. Tunggu freelancer mengajukan ke admin untuk direview.</span>
                            </p>
                        </div>
                    `;
            } else if (s.status === 'Pending') {
                actionButtons = `
                        <div class="flex gap-3">
                            <button onclick="window.updateServiceStatus(${s.id}, 'Rejected')" class="flex-1 py-4 bg-red-50 text-red-600 font-bold rounded-2xl text-[13px] hover:bg-red-600 hover:text-white transition-all">Reject</button>
                            <button onclick="window.updateServiceStatus(${s.id}, 'Approved')" class="flex-1 py-4 bg-[#0f766e] text-white font-bold rounded-2xl text-[13px] hover:bg-[#0a5e58] transition-all shadow-lg shadow-teal-sm">Approve</button>
                        </div>
                    `;
            } else if (s.status === 'Rejected') {
                rejectionInfo = `
                        <div class="p-4 bg-red-50 rounded-2xl border border-red-100 mb-6">
                            <p class="text-[11px] text-red-700 font-bold leading-relaxed flex items-start gap-2">
                                <i class="ri-close-circle-line mt-0.5"></i>
                                <span>Service ini telah ditolak. ${s.reject_reason ? 'Alasan: ' + s.reject_reason : 'Freelancer perlu mengajukan ulang.'}</span>
                            </p>
                        </div>
                    `;
                actionButtons = `
                        <div class="flex gap-3">
                            <button onclick="window.updateServiceStatus(${s.id}, 'Rejected')" class="flex-1 py-4 bg-red-50 text-red-600 font-bold rounded-2xl text-[13px] hover:bg-red-600 hover:text-white transition-all">Reject Ulang</button>
                            <button onclick="window.updateServiceStatus(${s.id}, 'Approved')" class="flex-1 py-4 bg-[#0f766e] text-white font-bold rounded-2xl text-[13px] hover:bg-[#0a5e58] transition-all shadow-lg shadow-teal-sm">Approve</button>
                        </div>
                    `;
            } else if (s.status === 'Approved') {
                actionButtons = `
                        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                            <p class="text-[11px] text-emerald-700 font-bold leading-relaxed flex items-start gap-2">
                                <i class="ri-checkbox-circle-line mt-0.5"></i>
                                <span>Service ini sudah disetujui dan ditampilkan ke publik.</span>
                            </p>
                        </div>
                    `;
            }

            box.innerHTML = `
                    <div class="relative">
                        <!-- Gradient Header -->
                        <div class="h-28 bg-gradient-to-r from-[#0f766e] to-[#10b981] flex items-center px-8 relative">
                            <div class="flex-1">
                                <h2 class="text-white font-black text-xl tracking-tight">Service Details</h2>
                                <p class="text-white/70 text-[10px] font-bold uppercase tracking-[0.2em]">Service ID: #SRV-${s.id}</p>
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
                                    <span class="text-[13px] font-bold text-slate-500">Starting Price</span>
                                    <span class="text-[1.5rem] font-black text-[#0f766e]">Rp${Number(s.price_min || s.base_price).toLocaleString('id-ID')}</span>
                                </div>
                            </div>

                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 mb-6">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Service Provider</span>
                                <div class="flex items-center gap-4">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(s.freelancer?.skomda_student?.name || 'F')}&background=0f766e&color=fff" class="w-11 h-11 rounded-xl shadow-sm" />
                                    <div>
                                        <p class="text-[14px] font-black text-slate-800">${s.freelancer?.skomda_student?.name || 'N/A'}</p>
                                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-tight">${s.freelancer?.skomda_student?.major || 'Freelancer'}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Service Description</span>
                                <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-100/50">
                                    <p class="text-[13px] text-slate-600 leading-relaxed font-medium max-h-[120px] overflow-y-auto pr-2 custom-scrollbar">${s.description || 'No description provided.'}</p>
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

        window.updateServiceStatus = async function (id, status, event) {
            const box = document.getElementById('modal-service-box');
            var actionBtn = event ? event.target : box.querySelector('button:not([disabled])');

            try {
                if (status === 'Rejected') {
                    const reason = prompt('Masukkan alasan penolakan layanan:');
                    if (reason === null) return;
                    const trimmedReason = reason.trim();
                    if (!trimmedReason) {
                        window.showToast('Alasan penolakan wajib diisi.', 'warning');
                        return;
                    }
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

                    window.showToast?.('Status berhasil diperbarui!', 'success');
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