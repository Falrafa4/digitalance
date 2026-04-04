@extends('layouts.dashboard')
@section('title', 'Offers & Negotiations | Digitalance')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/offers.css') }}">
@endsection

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <i class="ri-price-tag-3-line text-2xl text-teal-600"></i>
                    <h1 class="text-2xl font-bold text-gray-800">Offers & Negotiations</h1>
                </div>
                <p class="text-gray-600">Pantau tawaran jasa masuk dan log pesan negosiasi antar pengguna secara real-time.
                </p>
            </div>
            <div class="relative w-full md:w-64">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="global-search-input" onkeyup="handleSearch()" placeholder="Cari ID atau Nama..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8" id="stats-row">
    </div>

    <div class="flex border-b border-gray-200 mb-6 bg-white rounded-t-lg overflow-hidden">
        <button
            class="section-tab active w-full md:w-auto px-6 py-3 text-sm font-semibold text-gray-500 border-b-2 border-transparent transition-all"
            data-target="offers-section">
            <i class="ri-price-tag-3-line mr-2"></i>Data Tawaran (Offers)
        </button>
        <button
            class="section-tab w-full md:w-auto px-6 py-3 text-sm font-semibold text-gray-500 border-b-2 border-transparent transition-all"
            data-target="nego-section">
            <i class="ri-discuss-line mr-2"></i>Pesan Negosiasi
        </button>
    </div>

    <div id="offers-section" class="tab-content active space-y-4">
        <div class="flex flex-wrap gap-2" id="offers-filters">
            <button
                class="filter-tab active px-4 py-1.5 text-xs font-medium rounded-full bg-teal-600 text-white transition-all"
                data-filter="all">Semua</button>
            <button
                class="filter-tab px-4 py-1.5 text-xs font-medium rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50"
                data-filter="sent">Sent</button>
            <button
                class="filter-tab px-4 py-1.5 text-xs font-medium rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50"
                data-filter="accepted">Accepted</button>
            <button
                class="filter-tab px-4 py-1.5 text-xs font-medium rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-gray-50"
                data-filter="rejected">Rejected</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="offers-table">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Offer ID</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Klien & Freelancer</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Detail Layanan</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Harga</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="offers-tbody" class="divide-y divide-gray-50">
                    </tbody>
                </table>
            </div>

            <div id="offers-empty" class="py-20 text-center hidden">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-price-tag-3-line text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-gray-700 font-bold text-lg">Tidak ada tawaran</h3>
                <p class="text-gray-400">Data tawaran akan muncul setelah ada aktivitas transaksi.</p>
            </div>
        </div>
    </div>

    <div id="nego-section" class="tab-content space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="nego-table">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Nego ID</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Order Ref</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Pengirim</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Isi Pesan</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Waktu</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="nego-tbody" class="divide-y divide-gray-50">
                    </tbody>
                </table>
            </div>

            <div id="nego-empty" class="py-20 text-center hidden">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-discuss-line text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-gray-700 font-bold text-lg">Belum ada negosiasi</h3>
                <p class="text-gray-400">Log percakapan negosiasi belum terekam.</p>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <div id="detail-modal-overlay"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[99] hidden flex items-center justify-center transition-all duration-300">
        <div id="detail-modal-box"
            class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 overflow-hidden transform transition-all">
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Menyiapkan data dari database ke format JSON untuk diolah JavaScript
        var offersData = {!! json_encode($offers) !!};
        var negotiationsData = {!! json_encode($negotiations) !!};
        window.OFFERS_PAGE = {
            offers: offersData,
            negotiations: negotiationsData
        };

        // Tab Switching Logic (Klik Tap-Tap)
        document.querySelectorAll('.section-tab').forEach(tab => {
            tab.addEventListener('click', function () {
                // Hapus class active dari semua tab
                document.querySelectorAll('.section-tab').forEach(t => {
                    t.classList.remove('active', 'text-teal-600', 'border-teal-600');
                    t.classList.add('text-gray-500', 'border-transparent');
                });

                // Tambahkan class active ke tab yang diklik
                this.classList.add('active', 'text-teal-600', 'border-teal-600');
                this.classList.remove('text-gray-500', 'border-transparent');

                // Sembunyikan semua konten, tunjukkan yang dipilih
                const target = this.getAttribute('data-target');
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                document.getElementById(target).classList.add('active');
            });
        });
    </script>
    <script src="{{ asset('js/offers.js') }}"></script>
@endsection