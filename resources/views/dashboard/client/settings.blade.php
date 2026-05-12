@extends('layouts.dashboard')
@section('title', 'Settings | Digitalance')

@section('content')
    <section class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-8">
        <div class="min-w-0">
            <h1 class="font-display text-[1.65rem] font-extrabold text-slate-900 mb-1.5 flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                    <i class="ri-settings-4-line text-[20px]"></i>
                </div>
                Settings
            </h1>
            <p class="text-slate-500 text-[13.5px]">
                Pusat navigasi dan pengaturan akun Client.
            </p>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Quick Links --}}
        <section class="lg:col-span-2">
            <h2 class="font-display text-[1.1rem] font-bold text-slate-800 mb-4">Akses Cepat (Quick Links)</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('client.orders.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="ri-file-list-3-line text-[50px] text-slate-100/50 -rotate-12 translate-x-4 -translate-y-4"></i>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Orders</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Pantau dan kelola pesanan kamu.</p>
                    </div>
                </a>

                <a href="{{ route('client.services.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="ri-tools-line text-[50px] text-slate-100/50 -rotate-12 translate-x-4 -translate-y-4"></i>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-tools-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Katalog Jasa</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Browse layanan yang tersedia.</p>
                    </div>
                </a>

                <a href="{{ route('client.talents.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="ri-user-search-line text-[50px] text-slate-100/50 -rotate-12 translate-x-4 -translate-y-4"></i>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-user-search-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Find Talent</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Temukan freelancer terbaik.</p>
                    </div>
                </a>

                <a href="{{ route('client.results.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="ri-task-line text-[50px] text-slate-100/50 -rotate-12 translate-x-4 -translate-y-4"></i>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-task-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Hasil Kerja</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Lihat & unduh hasil pekerjaan freelancer.</p>
                    </div>
                </a>

                <a href="{{ route('client.loker.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="ri-briefcase-2-line text-[50px] text-slate-100/50 -rotate-12 translate-x-4 -translate-y-4"></i>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-briefcase-2-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Lowongan Kerja</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Pantau job dan penawaran freelancer.</p>
                    </div>
                </a>

                <a href="{{ route('client.payments.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="ri-wallet-3-line text-[50px] text-slate-100/50 -rotate-12 translate-x-4 -translate-y-4"></i>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-wallet-3-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Payments</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Riwayat pembayaran dan transaksi.</p>
                    </div>
                </a>

                <a href="{{ route('client.messages.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-chat-voice-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Messages</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Percakapan dengan freelancer.</p>
                    </div>
                </a>

                <a href="{{ route('client.profile') }}" class="group bg-slate-900 border border-slate-800 rounded-[18px] p-5 hover:bg-black transition-all duration-200 flex items-center justify-between sm:col-span-2 shadow-md hover:shadow-xl hover:-translate-y-0.5">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-xl bg-white/10 text-white flex items-center justify-center text-[22px] shrink-0">
                            <i class="ri-user-settings-line"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-[14.5px] text-white mb-0.5">Account & Security</h3>
                            <p class="text-[12.5px] text-slate-400">Ubah data profil, password, dan pengaturan keamanan.</p>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white group-hover:bg-teal-500 group-hover:scale-110 transition-all shrink-0 ml-4">
                        <i class="ri-arrow-right-line"></i>
                    </div>
                </a>
            </div>
        </section>

        {{-- Panduan Client --}}
        <section>
            <h2 class="font-display text-[1.1rem] font-bold text-slate-800 mb-4">Panduan Client</h2>

            <div class="bg-white border border-slate-200 rounded-[20px] p-6 h-[calc(100%-2.5rem)] shadow-sm">

                <div class="relative pl-6 pb-6 border-l-2 border-slate-100 last:pb-0 last:border-transparent">
                    <div class="absolute left-[-9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-emerald-500 shadow-[0_0_0_3px_rgba(16,185,129,0.1)]"></div>
                    <h3 class="text-[14px] text-slate-900 mb-1.5 font-bold -mt-1">Buat Order</h3>
                    <p class="text-[12.5px] text-slate-500 leading-relaxed">
                        Pilih jasa dari katalog, isi brief yang jelas, dan tunggu freelancer merespons.
                    </p>
                </div>

                <div class="relative pl-6 pb-6 border-l-2 border-slate-100 last:pb-0 last:border-transparent">
                    <div class="absolute left-[-9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-amber-500 shadow-[0_0_0_3px_rgba(245,158,11,0.1)]"></div>
                    <h3 class="text-[14px] text-slate-900 mb-1.5 font-bold -mt-1">Negosiasi</h3>
                    <p class="text-[12.5px] text-slate-500 leading-relaxed">
                        Jika harga perlu disesuaikan, gunakan fitur negosiasi sebelum melakukan pembayaran.
                    </p>
                </div>

                <div class="relative pl-6 pb-6 border-l-2 border-slate-100 last:pb-0 last:border-transparent">
                    <div class="absolute left-[-9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-blue-500 shadow-[0_0_0_3px_rgba(59,130,246,0.1)]"></div>
                    <h3 class="text-[14px] text-slate-900 mb-1.5 font-bold -mt-1">Pembayaran</h3>
                    <ul class="text-[12.5px] text-slate-500 leading-relaxed list-disc pl-4 space-y-1.5 mt-2">
                        <li>Pastikan harga sudah disepakati sebelum bayar.</li>
                        <li>Simpan bukti pembayaran.</li>
                        <li>Request revisi jika hasil belum sesuai.</li>
                    </ul>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                        <i class="ri-user-voice-fill text-[20px]"></i>
                    </div>
                    <div>
                        <div class="text-[11.5px] font-bold text-slate-800 uppercase tracking-wider">Digitalance Client</div>
                        <div class="text-[11px] text-slate-400">Version 1.0.0</div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
