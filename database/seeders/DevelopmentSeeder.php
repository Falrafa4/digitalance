<?php

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Loker;
use App\Models\LokerApplication;
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

        $freelancers->take(6)->each(function ($freelancer) {
            $freelancer->update(['status' => 'Approved']);
        });
        $freelancers = $freelancers->fresh();

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
        $allClients = Client::query()->orderBy('id')->get()->values();
        $approvedFreelancers = Freelancer::with('skomda_student')
            ->where('status', 'Approved')
            ->orderBy('id')
            ->get()
            ->values();

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

        // 6 - LOKER + LOKER APPLICATIONS
        $this->seedLokers($categoryModels, $allClients, $approvedFreelancers);

        // 7 - TRANSACTION FLOW
        $orders = Order::factory(30)->create();
        $offers = Offer::factory(30)->create();
        Negotiation::factory(10)->create();
        Transaction::factory(15)->create();
        Result::factory(15)->create();

        // 8 - REVIEW
        // Buat review hanya jika order belum punya review
        Order::doesntHave('review')
            ->get()
            ->each(function ($order) {
                Review::factory()->create([
                    'order_id' => $order->id,
                ]);
            });
    }

    private function seedLokers($categoryModels, $clients, $approvedFreelancers): void
    {
        if ($clients->count() < 3 || $approvedFreelancers->count() < 4) {
            return;
        }

        $clientPool = [
            'primary' => $clients->get(0),
            'secondary' => $clients->get(1) ?? $clients->get(0),
            'third' => $clients->get(2) ?? $clients->get(0),
            'fourth' => $clients->get(3) ?? $clients->get(1) ?? $clients->get(0),
        ];

        $freelancerPool = $approvedFreelancers->values();

        $lokerCatalog = [
            [
                'title' => 'Landing Page PPDB Sekolah',
                'description' => 'Butuh freelancer untuk membuat landing page PPDB sekolah yang modern, responsif, mudah dibaca di mobile, dan memiliki formulir CTA yang terhubung ke WhatsApp admin.',
                'category' => 'Web Development',
                'client' => $clientPool['primary'],
                'budget_min' => 1200000,
                'budget_max' => 2200000,
                'deadline' => now()->addDays(10)->toDateString(),
                'status' => 'Open',
                'applications' => [
                    [
                        'freelancer' => $freelancerPool->get(0),
                        'proposal' => 'Saya siap membuat landing page PPDB dengan fokus pada performa, struktur konten yang jelas, dan CTA yang mudah dikonversi.',
                        'proposed_price' => 1800000,
                        'status' => 'Pending',
                    ],
                    [
                        'freelancer' => $freelancerPool->get(1),
                        'proposal' => 'Saya punya pengalaman membangun landing page promosi sekolah dan akan menyiapkan desain yang ramah mobile serta mudah dikelola.',
                        'proposed_price' => 2000000,
                        'status' => 'Pending',
                    ],
                    [
                        'freelancer' => $freelancerPool->get(2),
                        'proposal' => 'Saya bisa bantu dari desain sampai implementasi akhir, termasuk optimasi gambar dan integrasi tombol kontak langsung.',
                        'proposed_price' => 2100000,
                        'status' => 'Pending',
                    ],
                ],
            ],
            [
                'title' => 'Desain Feed Promo Kelas Industri',
                'description' => 'Membutuhkan desainer untuk menyiapkan template feed Instagram promosi kelas industri dengan gaya visual yang konsisten untuk beberapa minggu kampanye.',
                'category' => 'Desain Grafis',
                'client' => $clientPool['secondary'],
                'budget_min' => 500000,
                'budget_max' => 950000,
                'deadline' => now()->addDays(8)->toDateString(),
                'status' => 'Open',
                'applications' => [
                    [
                        'freelancer' => $freelancerPool->get(3),
                        'proposal' => 'Saya bisa menyiapkan template feed yang konsisten, editable, dan sesuai target audience siswa maupun orang tua.',
                        'proposed_price' => 850000,
                        'status' => 'Pending',
                    ],
                    [
                        'freelancer' => $freelancerPool->get(4),
                        'proposal' => 'Saya terbiasa mengerjakan desain promosi sekolah dan siap menyusun layout yang clean dengan elemen branding yang kuat.',
                        'proposed_price' => 900000,
                        'status' => 'Pending',
                    ],
                ],
            ],
            [
                'title' => 'Setup Access Point Lab Komputer',
                'description' => 'Butuh bantuan konfigurasi access point dan segmentasi jaringan sederhana untuk laboratorium komputer agar akses internet lebih stabil saat praktik bersama.',
                'category' => 'Jaringan Komputer',
                'client' => $clientPool['third'],
                'budget_min' => 700000,
                'budget_max' => 1400000,
                'deadline' => now()->addDays(6)->toDateString(),
                'status' => 'Open',
                'applications' => [],
            ],
            [
                'title' => 'Maintenance Website Alumni',
                'description' => 'Mencari freelancer untuk maintenance website alumni selama satu bulan, termasuk backup berkala, perbaikan bug ringan, dan monitoring performa halaman utama.',
                'category' => 'IT Support',
                'client' => $clientPool['primary'],
                'budget_min' => 600000,
                'budget_max' => 1500000,
                'deadline' => now()->addDays(12)->toDateString(),
                'status' => 'Open',
                'applications' => [
                    [
                        'freelancer' => $freelancerPool->get(5),
                        'proposal' => 'Saya bisa membantu maintenance website alumni secara rutin dan membuat checklist pekerjaan mingguan agar progres mudah dipantau.',
                        'proposed_price' => 1250000,
                        'status' => 'Pending',
                    ],
                ],
            ],
            [
                'title' => 'Prototype Monitoring Suhu Server',
                'description' => 'Dibutuhkan prototype monitoring suhu ruang server menggunakan sensor sederhana dengan output dashboard dasar agar mudah dipresentasikan.',
                'category' => 'Internet of Things (IoT)',
                'client' => $clientPool['fourth'],
                'budget_min' => 1800000,
                'budget_max' => 3200000,
                'deadline' => now()->addDays(15)->toDateString(),
                'status' => 'Open',
                'applications' => [
                    [
                        'freelancer' => $freelancerPool->get(0),
                        'proposal' => 'Saya siap membuat prototype monitoring suhu beserta dashboard pembacaan real-time untuk kebutuhan demo internal.',
                        'proposed_price' => 2900000,
                        'status' => 'Pending',
                    ],
                    [
                        'freelancer' => $freelancerPool->get(2),
                        'proposal' => 'Saya pernah mengerjakan sensor monitoring sederhana dan dapat membantu dokumentasi project agar mudah dipresentasikan.',
                        'proposed_price' => 3000000,
                        'status' => 'Pending',
                    ],
                ],
            ],
            [
                'title' => 'Video Profil Ekstrakurikuler',
                'description' => 'Mencari editor video untuk merapikan footage kegiatan ekstrakurikuler menjadi video profil singkat yang cocok dipakai di media sosial dan presentasi sekolah.',
                'category' => 'Multimedia',
                'client' => $clientPool['secondary'],
                'budget_min' => 650000,
                'budget_max' => 1300000,
                'deadline' => now()->addDays(9)->toDateString(),
                'status' => 'Closed',
                'applications' => [
                    [
                        'freelancer' => $freelancerPool->get(3),
                        'proposal' => 'Saya siap mengedit video profil ekstrakurikuler dengan pacing yang rapi, subtitle, dan transisi yang tetap ringan.',
                        'proposed_price' => 1150000,
                        'status' => 'Approved',
                    ],
                    [
                        'freelancer' => $freelancerPool->get(4),
                        'proposal' => 'Saya bisa bantu editing footage kegiatan menjadi video yang ringkas dan tetap enak ditonton untuk audiens siswa baru.',
                        'proposed_price' => 1200000,
                        'status' => 'Rejected',
                    ],
                ],
            ],
            [
                'title' => 'Dashboard Presensi Workshop',
                'description' => 'Butuh dashboard sederhana untuk presensi workshop internal lengkap dengan filter peserta dan export data agar panitia lebih mudah rekap peserta.',
                'category' => 'Web Development',
                'client' => $clientPool['third'],
                'budget_min' => 1600000,
                'budget_max' => 2800000,
                'deadline' => now()->addDays(14)->toDateString(),
                'status' => 'Closed',
                'applications' => [
                    [
                        'freelancer' => $freelancerPool->get(1),
                        'proposal' => 'Saya dapat membangun dashboard presensi workshop yang ringkas, mudah dipakai panitia, dan punya ekspor data yang rapi.',
                        'proposed_price' => 2500000,
                        'status' => 'Approved',
                    ],
                    [
                        'freelancer' => $freelancerPool->get(5),
                        'proposal' => 'Saya siap membantu membuat dashboard dengan alur presensi dan filter data yang efisien untuk admin acara.',
                        'proposed_price' => 2600000,
                        'status' => 'Rejected',
                    ],
                ],
            ],
            [
                'title' => 'Poster Open Recruitment Organisasi',
                'description' => 'Perlu desain poster dan beberapa turunan konten untuk open recruitment organisasi sekolah dengan visual yang tegas dan informatif.',
                'category' => 'Desain Grafis',
                'client' => $clientPool['fourth'],
                'budget_min' => 300000,
                'budget_max' => 700000,
                'deadline' => now()->addDays(5)->toDateString(),
                'status' => 'Open',
                'applications' => [],
            ],
            [
                'title' => 'Optimasi WiFi Area Perpustakaan',
                'description' => 'Butuh analisis sederhana dan optimasi titik akses WiFi di area perpustakaan agar koneksi lebih stabil saat banyak pengunjung.',
                'category' => 'Jaringan Komputer',
                'client' => $clientPool['primary'],
                'budget_min' => 900000,
                'budget_max' => 1700000,
                'deadline' => now()->addDays(11)->toDateString(),
                'status' => 'Closed',
                'applications' => [
                    [
                        'freelancer' => $freelancerPool->get(2),
                        'proposal' => 'Saya bisa bantu audit sederhana titik akses dan memberi rekomendasi optimasi kanal, posisi, serta pembagian jaringan tamu.',
                        'proposed_price' => 1500000,
                        'status' => 'Rejected',
                    ],
                ],
            ],
            [
                'title' => 'Motion Graphic Video Sambutan',
                'description' => 'Mencari freelancer untuk membuat motion graphic sederhana sebagai pembuka video sambutan kepala sekolah untuk acara presentasi mitra.',
                'category' => 'Multimedia',
                'client' => $clientPool['third'],
                'budget_min' => 550000,
                'budget_max' => 1100000,
                'deadline' => now()->addDays(7)->toDateString(),
                'status' => 'Open',
                'applications' => [
                    [
                        'freelancer' => $freelancerPool->get(4),
                        'proposal' => 'Saya siap membuat motion graphic pembuka yang formal, ringkas, dan sesuai durasi presentasi acara resmi.',
                        'proposed_price' => 980000,
                        'status' => 'Pending',
                    ],
                ],
            ],
        ];

        foreach ($lokerCatalog as $item) {
            $category = $categoryModels->get($item['category']);

            if (!$category || !$item['client']) {
                continue;
            }

            $loker = Loker::updateOrCreate(
                [
                    'client_id' => $item['client']->id,
                    'title' => $item['title'],
                ],
                [
                    'category_id' => $category->id,
                    'description' => $item['description'],
                    'budget_min' => $item['budget_min'],
                    'budget_max' => $item['budget_max'],
                    'deadline' => $item['deadline'],
                    'status' => $item['status'],
                ]
            );

            $this->seedLokerApplications($loker, $item['applications']);
        }
    }

    private function seedLokerApplications(Loker $loker, array $applications): void
    {
        foreach ($applications as $item) {
            $freelancer = $item['freelancer'] ?? null;

            if (!$freelancer) {
                continue;
            }

            $application = LokerApplication::updateOrCreate(
                [
                    'loker_id' => $loker->id,
                    'freelancer_id' => $freelancer->id,
                ],
                [
                    'proposal' => $item['proposal'],
                    'proposed_price' => $item['proposed_price'],
                    'status' => $item['status'],
                ]
            );

            if ($item['status'] === 'Approved') {
                Order::updateOrCreate(
                    [
                        'loker_application_id' => $application->id,
                    ],
                    [
                        'service_id' => null,
                        'client_id' => $loker->client_id,
                        'freelancer_id' => $freelancer->id,
                        'brief' => $loker->title . ' - ' . $loker->description,
                        'status' => 'Pending',
                        'agreed_price' => $item['proposed_price'],
                        'deadline' => $loker->deadline,
                    ]
                );
            }
        }
    }
}
