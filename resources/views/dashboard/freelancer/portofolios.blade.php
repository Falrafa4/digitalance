@extends('layouts.dashboard')
@section('title', 'My Portofolios | Digitalance')

@section('content')
<div class="animate-fadeUp flex-1 px-8 py-7 overflow-y-auto">
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900 leading-tight">My Portofolios</h1>
            <p class="text-slate-500 mt-1 text-[0.95rem]">Kelola karya dan hasil kerjamu sebagai portofolio.</p>
        </div>
        <button onclick="document.getElementById('modal-add').classList.add('open')" class="px-5 py-2.5 bg-[#0f766e] text-white rounded-[12px] font-bold text-[13.5px] hover:bg-[#0d6b63] transition-all shadow-teal-sm flex items-center gap-2">
            <i class="ri-add-line text-lg"></i> Tambah Portofolio
        </button>
    </div>

    @if($portofolios->isEmpty())
        <div class="text-center py-16 px-5 bg-white border-2 border-dashed border-slate-200 rounded-[20px]">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mx-auto mb-4">
                <i class="ri-folder-upload-line"></i>
            </div>
            <h3 class="font-display text-[1.15rem] font-bold text-slate-700 mb-1">Belum Ada Portofolio</h3>
            <p class="text-[13px] text-slate-400">Tunjukkan karya terbaikmu kepada klien dengan mengunggah portofolio.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 pb-6">
            @foreach($portofolios as $port)
                <div class="bg-white border border-slate-200 rounded-[20px] overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                    <div class="h-[180px] w-full relative bg-slate-100">
                        @if($port->media_url)
                            <img src="{{ asset('storage/' . $port->media_url) }}" alt="Portofolio" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/800x600?text=No+Image'">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="ri-image-2-line text-4xl"></i></div>
                        @endif
                        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                            <button onclick="openEditModal({{ $port->id }}, '{{ addslashes($port->title) }}', '{{ addslashes($port->description) }}', {{ $port->service_id }})" class="w-10 h-10 rounded-full bg-white text-slate-700 flex items-center justify-center hover:bg-emerald-50 hover:text-emerald-600 transition-colors">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <form action="{{ route('freelancer.portofolios.destroy', $port->id) }}" method="POST" onsubmit="return confirm('Hapus portofolio ini?')">
                                @csrf @method('DELETE')
                                <button class="w-10 h-10 rounded-full bg-white text-slate-700 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-colors">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="p-5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Service: {{ $port->service->title ?? '-' }}</span>
                        <h3 class="font-display font-bold text-[1.1rem] text-slate-900 mb-2 truncate" title="{{ $port->title }}">{{ $port->title }}</h3>
                        <p class="text-[13px] text-slate-500 line-clamp-2" title="{{ $port->description }}">{{ $port->description ?: 'Tidak ada deskripsi.' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Modal Add -->
<div id="modal-add" class="fixed inset-0 z-[100] flex items-center justify-center hidden opacity-0 transition-all duration-200">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="this.parentElement.classList.remove('open', 'opacity-100'); this.parentElement.classList.add('hidden')"></div>
    <div class="bg-white rounded-[24px] w-full max-w-lg p-6 relative shadow-2xl scale-95 transition-transform duration-200 mx-4">
        <h2 class="font-display font-extrabold text-[1.4rem] text-slate-900 mb-6">Tambah Portofolio</h2>
        <form action="{{ route('freelancer.portofolios.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Judul Portofolio</label>
                <input type="text" name="title" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[14px] focus:border-[#0f766e] focus:ring-2 focus:ring-[#0f766e]/20 outline-none">
            </div>
            <div>
                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Layanan / Service Terkait</label>
                <select name="service_id" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[14px] focus:border-[#0f766e] outline-none">
                    <option value="" disabled selected>Pilih Layanan</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}">{{ $svc->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[14px] focus:border-[#0f766e] outline-none resize-none"></textarea>
            </div>
            <!-- Untuk gambar, disesuaikan backend. Asumsikan name="media_url" bisa upload file jika controller dukung, tapi Request nya mungkin butuh file. Cek Dulu, kita biarkan text / file standar -->
            <div>
                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Gambar / Media (URL)</label>
                <input type="text" name="media_url" placeholder="Masukkan URL gambar atau tinggalkan kosong" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[14px] focus:border-[#0f766e] outline-none">
                <span class="text-xs text-slate-400 mt-1 block">Catatan: Ganti dengan input type="file" jika backend mendukung upload media.</span>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('modal-add').classList.remove('open', 'opacity-100'); document.getElementById('modal-add').classList.add('hidden')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#0f766e] text-white font-bold text-sm hover:bg-[#0d6b63] transition-colors shadow-teal-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Add simple script to handle modal visibility -->
<style>
    #modal-add.open { display: flex; opacity: 1; }
    #modal-add.open > div:last-child { transform: scale(1); }
</style>
<script>
    function openEditModal(id, title, desc, service_id) {
        alert('Fitur edit portofolio (ID: ' + id + ') belum sepenuhnya dibuatkan modal di Phase 3 ini. Silakan arahkan ke halaman edit atau buat modal edit serupa modal Add.');
    }
</script>
@endsection
