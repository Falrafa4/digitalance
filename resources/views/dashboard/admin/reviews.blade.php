@extends('layouts.dashboard')
@section('title', 'Review Management | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/reviews.css') }}">
    <style>
        .filter-tab-link { px-[18px] py-2 rounded-full border-[1.5px] border-slate-200 bg-white text-slate-500 font-bold text-[12.5px] transition-all; }
        .filter-tab-link.active { border-color: #0f766e; bg: #0f766e; color: white; shadow: 0 4px 12px rgba(15,118,110,0.2); }
    </style>
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Review Management</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Kelola ulasan dan penilaian yang diberikan oleh pengguna platform.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="bg-white px-5 py-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-lg shadow-sm">
                    <i class="ri-star-fill"></i>
                </div>
                <div>
                    <div class="text-[1.2rem] font-black text-slate-900 leading-none">{{ $reviews->total() }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Ulasan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between gap-4 mb-8 flex-wrap animate-fadeUp-2">
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.reviews.index', array_merge(request()->query(), ['rating' => ''])) }}" 
               class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ !request('rating') ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                Semua
            </a>
            <a href="{{ route('admin.reviews.index', array_merge(request()->query(), ['rating' => '5'])) }}" 
               class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ request('rating') == '5' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                5 Bintang
            </a>
            <a href="{{ route('admin.reviews.index', array_merge(request()->query(), ['rating' => '4'])) }}" 
               class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ request('rating') == '4' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                4 Bintang
            </a>
            <a href="{{ route('admin.reviews.index', array_merge(request()->query(), ['rating' => 'low'])) }}" 
               class="px-[18px] py-2 rounded-full border-[1.5px] font-bold text-[12.5px] transition-all {{ request('rating') == 'low' ? 'border-[#0f766e] bg-[#0f766e] text-white shadow-teal-sm' : 'border-slate-200 bg-white text-slate-500' }}">
                3 Bintang ke Bawah
            </a>
        </div>

        <form action="{{ route('admin.reviews.index') }}" method="GET" class="relative">
            <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[15px]"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari client atau service..." 
                   class="pl-10 pr-4 py-[9px] w-[260px] border-[1.5px] border-slate-200 rounded-[11px] text-[13px] font-semibold text-slate-700 bg-white outline-none focus:border-[#0f766e] transition-all" />
        </form>
    </div>

    @if($reviews->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fadeUp-3">
            @foreach($reviews as $review)
                <div class="bg-white border border-slate-200 rounded-[24px] p-6 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-start justify-between mb-5">
                        <div class="flex items-center gap-3.5">
                            <img src="https://ui-avatars.com/api/?name=${{ urlencode($review->order->client->name ?? 'C') }}&background=0f766e&color=fff" class="w-11 h-11 rounded-xl shadow-sm" />
                            <div>
                                <h3 class="font-bold text-slate-900 text-[14.5px]">{{ $review->order->client->name ?? 'Client' }}</h3>
                                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-0.5">Order #{{ $review->order_id }}</p>
                            </div>
                        </div>
                        <div class="flex gap-0.5 bg-amber-50 px-2 py-1 rounded-lg">
                             <i class="ri-star-fill text-amber-400 text-[13px]"></i>
                             <span class="text-amber-700 font-black text-[12px] ml-0.5">{{ $review->rating }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-5">
                        <p class="text-slate-500 text-[11px] font-bold uppercase tracking-widest mb-1.5">Service</p>
                        <p class="text-slate-800 font-bold text-[13.5px] truncate">{{ $review->order->service->title ?? 'N/A' }}</p>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-5">
                        <p class="text-slate-600 text-[13px] leading-relaxed italic">"{{ $review->comment ?? 'Tidak ada komentar.' }}"</p>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-50">
                        <span class="text-slate-400 text-[11px] font-medium">{{ $review->created_at->format('d M Y') }}</span>
                        <button onclick="window.openDeleteReview({{ $review->id }})" class="text-[11px] font-bold text-red-500 hover:text-red-700 uppercase tracking-wider">Delete Review</button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 flex justify-center pagination-container">
            {{ $reviews->links() }}
        </div>
    @else
        <div class="py-24 text-center animate-fadeUp-3">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5 text-[2.5rem] text-slate-300">
                <i class="ri-star-line"></i>
            </div>
            <h3 class="text-[1.2rem] font-extrabold text-slate-900">Belum Ada Ulasan</h3>
            <p class="text-slate-500 max-w-[320px] mx-auto mt-2">Tidak ditemukan ulasan sesuai filter yang dipilih.</p>
        </div>
    @endif
@endsection

@section('modals')
    <!-- Delete Confirmation Modal -->
    <div class="fixed inset-0 z-[200] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300" id="modal-delete-review">
        <div class="bg-white rounded-[24px] w-full max-w-[400px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300">
            <div class="px-8 pt-8 pb-6 text-center">
                <div class="w-[72px] h-[72px] mx-auto mb-5 bg-red-50 rounded-full flex items-center justify-center text-[2rem] text-red-500">
                    <i class="ri-error-warning-fill"></i>
                </div>
                <h3 class="text-[1.3rem] font-black text-slate-900 mb-2">Hapus Ulasan?</h3>
                <p class="text-[13.5px] text-slate-500 leading-relaxed">Ulasan ini akan dihapus permanen dan tidak dapat dikembalikan.</p>
            </div>
            <div class="flex gap-3 px-8 pb-8">
                <button onclick="window.closeDeleteReview()" class="flex-1 py-3.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-[13px] hover:bg-slate-200 transition-all">Batal</button>
                <button id="btn-confirm-delete-review" class="flex-1 py-3.5 rounded-xl bg-red-500 text-white font-bold text-[13px] hover:bg-red-600 transition-all shadow-lg shadow-red-200">Ya, Hapus</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.openDeleteReview = function(id) {
            const overlay = document.getElementById('modal-delete-review');
            overlay.classList.remove('opacity-0', 'pointer-events-none');

            const btn = document.getElementById('btn-confirm-delete-review');
            btn.onclick = function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/reviews/' + id;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            };
        };

        window.closeDeleteReview = function() {
            const overlay = document.getElementById('modal-delete-review');
            overlay.classList.add('opacity-0', 'pointer-events-none');
        };
    </script>
@endsection