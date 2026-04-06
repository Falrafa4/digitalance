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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('admin.orders.index') }}"
                    class="bg-white border border-slate-200 rounded-2xl p-5 transition-all hover:border-emerald-400 hover:shadow-[0_10px_30px_rgba(2,6,23,0.06)]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <i class="ri-file-list-3-line text-[18px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900">Orders</div>
                            <div class="text-[12px] text-slate-500">Kelola order & status pekerjaan</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.transactions.index') }}"
                    class="bg-white border border-slate-200 rounded-2xl p-5 transition-all hover:border-emerald-400 hover:shadow-[0_10px_30px_rgba(2,6,23,0.06)]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center">
                            <i class="ri-bank-card-line text-[18px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900">Transactions</div>
                            <div class="text-[12px] text-slate-500">Pantau pembayaran & status transaksi</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.offers.index') }}"
                    class="bg-white border border-slate-200 rounded-2xl p-5 transition-all hover:border-emerald-400 hover:shadow-[0_10px_30px_rgba(2,6,23,0.06)]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-700 flex items-center justify-center">
                            <i class="ri-price-tag-3-line text-[18px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900">Offers</div>
                            <div class="text-[12px] text-slate-500">Cek penawaran terhadap order</div>
                        </div>
                    </div>
                </a>


                <a href="{{ route('admin.reviews.index') }}"
                    class="bg-white border border-slate-200 rounded-2xl p-5 transition-all hover:border-emerald-400 hover:shadow-[0_10px_30px_rgba(2,6,23,0.06)]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-800 flex items-center justify-center">
                            <i class="ri-star-line text-[18px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900">Reviews</div>
                            <div class="text-[12px] text-slate-500">Lihat rating & ulasan client</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.services.index') }}"
                    class="bg-white border border-slate-200 rounded-2xl p-5 transition-all hover:border-emerald-400 hover:shadow-[0_10px_30px_rgba(2,6,23,0.06)]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center">
                            <i class="ri-tools-line text-[18px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900">Services</div>
                            <div class="text-[12px] text-slate-500">Kelola jasa & kategori</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.portofolios.index') }}"
                    class="bg-white border border-slate-200 rounded-2xl p-5 transition-all hover:border-emerald-400 hover:shadow-[0_10px_30px_rgba(2,6,23,0.06)]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center">
                            <i class="ri-folder-user-line text-[18px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900">Portofolios</div>
                            <div class="text-[12px] text-slate-500">Lihat portofolio freelancer</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.profile') }}"
                    class="bg-white border border-slate-200 rounded-2xl p-5 transition-all hover:border-emerald-400 hover:shadow-[0_10px_30px_rgba(2,6,23,0.06)]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center">
                            <i class="ri-user-line text-[18px]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900">Account</div>
                            <div class="text-[12px] text-slate-500">Edit profil admin</div>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        {{-- Admin Guidelines --}}
        <section>
            <h2 class="font-display text-[1.2rem] font-bold mb-4">Admin Guidelines</h2>

            <div class="bg-white border border-slate-200 rounded-3xl p-6">
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