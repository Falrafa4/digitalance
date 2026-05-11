@extends('layouts.dashboard')
@section('title', 'Service Management | Digitalance')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/services.css') }}">
    <style>
        .service-card { cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .service-card:hover { transform: translateY(-4px); box-shadow: 0 15px 30px -10px rgba(15,118,110,0.15); border-color: #0f766e; }
        .status-pill { padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
    </style>
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Services Management</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Kelola dan pantau seluruh layanan yang ditawarkan oleh freelancer.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="bg-white px-5 py-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#eef2ff] text-[#6366f1] flex items-center justify-center text-lg shadow-sm">
                    <i class="ri-tools-line"></i>
                </div>
                <div>
                    <div class="text-[1.2rem] font-black text-slate-900 leading-none">{{ $services->total() }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Layanan</div>
                </div>
            </div>
        </div>
    </div>

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
                <div class="service-card bg-white border border-slate-200 rounded-[24px] p-6 flex flex-col" onclick="window.openServiceDetail({{ $s->id }})">
                    <div class="flex justify-between items-start mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 text-xl border border-slate-100">
                             <i class="ri-tools-line"></i>
                        </div>
                        <span class="status-pill status-{{ strtolower($s->status) }}">{{ $s->status }}</span>
                    </div>

                    <h3 class="font-display font-black text-slate-900 text-[1.05rem] mb-2 leading-tight truncate">{{ $s->title }}</h3>
                    
                    <div class="flex items-center gap-2 mb-6">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($s->freelancer->skomda_student->name ?? 'F') }}&background=0f766e&color=fff" class="w-6 h-6 rounded-lg" />
                        <span class="text-[12px] font-bold text-slate-500">{{ $s->freelancer->skomda_student->name ?? 'Freelancer' }}</span>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-6 flex-1">
                        <p class="text-slate-600 text-[12.5px] leading-relaxed line-clamp-2">{{ $s->description }}</p>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-50 mt-auto">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Harga Mulai</p>
                            <p class="text-[#0f766e] font-black text-[1.1rem]">Rp{{ number_format($s->base_price, 0, ',', '.') }}</p>
                        </div>
                        <button class="w-10 h-10 rounded-xl bg-[#f0fdfa] text-[#0f766e] flex items-center justify-center hover:bg-[#0f766e] hover:text-white transition-all">
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
        <div class="py-24 text-center animate-fadeUp-3">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5 text-[2.5rem] text-slate-300">
                <i class="ri-tools-line"></i>
            </div>
            <h3 class="text-[1.2rem] font-extrabold text-slate-900">Belum Ada Layanan</h3>
            <p class="text-slate-500 max-w-[320px] mx-auto mt-2">Tidak ditemukan layanan yang sesuai dengan kriteria Anda.</p>
        </div>
    @endif
@endsection

@section('modals')
    <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-service-overlay">
        <div class="bg-white rounded-[28px] w-full max-w-[500px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-service-box">
             <!-- Content via JS -->
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.__SERVICES_DATA__ = @json($services instanceof \Illuminate\Pagination\LengthAwarePaginator ? $services->items() : $services);
        
        window.openServiceDetail = function(id) {
            const s = window.__SERVICES_DATA__.find(x => x.id == id);
            if (!s) {
                console.error('Service not found:', id);
                return;
            }

            const box = document.getElementById('modal-service-box');
            const overlay = document.getElementById('modal-service-overlay');
            if(!box || !overlay) return;

            box.innerHTML = `
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-16 h-16 rounded-[22px] bg-[#f0fdfa] text-[#0f766e] flex items-center justify-center text-[2rem] shadow-sm border border-[#ccfbf1]">
                             <i class="ri-tools-line"></i>
                        </div>
                        <button onclick="window.closeServiceDetail()" class="w-9 h-9 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>

                    <h2 class="text-[1.5rem] font-black text-slate-900 mb-2 leading-tight">${s.title}</h2>
                    <div class="flex items-center gap-2 mb-6">
                        <span class="status-pill status-${s.status.toLowerCase()}">${s.status}</span>
                        <span class="text-[12px] font-bold text-slate-400 font-mono">#SRV-${s.id}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                             <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Harga Dasar</p>
                             <p class="text-slate-900 font-black text-lg">Rp${Number(s.base_price).toLocaleString('id-ID')}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                             <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Freelancer</p>
                             <p class="text-slate-900 font-bold text-[13.5px] truncate">${s.freelancer?.skomda_student?.name || 'N/A'}</p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Deskripsi Layanan</p>
                        <p class="text-[13px] text-slate-600 leading-relaxed max-h-32 overflow-y-auto pr-2">${s.description || 'Tidak ada deskripsi.'}</p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="flex gap-3">
                             <button onclick="window.updateServiceStatus(${s.id}, 'Rejected')" class="flex-1 py-3.5 bg-red-50 text-red-600 font-bold rounded-xl text-sm hover:bg-red-600 hover:text-white transition-all">Reject</button>
                             <button onclick="window.updateServiceStatus(${s.id}, 'Approved')" class="flex-1 py-3.5 bg-[#0f766e] text-white font-bold rounded-xl text-sm hover:bg-[#0a5e58] transition-all shadow-lg shadow-teal-sm">Approve Service</button>
                        </div>
                        <button onclick="window.closeServiceDetail()" class="py-3 bg-slate-100 text-slate-500 font-bold rounded-xl text-sm hover:bg-slate-200 transition-all">Close</button>
                    </div>
                </div>
            `;

            overlay.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
        };

        window.closeServiceDetail = function() {
            const overlay = document.getElementById('modal-service-overlay');
            const box = document.getElementById('modal-service-box');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            box.classList.add('scale-95');
        };

        window.updateServiceStatus = async function(id, status) {
            try {
                const response = await fetch(`/admin/services/${id}/status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: status })
                });
                
                if(!response.ok) throw new Error('Gagal memperbarui status');
                
                window.location.reload();
            } catch(e) {
                alert(e.message);
            }
        };

        document.getElementById('modal-service-overlay').onclick = (e) => {
            if(e.target === e.currentTarget) window.closeServiceDetail();
        };
    </script>
@endsection
