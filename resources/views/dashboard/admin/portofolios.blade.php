@extends('layouts.dashboard')
@section('title', 'Portfolios Management | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/portofolios.css') }}">
    <style>
        .port-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .port-card:hover { transform: translateY(-8px); }
        .port-card:hover .port-overlay { opacity: 1; transform: translateY(0); }
        .port-overlay { transform: translateY(10px); transition: all 0.3s ease; }
    </style>
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Portfolios</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Review dan kelola karya-karya terbaik dari freelancer kami.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="bg-white px-5 py-3 rounded-2xl border border-slate-100 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shadow-sm">
                    <i class="ri-gallery-line"></i>
                </div>
                <div>
                    <div class="text-[1.2rem] font-black text-slate-900 leading-none">{{ $portofolios->total() }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Works</div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between gap-4 mb-10 flex-wrap animate-fadeUp-2">
        <form action="{{ route('admin.portofolios.index') }}" method="GET" class="flex gap-2 flex-wrap items-center">
            <div class="relative">
                <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[15px]"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title or freelancer..." 
                       class="pl-10 pr-4 py-[9px] w-[280px] border-[1.5px] border-slate-200 rounded-[14px] text-[13px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] transition-all" />
            </div>
            @if(request('q'))
                <a href="{{ route('admin.portofolios.index') }}" class="px-4 py-2 text-slate-400 hover:text-red-500 text-xs font-black uppercase tracking-widest transition-all">Clear Search</a>
            @endif
        </form>
    </div>

    @if($portofolios->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-7 animate-fadeUp-3">
            @foreach($portofolios as $p)
                <div class="port-card bg-white rounded-[24px] border border-slate-100 overflow-hidden shadow-sm hover:shadow-xl hover:border-[#0f766e]/30 group cursor-pointer" onclick="window.openPortDetail({{ $p->id }})">
                    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                        <img src="{{ $p->media_url ? asset('storage/' . $p->media_url) : 'https://placehold.co/800x600?text=Digitalance' }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-5">
                            <div class="port-overlay opacity-0 w-full">
                                <span class="block text-white text-[13px] font-bold mb-1"><i class="ri-eye-line mr-1"></i> View Work Details</span>
                                <div class="flex items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(p.service?.freelancer?.skomda_student?.name || 'F')}&background=0f766e&color=fff" class="w-5 h-5 rounded-full" />
                                    <span class="text-white/80 text-[11px] font-medium">{{ $p->service->freelancer->skomda_student->name ?? 'Freelancer' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[10px] font-black text-[#0f766e] bg-[#f0fdfa] px-2 py-0.5 rounded-lg uppercase tracking-wider">#PORT-{{ $p->id }}</span>
                            <span class="text-[10px] font-bold text-slate-400 truncate flex-1">{{ $p->service->title ?? 'General' }}</span>
                        </div>
                        <h3 class="text-[14.5px] font-black text-slate-900 mb-2 truncate leading-tight">{{ $p->title }}</h3>
                        <p class="text-[12px] text-slate-500 line-clamp-2 leading-relaxed mb-4">{{ $p->description }}</p>
                        <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                            <div class="flex items-center gap-2">
                                <i class="ri-calendar-line text-slate-300 text-xs"></i>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $p->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="w-7 h-7 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-[#0f766e] group-hover:text-white transition-all">
                                <i class="ri-arrow-right-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 pagination-container">
            {{ $portofolios->links() }}
        </div>
    @else
        <div class="py-32 text-center animate-fadeUp-3">
            <div class="w-24 h-24 bg-slate-50 rounded-[32px] flex items-center justify-center mx-auto mb-6 text-[2.8rem] text-slate-200">
                <i class="ri-gallery-line"></i>
            </div>
            <h3 class="text-[1.4rem] font-black text-slate-900">No Portfolios Yet</h3>
            <p class="text-slate-500 max-w-[350px] mx-auto mt-2 font-medium">Belum ada karya yang diunggah oleh para freelancer kami.</p>
        </div>
    @endif
@endsection

@section('modals')
    <div class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-port-overlay">
        <div class="bg-white rounded-[32px] w-full max-w-[500px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300" id="modal-port-box">
             <!-- Content via JS -->
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.__PORTOFOLIOS_DATA__ = @json($portofolios->items());
        
        window.openPortDetail = function(id) {
            const p = window.__PORTOFOLIOS_DATA__.find(x => x.id == id);
            if (!p) return;

            const box = document.getElementById('modal-port-box');
            const overlay = document.getElementById('modal-port-overlay');
            
            const imageUrl = p.media_url ? `/storage/${p.media_url}` : 'https://placehold.co/800x600?text=Digitalance';

            box.innerHTML = `
                <div class="relative aspect-video bg-slate-100">
                    <img src="${imageUrl}" class="w-full h-full object-cover">
                    <button onclick="window.closePortDetail()" class="absolute top-5 right-5 w-10 h-10 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/70 backdrop-blur-md transition">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>
                <div class="p-8">
                    <div class="flex items-center gap-3 mb-4">
                         <span class="text-[11px] font-black text-[#0f766e] bg-[#f0fdfa] px-2.5 py-1 rounded-lg uppercase tracking-wider">Portfolio #ORD-${p.id}</span>
                         <span class="text-[11px] font-bold text-slate-400">Published on ${new Date(p.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'})}</span>
                    </div>
                    <h2 class="text-[1.6rem] font-black text-slate-900 mb-6 leading-tight">${p.title}</h2>
                    
                    <div class="p-5 bg-slate-50 rounded-[24px] border border-slate-100 mb-8">
                        <div class="flex items-center gap-4">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(p.service?.freelancer?.skomda_student?.name || 'F')}&background=0f766e&color=fff" class="w-12 h-12 rounded-[14px]" />
                            <div>
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Freelancer Account</p>
                                <p class="text-[14px] font-black text-slate-800">${p.service?.freelancer?.skomda_student?.name || 'N/A'}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-10">
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3">Project Description</h4>
                        <p class="text-[13.5px] text-slate-600 leading-relaxed font-medium">${p.description || 'No description provided.'}</p>
                    </div>

                    <div class="flex gap-4">
                        <button onclick="window.confirmDeletePort(${p.id})" class="flex-1 py-4 bg-red-50 text-red-500 font-bold rounded-2xl text-[13px] hover:bg-red-500 hover:text-white transition-all">Delete Portfolio</button>
                        <button onclick="window.closePortDetail()" class="flex-1 py-4 bg-slate-900 text-white font-bold rounded-2xl text-[13px] hover:bg-slate-800 transition-all shadow-xl">Close Preview</button>
                    </div>
                </div>
            `;

            overlay.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
        };

        window.closePortDetail = function() {
            const overlay = document.getElementById('modal-port-overlay');
            const box = document.getElementById('modal-port-box');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            box.classList.add('scale-95');
        };

        window.confirmDeletePort = async function(id) {
            if(!confirm('Yakin ingin menghapus portofolio ini secara permanen?')) return;
            
            try {
                const response = await fetch(`/admin/portofolios/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if(!response.ok) throw new Error('Failed to delete portfolio.');
                
                window.location.reload();
            } catch(e) {
                alert(e.message);
            }
        };

        document.getElementById('modal-port-overlay').onclick = (e) => {
            if(e.target === e.currentTarget) window.closePortDetail();
        };
    </script>
@endsection