@extends('layouts.dashboard')
@section('title', 'Settings | Digitalance')

@section('content')
    <section class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-8 animate-fadeUp">
        <div class="min-w-0">
            <h1 class="font-display text-[1.85rem] sm:text-[2.1rem] font-extrabold text-slate-900 leading-tight">
                Settings
            </h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">
                Pusat navigasi & panduan admin. (Tanpa perubahan backend)
            </p>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Quick Links --}}
        <section class="xl:col-span-2">
            <h2 class="font-display text-[1.2rem] font-bold mb-4">Quick Links</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <a href="{{ route('admin.orders.index') }}"
                    class="group relative bg-white border border-slate-200 rounded-[20px] p-6 transition-all duration-300 hover:border-emerald-500 hover:shadow-[0_12px_40px_rgba(16,185,129,0.15)] hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex flex-col gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <i class="ri-file-list-3-line text-[22px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-display font-bold text-[1.1rem] text-slate-900 mb-1">Orders</div>
                            <div class="text-[13px] text-slate-500 leading-relaxed">Kelola order & status pekerjaan secara real-time</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.transactions.index') }}"
                    class="group relative bg-white border border-slate-200 rounded-[20px] p-6 transition-all duration-300 hover:border-sky-500 hover:shadow-[0_12px_40px_rgba(14,165,233,0.15)] hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-sky-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex flex-col gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center shadow-lg shadow-sky-500/30">
                            <i class="ri-bank-card-line text-[22px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-display font-bold text-[1.1rem] text-slate-900 mb-1">Transactions</div>
                            <div class="text-[13px] text-slate-500 leading-relaxed">Pantau arus kas, pembayaran & status refund</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.offers.index') }}"
                    class="group relative bg-white border border-slate-200 rounded-[20px] p-6 transition-all duration-300 hover:border-violet-500 hover:shadow-[0_12px_40px_rgba(139,92,246,0.15)] hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-violet-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex flex-col gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-400 to-purple-500 text-white flex items-center justify-center shadow-lg shadow-violet-500/30">
                            <i class="ri-price-tag-3-line text-[22px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-display font-bold text-[1.1rem] text-slate-900 mb-1">Offers</div>
                            <div class="text-[13px] text-slate-500 leading-relaxed">Review dan pantau penawaran terhadap order</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.reviews.index') }}"
                    class="group relative bg-white border border-slate-200 rounded-[20px] p-6 transition-all duration-300 hover:border-amber-500 hover:shadow-[0_12px_40px_rgba(245,158,11,0.15)] hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex flex-col gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <i class="ri-star-line text-[22px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-display font-bold text-[1.1rem] text-slate-900 mb-1">Reviews</div>
                            <div class="text-[13px] text-slate-500 leading-relaxed">Analisis sentimen dan ulasan dari Client</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.services.index') }}"
                    class="group relative bg-white border border-slate-200 rounded-[20px] p-6 transition-all duration-300 hover:border-teal-500 hover:shadow-[0_12px_40px_rgba(20,184,166,0.15)] hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-teal-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex flex-col gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-400 to-emerald-500 text-white flex items-center justify-center shadow-lg shadow-teal-500/30">
                            <i class="ri-tools-line text-[22px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-display font-bold text-[1.1rem] text-slate-900 mb-1">Services</div>
                            <div class="text-[13px] text-slate-500 leading-relaxed">Manajemen daftar layanan dan kategori aktif</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.portofolios.index') }}"
                    class="group relative bg-white border border-slate-200 rounded-[20px] p-6 transition-all duration-300 hover:border-indigo-500 hover:shadow-[0_12px_40px_rgba(99,102,241,0.15)] hover:-translate-y-1 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex flex-col gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-400 to-blue-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30">
                            <i class="ri-folder-user-line text-[22px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-display font-bold text-[1.1rem] text-slate-900 mb-1">Portofolios</div>
                            <div class="text-[13px] text-slate-500 leading-relaxed">Lihat portofolio publik dari para freelancer</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.profile') }}"
                    class="group relative bg-white border border-slate-200 rounded-[20px] p-6 transition-all duration-300 hover:border-rose-500 hover:shadow-[0_12px_40px_rgba(244,63,94,0.15)] hover:-translate-y-1 overflow-hidden lg:col-span-3">
                    <div class="absolute inset-0 bg-gradient-to-br from-rose-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative flex flex-row items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-400 to-pink-600 text-white flex items-center justify-center shadow-lg shadow-rose-500/30 flex-shrink-0">
                            <i class="ri-user-settings-line text-[24px]"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-display font-bold text-[1.2rem] text-slate-900 mb-1">Account & Security</div>
                            <div class="text-[13px] text-slate-500 leading-relaxed">Ubah data profil, password, dan pengaturan keamanan Admin.</div>
                        </div>
                        <i class="ri-arrow-right-s-line text-slate-400 text-[24px] group-hover:text-rose-500 transition-colors"></i>
                    </div>
                </a>
            </div>
        </section>

        {{-- Admin Guidelines --}}
        <section>
            <h2 class="font-display text-[1.2rem] font-bold mb-4">Admin Guidelines</h2>

            <div class="bg-white border border-slate-200 rounded-[18px] p-6">
                <div class="flex gap-3 items-start pb-4 border-b border-slate-200">
                    <i class="ri-user-received-line text-emerald-600 text-[20px] flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h3 class="text-[14px] text-slate-900 mb-1 font-semibold">Verifikasi Freelancer</h3>
                        <p class="text-[12px] text-slate-500 leading-relaxed">
                            Approve jika data valid. Reject jika data tidak sesuai/duplikat.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 items-start py-4 border-b border-slate-200">
                    <i class="ri-file-warning-line text-orange-500 text-[20px] flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h3 class="text-[14px] text-slate-900 mb-1 font-semibold">Dispute</h3>
                        <p class="text-[12px] text-slate-500 leading-relaxed">
                            Cek “Open Disputes” di dashboard dan follow-up order terkait.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 items-start pt-4">
                    <i class="ri-shield-check-line text-slate-700 text-[20px] flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h3 class="text-[14px] text-slate-900 mb-1 font-semibold">Standar Operasional</h3>
                        <ul class="text-[12px] text-slate-500 leading-relaxed list-disc pl-4 space-y-1">
                            <li>Gunakan status order sesuai alur (pending → paid → in_progress → completed).</li>
                            <li>Pastikan transaksi “paid” sebelum order diproses.</li>
                            <li>Review digunakan untuk evaluasi layanan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection