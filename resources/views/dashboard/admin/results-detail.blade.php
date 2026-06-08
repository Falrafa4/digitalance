@extends('layouts.dashboard')
@section('title', 'Detail Hasil | Digitalance')

@section('content')
    <div class="animate-fadeUp max-w-4xl mx-auto py-8">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('admin.results.index') }}"
                class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition-colors font-bold text-sm">
                <i class="ri-arrow-left-line"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-\[28px\] overflow-hidden shadow-sm max-w-\[680px\] mx-auto">
            <div class="p-9 border-b border-slate-100 bg-slate-50/50">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div
                            class="w-16 h-16 rounded-[22px] bg-white border border-slate-200 shadow-sm flex items-center justify-center text-3xl text-[#0f766e]">
                            <i class="ri-file-list-2-line"></i>
                        </div>
                        <div>
                            <h1 class="font-display text-[1.8rem] font-black text-slate-900 leading-tight">Detail Hasil
                                Pekerjaan</h1>
                            <p class="text-slate-400 text-sm font-bold uppercase tracking-widest mt-1">Result ID:
                                #RES-{{ $result->id }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white p-2 rounded-2xl border border-slate-200">
                        <div class="flex -space-x-2">
                            @php
                                $clientAvatarUrl = $result->order->client->profile_photo
                                    ? asset('storage/' . $result->order->client->profile_photo)
                                    : 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($result->order->client->name ?? $result->order->client->email ?? 'User') . '&background=6366f1&color=fff&size=32';
                                $freelancerAvatarUrl = $result->order->service->freelancer->profile_photo
                                    ? asset('storage/' . $result->order->service->freelancer->profile_photo)
                                    : ($result->order->service->freelancer->skomda_student->avatar
                                        ? asset('storage/' . $result->order->service->freelancer->skomda_student->avatar)
                                        : 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($result->order->service->freelancer->skomda_student->name ?? $result->order->service->freelancer->skomda_student->email ?? 'User') . '&background=0f766e&color=fff&size=32');
                            @endphp
                            <img src="{{ $clientAvatarUrl }}" class="w-8 h-8 rounded-full border-2 border-white" title="Client" />
                            <img src="{{ $freelancerAvatarUrl }}" class="w-8 h-8 rounded-full border-2 border-white" title="Freelancer" />
                        </div>
                        <div class="pr-2">
                            <span class="text-[11px] font-bold text-slate-400 uppercase">Collaboration</span>
                            <p class="text-[12px] font-black text-slate-700">#ORD-{{ $result->order_id }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-9 grid grid-cols-1 lg:grid-cols-3 gap-7">
                <div class="lg:col-span-2 space-y-8">
                    <section>
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] mb-4">Pesan & Catatan
                            Freelancer</h3>
                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6">
                            <p class="text-slate-700 text-[14px] leading-relaxed whitespace-pre-wrap">{{ $result->note ?: 'Tidak ada catatan tertulis.' }}</p>
                            <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-4">
                                Versi: {{ $result->version ?: '-' }}
                            </p>
                        </div>
                    </section>

                    <section>
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] mb-4">File Pekerjaan</h3>
                        @if($result->file_url)
                        <div
                            class="flex items-center justify-between p-5 bg-white border-2 border-slate-100 rounded-3xl hover:border-[#0f766e]/30 transition-all group">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-[#f0fdfa] text-[#0f766e] flex items-center justify-center text-2xl">
                                    <i class="{{ $result->fileIcon() }}"></i>
                                </div>
                                <div>
                                    <p
                                        class="text-[14px] font-black text-slate-900 group-hover:text-[#0f766e] transition-colors">
                                        {{ $result->fileLabel() }}</p>
                                    <p class="text-[11px] text-slate-500 font-bold uppercase">
                                        {{ $result->isExternalLink() ? 'Link eksternal' : 'File tersimpan' }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 font-bold uppercase">Diunggah pada
                                        {{ $result->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ $result->downloadUrl() }}" target="_blank" rel="noopener noreferrer"
                                class="px-5 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-[12px] hover:bg-[#0f766e] transition-all shadow-lg">
                                {{ $result->fileActionLabel() }}
                            </a>
                        </div>
                        @else
                            <div class="p-5 bg-slate-50 border border-slate-100 rounded-3xl text-center text-slate-500 text-[13px]">
                                Tidak ada file atau link hasil.
                            </div>
                        @endif
                    </section>
                </div>

                <div class="space-y-6">
                    <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100">
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4 text-center">
                            Informasi Pesanan</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-slate-500 font-medium">Status Pesanan</span>
                                <x-ui.status-badge :status="$result->order->status" />
                            </div>
                            <div class="flex justify-between items-center text-[13px]">
                                <span class="text-slate-500 font-medium">Harga</span>
                                <span
                                    class="text-slate-900 font-black">Rp{{ number_format($result->order->agreed_price, 0, ',', '.') }}</span>
                            </div>
                            <hr class="border-slate-200">
                            <div class="pt-2">
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Layanan</p>
                                <p class="text-[13px] font-bold text-slate-800 line-clamp-2 leading-snug">
                                    {{ $result->order->service->title }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <a href="{{ route('admin.orders.index') }}?q={{ $result->order_id }}"
                            class="w-full py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl text-center text-[12px] hover:bg-slate-50 transition-all">
                            Kelola Pesanan Ini
                        </a>
                        <form action="{{ route('admin.results.destroy', $result->id) }}" method="POST"
                            onsubmit="return confirm('Hapus hasil ini? Tindakan ini permanen.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full py-3 bg-red-50 text-red-500 font-bold rounded-2xl text-[12px] hover:bg-red-500 hover:text-white transition-all">
                                Hapus Hasil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
