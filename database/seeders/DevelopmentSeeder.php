<?php

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Negotiation;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Portofolio;
use App\Models\Result;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1 - MASTER DATA ADMIN
        Administrator::firstOrCreate(
            ['email' => 'admin1@email.com'],
            [
                'name' => 'Admin 1',
                'password' => bcrypt('admin123'),
            ]
        );

        Administrator::firstOrCreate(
            ['email' => 'admin2@email.com'],
            [
                'name' => 'Admin 2',
                'password' => bcrypt('admin123'),
            ]
        );

        // 2 - BASE USER DATA
        Client::updateOrCreate(
            ['email' => 'client1@email.com'],
            [
                'name' => 'Client 1',
                'password' => bcrypt('client123'),
                'phone' => '081234567890',
            ]
        );

        $clients = Client::factory(10)->create();
        User::factory(5)->create();

        // 3 - FREELANCER (DEPEND ON USER & STUDENT)
        $freelancers = Freelancer::factory(10)->create();

        SkomdaStudent::whereIn('id', $freelancers->pluck('student_id')->filter()->unique())
            ->update(['is_registered' => true]);

        // 4 - MASTER KATEGORI LAYANAN
        $categories = [
            [
                'name' => 'Web Development',
                'description' => 'Pembuatan website, landing page, dashboard, dan integrasi fitur backend.',
            ],
            [
                'name' => 'Desain Grafis',
                'description' => 'Desain konten visual untuk promosi, branding, dan kebutuhan media sosial.',
            ],
            [
                'name' => 'Jaringan Komputer',
                'description' => 'Instalasi, konfigurasi, dan troubleshooting jaringan untuk sekolah maupun UMKM.',
            ],
            [
                'name' => 'IT Support',
                'description' => 'Dukungan teknis perangkat dan software, termasuk maintenance berkala.',
            ],
            [
                'name' => 'Internet of Things (IoT)',
                'description' => 'Perancangan solusi IoT sederhana untuk monitoring dan otomasi perangkat.',
            ],
            [
                'name' => 'Multimedia',
                'description' => 'Produksi konten multimedia dasar untuk kebutuhan dokumentasi dan promosi.',
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }

        $categoryModels = ServiceCategory::query()->get()->keyBy('name');

        // 5 - SERVICE + PORTOFOLIO
        $serviceCatalog = [
            'Web Development' => [
                [
                    'title' => 'Landing Page Promosi Produk',
                    'description' => 'Pembuatan landing page responsif untuk promosi produk sekolah, UMKM, atau event dengan CTA yang jelas.',
                    'price_min' => 350000,
                    'price_max' => 900000,
                    'delivery_time' => 5,
                ],
                [
                    'title' => 'Website Profil Sekolah',
                    'description' => 'Pembuatan website profil lembaga dengan halaman jurusan, galeri, berita, dan formulir kontak.',
                    'price_min' => 1200000,
                    'price_max' => 2500000,
                    'delivery_time' => 12,
                ],
                [
                    'title' => 'Dashboard Admin Sederhana',
                    'description' => 'Pembuatan dashboard admin untuk rekap data, filter pencarian, dan export laporan dasar.',
                    'price_min' => 1500000,
                    'price_max' => 3000000,
                    'delivery_time' => 14,
                ],
            ],
            'Desain Grafis' => [
                [
                    'title' => 'Desain Feed Instagram Branding',
                    'description' => 'Pembuatan template feed Instagram yang konsisten untuk branding dan promosi konten harian.',
                    'price_min' => 300000,
                    'price_max' => 750000,
                    'delivery_time' => 4,
                ],
                [
                    'title' => 'Poster Event Sekolah',
                    'description' => 'Desain poster digital untuk lomba, seminar, bazar, atau kegiatan sekolah lainnya.',
                    'price_min' => 200000,
                    'price_max' => 500000,
                    'delivery_time' => 3,
                ],
                [
                    'title' => 'Logo UMKM Sederhana',
                    'description' => 'Pembuatan logo minimalis untuk usaha kecil dengan beberapa opsi konsep warna dan ikon.',
                    'price_min' => 250000,
                    'price_max' => 800000,
                    'delivery_time' => 5,
                ],
            ],
            'Jaringan Komputer' => [
                [
                    'title' => 'Setup Jaringan Kantor Kecil',
                    'description' => 'Instalasi topologi jaringan dasar untuk kantor kecil, toko, atau ruang laboratorium.',
                    'price_min' => 750000,
                    'price_max' => 1800000,
                    'delivery_time' => 7,
                ],
                [
                    'title' => 'Konfigurasi WiFi Sekolah',
                    'description' => 'Pengaturan router, access point, dan pemisahan jaringan tamu untuk lingkungan sekolah.',
                    'price_min' => 500000,
                    'price_max' => 1200000,
                    'delivery_time' => 4,
                ],
                [
                    'title' => 'Troubleshooting Jaringan',
                    'description' => 'Pengecekan dan perbaikan koneksi internet, perangkat jaringan, dan gangguan akses dasar.',
                    'price_min' => 300000,
                    'price_max' => 900000,
                    'delivery_time' => 2,
                ],
            ],
            'IT Support' => [
                [
                    'title' => 'Maintenance Laptop Sekolah',
                    'description' => 'Pembersihan software, update aplikasi, backup data, dan optimasi laptop atau PC.',
                    'price_min' => 250000,
                    'price_max' => 650000,
                    'delivery_time' => 3,
                ],
                [
                    'title' => 'Instalasi Aplikasi Kerja',
                    'description' => 'Instalasi dan konfigurasi aplikasi kantor, belajar, atau produksi sesuai kebutuhan pengguna.',
                    'price_min' => 150000,
                    'price_max' => 400000,
                    'delivery_time' => 2,
                ],
                [
                    'title' => 'Remote Helpdesk Dasar',
                    'description' => 'Bantuan teknis jarak jauh untuk masalah login, software error, dan setting perangkat.',
                    'price_min' => 100000,
                    'price_max' => 300000,
                    'delivery_time' => 1,
                ],
            ],
            'Internet of Things (IoT)' => [
                [
                    'title' => 'Monitoring Suhu Ruangan',
                    'description' => 'Pembuatan solusi IoT untuk memantau suhu dan kelembapan ruang kelas atau laboratorium.',
                    'price_min' => 1200000,
                    'price_max' => 2800000,
                    'delivery_time' => 14,
                ],
                [
                    'title' => 'Otomasi Lampu Sederhana',
                    'description' => 'Pembuatan prototype kontrol lampu otomatis berbasis sensor atau jadwal waktu.',
                    'price_min' => 900000,
                    'price_max' => 2200000,
                    'delivery_time' => 10,
                ],
            ],
            'Multimedia' => [
                [
                    'title' => 'Editing Video Promosi',
                    'description' => 'Penyuntingan video pendek untuk promosi produk, kegiatan sekolah, atau sosial media.',
                    'price_min' => 350000,
                    'price_max' => 1200000,
                    'delivery_time' => 5,
                ],
                [
                    'title' => 'Motion Graphics Intro',
                    'description' => 'Pembuatan intro video singkat dengan animasi teks, logo, dan transisi dasar.',
                    'price_min' => 500000,
                    'price_max' => 1500000,
                    'delivery_time' => 7,
                ],
                [
                    'title' => 'Foto Produk Marketplace',
                    'description' => 'Pengambilan dan pengolahan foto produk agar lebih menarik untuk katalog online.',
                    'price_min' => 250000,
                    'price_max' => 800000,
                    'delivery_time' => 4,
                ],
            ],
        ];

        foreach ($serviceCatalog as $categoryName => $services) {
            $category = $categoryModels->get($categoryName);

            if (!$category) {
                continue;
            }

            foreach ($services as $service) {
                Service::updateOrCreate(
                    ['title' => $service['title']],
                    [
                        'category_id' => $category->id,
                        'freelancer_id' => $freelancers->random()->id,
                        'description' => $service['description'],
                        'price_min' => $service['price_min'],
                        'price_max' => $service['price_max'],
                        'delivery_time' => $service['delivery_time'],
                        'status' => 'Approved',
                    ]
                );
            }
        }

        Portofolio::factory(20)->create();

        // 6 - TRANSACTION FLOW
        $orders = Order::factory(30)->create();
        $offers = Offer::factory(30)->create();
        Negotiation::factory(10)->create();
        Transaction::factory(15)->create();
        Result::factory(15)->create();

        // 7 - REVIEW
        // Buat review hanya jika order belum punya review
        Order::doesntHave('review')
            ->get()
            ->each(function ($order) {
                Review::factory()->create([
                    'order_id' => $order->id,
                ]);
            });
    }
}
