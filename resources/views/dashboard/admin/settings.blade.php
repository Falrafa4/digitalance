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
                Pusat navigasi & panduan operasional administrator.
            </p>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Quick Links --}}
        <section class="lg:col-span-2">
            <h2 class="font-display text-[1.1rem] font-bold text-slate-800 mb-4">Akses Cepat (Quick Links)</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('admin.orders.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Orders Management</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Kelola order & status pekerjaan secara real-time.</p>
                    </div>
                </a>

                <a href="{{ route('admin.transactions.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-bank-card-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Transactions</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Pantau arus kas, verifikasi pembayaran & refund.</p>
                    </div>
                </a>

                <a href="{{ route('admin.offers.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-price-tag-3-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Offers</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Review dan pantau penawaran custom order.</p>
                    </div>
                </a>

                <a href="{{ route('admin.reviews.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-star-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Reviews & Ratings</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Analisis sentimen dan ulasan kepuasan Client.</p>
                    </div>
                </a>

                <a href="{{ route('admin.services.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-tools-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Services Catalog</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Manajemen daftar layanan dan kategori aktif.</p>
                    </div>
                </a>

                <a href="{{ route('admin.portofolios.index') }}" class="group bg-white border border-slate-200 rounded-[18px] p-5 hover:border-teal-400 hover:shadow-teal-sm transition-all duration-200 flex items-start gap-4 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-slate-50 to-transparent -z-10 rounded-bl-[100px] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-[22px] shrink-0 group-hover:scale-110 transition-transform">
                        <i class="ri-folder-user-line"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-[14.5px] text-slate-900 mb-1 group-hover:text-teal-700 transition-colors">Portofolios</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Lihat portofolio publik dari para freelancer.</p>
                    </div>
                </a>

                <a href="{{ route('admin.profile') }}" class="group bg-slate-900 border border-slate-800 rounded-[18px] p-5 hover:bg-black transition-all duration-200 flex items-center justify-between sm:col-span-2 shadow-md hover:shadow-xl hover:-translate-y-0.5">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-xl bg-white/10 text-white flex items-center justify-center text-[22px] shrink-0">
                            <i class="ri-user-settings-line"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-[14.5px] text-white mb-0.5">Account & Security</h3>
                            <p class="text-[12.5px] text-slate-400">Ubah data profil, password, dan pengaturan keamanan Admin.</p>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white group-hover:bg-teal-500 group-hover:scale-110 transition-all shrink-0 ml-4">
                        <i class="ri-arrow-right-line"></i>
                    </div>
                </a>
            </div>
        </section>

        {{-- Admin Guidelines --}}
        <section>
            <h2 class="font-display text-[1.1rem] font-bold text-slate-800 mb-4">Panduan Operasional</h2>

            <div class="bg-white border border-slate-200 rounded-[20px] p-6 h-[calc(100%-2.5rem)] shadow-sm">
                
                <div class="relative pl-6 pb-6 border-l-2 border-slate-100 last:pb-0 last:border-transparent">
                    <div class="absolute left-[-9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-emerald-500 shadow-[0_0_0_3px_rgba(16,185,129,0.1)]"></div>
                    <h3 class="text-[14px] text-slate-900 mb-1.5 font-bold -mt-1">Verifikasi Akun</h3>
                    <p class="text-[12.5px] text-slate-500 leading-relaxed">
                        Approve freelancer jika identitas siswa valid. Reject jika data mencurigakan/duplikat.
                    </p>
                </div>

                <div class="relative pl-6 pb-6 border-l-2 border-slate-100 last:pb-0 last:border-transparent">
                    <div class="absolute left-[-9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-amber-500 shadow-[0_0_0_3px_rgba(245,158,11,0.1)]"></div>
                    <h3 class="text-[14px] text-slate-900 mb-1.5 font-bold -mt-1">Monitor Dispute</h3>
                    <p class="text-[12.5px] text-slate-500 leading-relaxed">
                        Cek laporan *Fake Transaction* di halaman Transactions dan tanggapi secara adil.
                    </p>
                </div>

                <div class="relative pl-6 pb-6 border-l-2 border-slate-100 last:pb-0 last:border-transparent">
                    <div class="absolute left-[-9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-blue-500 shadow-[0_0_0_3px_rgba(59,130,246,0.1)]"></div>
                    <h3 class="text-[14px] text-slate-900 mb-1.5 font-bold -mt-1">Alur Transaksi</h3>
                    <ul class="text-[12.5px] text-slate-500 leading-relaxed list-disc pl-4 space-y-1.5 mt-2">
                        <li>Validasi pembayaran sebelum order diproses.</li>
                        <li>Pastikan status <code class="bg-slate-100 px-1 rounded text-slate-600 text-[11px]">in_progress</code> hanya aktif saat dibayar.</li>
                        <li>Hubungi *Customer* jika ada masalah file hasil.</li>
                    </ul>
                </div>
                
                <div class="mt-6 pt-5 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400">
                        <i class="ri-shield-check-fill text-[20px]"></i>
                    </div>
                    <div>
                        <div class="text-[11.5px] font-bold text-slate-800 uppercase tracking-wider">Digitalance Admin</div>
                        <div class="text-[11px] text-slate-400">Version 1.0.0</div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection