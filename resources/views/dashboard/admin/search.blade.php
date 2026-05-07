@extends('layouts.dashboard')

@section('title', 'Global Search Results - Digitalance')

@section('content')
<div class="mb-8">
    <h2 class="font-display text-[1.65rem] font-extrabold text-slate-900 mb-1.5 flex items-center gap-2">
        <div class="w-9 h-9 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600">
            <i class="ri-search-eye-line text-[20px]"></i>
        </div>
        Search Results
    </h2>
    <p class="text-slate-500 text-[13.5px]">Menampilkan hasil pencarian untuk: <strong class="text-slate-800">"{{ $q }}"</strong></p>
</div>

@if(!$q)
<div class="py-16 px-5 text-center bg-white rounded-3xl border border-slate-200">
    <div class="text-[3rem] text-slate-300 mb-3"><i class="ri-search-line"></i></div>
    <h3 class="font-display text-[1.1rem] font-bold text-slate-700 mb-1.5">Ketik kata kunci</h3>
    <p class="text-[13px] text-slate-400">Silakan masukkan kata kunci pencarian di kolom search header.</p>
</div>
@elseif($results->isEmpty())
<div class="py-16 px-5 text-center bg-white rounded-3xl border border-slate-200 shadow-sm">
    <div class="text-[3rem] text-slate-300 mb-3"><i class="ri-ghost-smile-line"></i></div>
    <h3 class="font-display text-[1.1rem] font-bold text-slate-700 mb-1.5">Tidak ada hasil ditemukan</h3>
    <p class="text-[13px] text-slate-400">Pencarian untuk "{{ $q }}" tidak menghasilkan data apapun.</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @foreach($results as $item)
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-teal-sm transition group flex flex-col h-full relative">
        
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                @if($item->search_type === 'Client')
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xl shrink-0"><i class="ri-user-smile-line"></i></div>
                @elseif($item->search_type === 'Freelancer')
                    <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-xl shrink-0"><i class="ri-user-star-line"></i></div>
                @elseif($item->search_type === 'Service')
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center text-xl shrink-0"><i class="ri-briefcase-4-line"></i></div>
                @elseif($item->search_type === 'Order')
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl shrink-0"><i class="ri-shopping-cart-2-line"></i></div>
                @else
                    <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-500 flex items-center justify-center text-xl shrink-0"><i class="ri-file-list-3-line"></i></div>
                @endif
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10.5px] font-bold uppercase tracking-wider mb-1
                        @if($item->search_type === 'Client') bg-blue-100 text-blue-700 
                        @elseif($item->search_type === 'Freelancer') bg-purple-100 text-purple-700 
                        @elseif($item->search_type === 'Service') bg-orange-100 text-orange-700 
                        @elseif($item->search_type === 'Order') bg-emerald-100 text-emerald-700 
                        @else bg-slate-100 text-slate-700 @endif
                    ">{{ $item->search_type }}</span>
                    <h3 class="font-display font-bold text-slate-800 text-[14.5px] line-clamp-1" title="{{ $item->title ?? $item->name ?? 'Order' }}">
                        {{ $item->title ?? $item->name ?? ('Order #' . str_pad($item->id, 4, '0', STR_PAD_LEFT)) }}
                    </h3>
                </div>
            </div>
        </div>
        
        <div class="text-[13px] text-slate-500 mb-5 flex-1 line-clamp-2 leading-relaxed">
            @if($item->search_type === 'Client')
                Email: {{ $item->email }} <br/>
                Bergabung: {{ $item->created_at->format('d M Y') }}
            @elseif($item->search_type === 'Freelancer')
                Email: {{ $item->skomda_student->email ?? '-' }} <br/>
                Bergabung: {{ $item->created_at->format('d M Y') }}
            @elseif($item->search_type === 'Service')
                Harga: Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}<br/>
                Kategori: {{ $item->service_category->name ?? '-' }}
            @elseif($item->search_type === 'Order')
                Status: {{ $item->status ?? 'Pending' }}<br/>
                Klien: {{ $item->client->name ?? '-' }}
            @endif
        </div>
        
        <div class="pt-4 border-t border-slate-100">
            <a href="{{ $item->search_url }}" class="inline-flex items-center justify-between w-full text-[13px] font-bold text-slate-600 group-hover:text-teal-600 transition">
                <span>Lihat Detail</span>
                <i class="ri-arrow-right-line group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        
        <!-- Make the whole card clickable -->
        <a href="{{ $item->search_url }}" class="absolute inset-0 z-10" aria-label="Lihat Detail {{ $item->title ?? $item->name }}"></a>
    </div>
    @endforeach
</div>
@endif

@endsection
