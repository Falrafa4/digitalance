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
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedAdministrators();

        [$clients, $freelancers] = $this->seedAccounts();
        $categories = $this->seedCategories();
        $services = $this->seedServices($categories, $freelancers);

        $this->seedPortofolios($services);
        $this->seedOrderStories($services, $clients);
        $this->seedLokers($categories, $clients, $freelancers);
    }

    private function seedAdministrators(): void
    {
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
    }

    private function seedAccounts(): array
    {
        $clientDefinitions = [
            'main_client' => [
                'name' => 'Fal Rafa',
                'email' => 'client1@email.com',
                'password' => bcrypt('password'),
                'phone' => '081234567801',
                'profile_photo' => 'profiles/placeholder.webp',
            ],
            'school_ops' => [
                'name' => 'Rina Mahendra',
                'email' => 'rina.mahendra@email.com',
                'password' => bcrypt('password'),
                'phone' => '081234567802',
                'profile_photo' => 'profiles/placeholder.webp',
            ],
            'umkm_owner' => [
                'name' => 'Bagas Saputra',
                'email' => 'bagas.saputra@email.com',
                'password' => bcrypt('password'),
                'phone' => '081234567803',
                'profile_photo' => 'profiles/placeholder.webp',
            ],
            'event_coord' => [
                'name' => 'Nadia Lestari',
                'email' => 'nadia.lestari@email.com',
                'password' => bcrypt('password'),
                'phone' => '081234567804',
                'profile_photo' => 'profiles/placeholder.webp',
            ],
            'library_team' => [
                'name' => 'Fahmi Kurniawan',
                'email' => 'fahmi.kurniawan@email.com',
                'password' => bcrypt('password'),
                'phone' => '081234567805',
                'profile_photo' => 'profiles/placeholder.webp',
            ],
        ];

        $studentDefinitions = [
            'main_freelancer' => [
                'nis' => '990000001',
                'name' => 'Syarivatun Nisa',
                'email' => 'freelancer1@email.com',
                'phone' => '081300000001',
                'class' => 'XI SIJA 2',
                'major' => 'SIJA',
                'is_registered' => true,
                'avatar' => null,
            ],
            'designer' => [
                'nis' => '990000002',
                'name' => 'Nabila Putri',
                'email' => 'nabila.putri@email.com',
                'phone' => '081300000002',
                'class' => 'XIII SIJA 2',
                'major' => 'SIJA',
                'is_registered' => true,
                'avatar' => null,
            ],
            'network_engineer' => [
                'nis' => '990000003',
                'name' => 'Dimas Prakoso',
                'email' => 'dimas.prakoso@email.com',
                'phone' => '081300000003',
                'class' => 'XII TJAT 1',
                'major' => 'TJAT',
                'is_registered' => true,
                'avatar' => null,
            ],
            'it_support' => [
                'nis' => '990000004',
                'name' => 'Salsa Maharani',
                'email' => 'salsa.maharani@email.com',
                'phone' => '081300000004',
                'class' => 'XII SIJA 1',
                'major' => 'SIJA',
                'is_registered' => true,
                'avatar' => null,
            ],
            'multimedia' => [
                'nis' => '990000005',
                'name' => 'Galang Ramadhan',
                'email' => 'galang.ramadhan@email.com',
                'phone' => '081300000005',
                'class' => 'XIII TJAT 1',
                'major' => 'TJAT',
                'is_registered' => true,
                'avatar' => null,
            ],
        ];

        $freelancerDefinitions = [
            'main_freelancer' => [
                'bio' => 'Freelancer web developer yang fokus pada landing page promosi, dashboard admin, dan website profil untuk sekolah maupun UMKM.',
                'password' => bcrypt('password'),
                'status' => 'Approved',
                'profile_photo' => 'profiles/placeholder.webp',
            ],
            'designer' => [
                'bio' => 'Desainer grafis yang terbiasa membuat feed sosial media, poster event, dan materi branding yang siap dipresentasikan.',
                'password' => bcrypt('password'),
                'status' => 'Approved',
                'profile_photo' => 'profiles/placeholder.webp',
            ],
            'network_engineer' => [
                'bio' => 'Freelancer bidang jaringan komputer untuk setup access point, troubleshooting koneksi, dan optimasi WiFi area kerja.',
                'password' => bcrypt('password'),
                'status' => 'Approved',
                'profile_photo' => 'profiles/placeholder.webp',
            ],
            'it_support' => [
                'bio' => 'Spesialis IT support untuk maintenance website, instalasi aplikasi, dan pendampingan teknis operasional harian.',
                'password' => bcrypt('password'),
                'status' => 'Approved',
                'profile_photo' => 'profiles/placeholder.webp',
            ],
            'multimedia' => [
                'bio' => 'Editor multimedia untuk video promosi, motion graphic sederhana, dan dokumentasi visual kebutuhan presentasi.',
                'password' => bcrypt('password'),
                'status' => 'Approved',
                'profile_photo' => 'profiles/placeholder.webp',
            ],
        ];

        $clients = [];

        foreach ($clientDefinitions as $key => $clientData) {
            $clients[$key] = Client::updateOrCreate(
                ['email' => $clientData['email']],
                $clientData
            );
        }

        $students = [];

        foreach ($studentDefinitions as $key => $studentData) {
            $students[$key] = SkomdaStudent::updateOrCreate(
                ['nis' => $studentData['nis']],
                $studentData
            );
        }

        $freelancers = [];

        foreach ($freelancerDefinitions as $key => $freelancerData) {
            $freelancers[$key] = Freelancer::updateOrCreate(
                ['student_id' => $students[$key]->id],
                $freelancerData
            );
        }

        return [$clients, $freelancers];
    }

    private function seedCategories(): array
    {
        $categories = [
            'Web Development' => 'Pembuatan website, landing page, dashboard, dan integrasi fitur backend.',
            'Desain Grafis' => 'Desain konten visual untuk promosi, branding, dan kebutuhan media sosial.',
            'Jaringan Komputer' => 'Instalasi, konfigurasi, dan troubleshooting jaringan untuk sekolah maupun UMKM.',
            'IT Support' => 'Dukungan teknis perangkat dan software, termasuk maintenance berkala.',
            'Internet of Things (IoT)' => 'Perancangan solusi IoT sederhana untuk monitoring dan otomasi perangkat.',
            'Multimedia' => 'Produksi konten multimedia dasar untuk kebutuhan dokumentasi dan promosi.',
        ];

        $categoryModels = [];

        foreach ($categories as $name => $description) {
            $categoryModels[$name] = ServiceCategory::updateOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'is_active' => true,
                ]
            );
        }

        return $categoryModels;
    }

    private function seedServices(array $categories, array $freelancers): array
    {
        $serviceDefinitions = [
            'landing_page_ppdb' => [
                'title' => 'Landing Page PPDB Sekolah',
                'category' => 'Web Development',
                'freelancer' => 'main_freelancer',
                'description' => 'Landing page promosi PPDB yang responsif, mudah dibaca di mobile, dan memiliki CTA langsung ke WhatsApp admin sekolah.',
                'price_min' => 850000,
                'price_max' => 1800000,
                'delivery_time' => 7,
            ],
            'dashboard_workshop' => [
                'title' => 'Dashboard Presensi Workshop',
                'category' => 'Web Development',
                'freelancer' => 'main_freelancer',
                'description' => 'Dashboard admin sederhana untuk presensi workshop, filter peserta, dan rekap data yang siap dipresentasikan.',
                'price_min' => 1500000,
                'price_max' => 2800000,
                'delivery_time' => 12,
            ],
            'feed_industri' => [
                'title' => 'Paket Feed Promosi Kelas Industri',
                'category' => 'Desain Grafis',
                'freelancer' => 'designer',
                'description' => 'Paket desain feed sosial media yang konsisten untuk kampanye promosi kelas industri dan program sekolah.',
                'price_min' => 450000,
                'price_max' => 900000,
                'delivery_time' => 5,
            ],
            'poster_event' => [
                'title' => 'Poster Event Sekolah dan Organisasi',
                'category' => 'Desain Grafis',
                'freelancer' => 'designer',
                'description' => 'Desain poster dan materi turunan untuk seminar, bazar, open recruitment, atau agenda promosi sekolah.',
                'price_min' => 250000,
                'price_max' => 650000,
                'delivery_time' => 3,
            ],
            'wifi_lab' => [
                'title' => 'Optimasi WiFi Laboratorium Komputer',
                'category' => 'Jaringan Komputer',
                'freelancer' => 'network_engineer',
                'description' => 'Audit sederhana, konfigurasi access point, dan rekomendasi optimasi jaringan agar koneksi lebih stabil saat praktikum.',
                'price_min' => 700000,
                'price_max' => 1500000,
                'delivery_time' => 4,
            ],
            'maintenance_site' => [
                'title' => 'Maintenance Website Alumni',
                'category' => 'IT Support',
                'freelancer' => 'it_support',
                'description' => 'Perawatan website bulanan meliputi backup, update minor, monitoring error, dan perbaikan ringan halaman utama.',
                'price_min' => 600000,
                'price_max' => 1400000,
                'delivery_time' => 30,
            ],
            'iot_monitoring' => [
                'title' => 'Prototype Monitoring Suhu Ruangan',
                'category' => 'Internet of Things (IoT)',
                'freelancer' => 'network_engineer',
                'description' => 'Prototype monitoring suhu dan kelembapan ruangan dengan pembacaan data sederhana untuk kebutuhan demo internal.',
                'price_min' => 1800000,
                'price_max' => 3200000,
                'delivery_time' => 14,
            ],
            'video_profile' => [
                'title' => 'Video Profil Ekstrakurikuler',
                'category' => 'Multimedia',
                'freelancer' => 'multimedia',
                'description' => 'Editing video profil singkat untuk media sosial dan presentasi mitra dengan subtitle serta motion sederhana.',
                'price_min' => 650000,
                'price_max' => 1300000,
                'delivery_time' => 6,
            ],
        ];

        $services = [];

        foreach ($serviceDefinitions as $key => $serviceData) {
            $services[$key] = Service::updateOrCreate(
                ['title' => $serviceData['title']],
                [
                    'category_id' => $categories[$serviceData['category']]->id,
                    'freelancer_id' => $freelancers[$serviceData['freelancer']]->id,
                    'description' => $serviceData['description'],
                    'price_min' => $serviceData['price_min'],
                    'price_max' => $serviceData['price_max'],
                    'delivery_time' => $serviceData['delivery_time'],
                    'status' => 'Approved',
                ]
            );
        }

        return $services;
    }

    private function seedPortofolios(array $services): void
    {
        $portofolioDefinitions = [
            [
                'service' => 'landing_page_ppdb',
                'title' => 'Landing Page PPDB Modern',
                'description' => 'Tampilan landing page penerimaan siswa baru dengan hero section, CTA WhatsApp, dan alur informasi yang ringkas.',
                'media_url' => 'portofolios/portofolio-1.webp',
            ],
            [
                'service' => 'dashboard_workshop',
                'title' => 'Dashboard Rekap Peserta Workshop',
                'description' => 'Contoh dashboard admin untuk filter kehadiran, rekap peserta, dan tampilan statistik acara.',
                'media_url' => 'portofolios/portofolio-2.webp',
            ],
            [
                'service' => 'feed_industri',
                'title' => 'Template Feed Kampanye Pendidikan',
                'description' => 'Seri desain feed Instagram untuk kampanye kelas industri yang konsisten dan siap diadaptasi ke beberapa postingan.',
                'media_url' => 'portofolios/portofolio-3.webp',
            ],
            [
                'service' => 'poster_event',
                'title' => 'Poster Seminar dan Open Recruitment',
                'description' => 'Koleksi poster acara sekolah dengan visual tegas, informasi lengkap, dan mudah dibaca dari layar presentasi.',
                'media_url' => 'portofolios/portofolio-4.webp',
            ],
            [
                'service' => 'wifi_lab',
                'title' => 'Dokumentasi Optimasi Access Point',
                'description' => 'Dokumentasi hasil audit jaringan laboratorium serta penataan ulang access point untuk area praktikum.',
                'media_url' => 'portofolios/portofolio-1.webp',
            ],
            [
                'service' => 'maintenance_site',
                'title' => 'Checklist Maintenance Website Sekolah',
                'description' => 'Contoh hasil maintenance berkala berupa update plugin, backup, dan laporan perbaikan bug prioritas.',
                'media_url' => 'portofolios/portofolio-2.webp',
            ],
            [
                'service' => 'iot_monitoring',
                'title' => 'Prototype Dashboard Monitoring Suhu',
                'description' => 'Visual prototype monitoring suhu ruangan dengan tampilan data ringkas untuk presentasi demo produk.',
                'media_url' => 'portofolios/portofolio-3.webp',
            ],
            [
                'service' => 'video_profile',
                'title' => 'Video Profil Organisasi Sekolah',
                'description' => 'Cuplikan editing video profil ekstrakurikuler dengan subtitle, musik latar, dan transisi ringan.',
                'media_url' => 'portofolios/portofolio-4.webp',
            ],
        ];

        foreach ($portofolioDefinitions as $item) {
            $service = $services[$item['service']] ?? null;

            if (!$service) {
                continue;
            }

            Portofolio::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'title' => $item['title'],
                ],
                [
                    'description' => $item['description'],
                    'media_url' => $item['media_url'],
                ]
            );
        }
    }

    private function seedOrderStories(array $services, array $clients): void
    {
        $stories = [
            [
                'service' => 'landing_page_ppdb',
                'client' => 'main_client',
                'brief' => 'Membutuhkan landing page PPDB yang formal, responsif, dan memiliki tombol pendaftaran cepat untuk kebutuhan presentasi ke mitra sekolah.',
                'status' => 'Completed',
                'agreed_price' => 1600000,
                'deadline' => now()->subDays(10)->toDateString(),
                'offers' => [
                    [
                        'title' => 'Penawaran Final Landing Page PPDB',
                        'description' => 'Termasuk desain halaman utama, integrasi CTA WhatsApp, optimasi mobile, dan handover file final.',
                        'offered_price' => 1600000,
                        'deadline' => now()->subDays(15)->toDateString(),
                        'status' => 'Accepted',
                    ],
                ],
                'transactions' => [
                    [
                        'amount' => 1600000,
                        'type' => 'Full',
                        'status' => 'Paid',
                    ],
                ],
                'result' => [
                    'file_url' => 'https://drive.google.com/file/d/1digitalance-demo-landing-page/view',
                    'result_mode' => 'link',
                    'note' => 'File final landing page, asset presentasi, dan panduan singkat penggunaan sudah diserahkan ke client.',
                    'version' => 'v1.0',
                ],
                'review' => [
                    'rating' => 5,
                    'comment' => 'Hasil sangat rapi, cepat selesai, dan cocok untuk kebutuhan demo produk kami.',
                ],
            ],
            [
                'service' => 'feed_industri',
                'client' => 'school_ops',
                'brief' => 'Perlu 12 template feed promosi kelas industri dengan tone visual yang seragam, editable, dan siap dipakai untuk kampanye 3 minggu.',
                'status' => 'Revision',
                'agreed_price' => 850000,
                'deadline' => now()->addDays(5)->toDateString(),
                'offers' => [
                    [
                        'title' => 'Penawaran Paket Feed Kelas Industri',
                        'description' => 'Paket desain feed sosial media dengan file editable, 2 kali revisi, dan panduan warna dasar.',
                        'offered_price' => 850000,
                        'deadline' => now()->addDays(7)->toDateString(),
                        'status' => 'Accepted',
                    ],
                ],
                'negotiations' => [
                    [
                        'sender' => 'client',
                        'message' => 'Bisa ditambahkan satu format carousel agar materi promosi lebih fleksibel?',
                        'proposed_price' => 800000,
                        'reason' => 'Penyesuaian ruang lingkup',
                        'description' => 'Client meminta tambahan satu layout carousel untuk konten pengumuman mingguan.',
                        'status' => 'Pending',
                    ],
                    [
                        'sender' => 'freelancer',
                        'message' => 'Bisa, saya tambahkan layout carousel dengan sedikit penyesuaian jadwal pengerjaan.',
                        'proposed_price' => 850000,
                        'reason' => 'Respon penawaran',
                        'description' => 'Freelancer menyetujui tambahan carousel dengan tetap menjaga kualitas template utama.',
                        'status' => 'Approved',
                    ],
                    [
                        'sender' => 'client',
                        'message' => 'Baik, lanjutkan sesuai revisi terakhir dan prioritaskan konten untuk minggu pertama.',
                        'proposed_price' => 850000,
                        'reason' => 'Konfirmasi revisi',
                        'description' => 'Client menyetujui penyesuaian revisi dan meminta prioritas untuk materi launch awal.',
                        'status' => 'Approved',
                    ],
                ],
                'transactions' => [
                    [
                        'amount' => 425000,
                        'type' => 'DP',
                        'status' => 'Paid',
                    ],
                ],
                'result' => [
                    'file_url' => 'https://drive.google.com/file/d/1digitalance-demo-feed-revisi/view',
                    'result_mode' => 'link',
                    'note' => 'Versi revisi pertama sudah dikirim dalam bentuk link presentasi dan file desain yang bisa diulas client.',
                    'version' => 'v1.1',
                ],
            ],
            [
                'service' => 'maintenance_site',
                'client' => 'umkm_owner',
                'brief' => 'Website alumni butuh maintenance 1 bulan, backup mingguan, dan monitoring error pada halaman berita serta formulir kontak.',
                'status' => 'In Progress',
                'agreed_price' => 1200000,
                'deadline' => now()->addDays(14)->toDateString(),
                'offers' => [
                    [
                        'title' => 'Penawaran Maintenance Website Bulanan',
                        'description' => 'Maintenance 1 bulan mencakup backup berkala, update minor, monitoring performa, dan perbaikan bug ringan.',
                        'offered_price' => 1200000,
                        'deadline' => now()->addDays(16)->toDateString(),
                        'status' => 'Accepted',
                    ],
                ],
                'transactions' => [
                    [
                        'amount' => 600000,
                        'type' => 'DP',
                        'status' => 'Paid',
                    ],
                ],
            ],
            [
                'service' => 'video_profile',
                'client' => 'event_coord',
                'brief' => 'Membutuhkan video profil ekstrakurikuler untuk presentasi sponsor, durasi singkat, ada subtitle, dan ritme edit yang formal.',
                'status' => 'Negotiated',
                'agreed_price' => null,
                'deadline' => now()->addDays(10)->toDateString(),
                'offers' => [
                    [
                        'title' => 'Penawaran Awal Video Profil',
                        'description' => 'Editing video profil dengan subtitle, transisi ringan, dan satu versi rasio presentasi.',
                        'offered_price' => 1100000,
                        'deadline' => now()->addDays(11)->toDateString(),
                        'status' => 'Sent',
                    ],
                ],
                'negotiations' => [
                    [
                        'sender' => 'client',
                        'message' => 'Apakah durasi video bisa dipersingkat menjadi 90 detik tanpa mengurangi bagian wawancara utama?',
                        'proposed_price' => 950000,
                        'reason' => 'Penyesuaian durasi',
                        'description' => 'Client mengusulkan durasi yang lebih pendek agar cocok diputar saat pitching.',
                        'status' => 'Pending',
                    ],
                    [
                        'sender' => 'freelancer',
                        'message' => 'Bisa, saya akan padatkan alur edit dan tetap mempertahankan poin wawancara yang paling penting.',
                        'proposed_price' => 1000000,
                        'reason' => 'Respon negosiasi',
                        'description' => 'Freelancer menurunkan biaya dengan konsekuensi memilih potongan footage yang lebih fokus.',
                        'status' => 'Pending',
                    ],
                    [
                        'sender' => 'client',
                        'message' => 'Baik, saya diskusikan dulu dengan tim dan kemungkinan akan lanjut di angka itu.',
                        'proposed_price' => 1000000,
                        'reason' => 'Menunggu keputusan',
                        'description' => 'Negosiasi masih berjalan dan client sedang meminta persetujuan internal.',
                        'status' => 'Pending',
                    ],
                ],
            ],
            [
                'service' => 'wifi_lab',
                'client' => 'library_team',
                'brief' => 'Butuh optimasi WiFi area perpustakaan dan ruang baca, tetapi anggaran akhirnya ditahan karena prioritas pindah ke pengadaan perangkat baru.',
                'status' => 'Cancelled',
                'agreed_price' => null,
                'deadline' => now()->addDays(9)->toDateString(),
                'offers' => [
                    [
                        'title' => 'Penawaran Audit dan Optimasi WiFi',
                        'description' => 'Audit posisi access point, pengaturan kanal, dan rekomendasi optimasi sederhana untuk area perpustakaan.',
                        'offered_price' => 950000,
                        'deadline' => now()->addDays(10)->toDateString(),
                        'status' => 'Rejected',
                    ],
                ],
                'negotiations' => [
                    [
                        'sender' => 'client',
                        'message' => 'Sementara project kami tunda karena anggaran dialihkan ke pembelian perangkat lain.',
                        'proposed_price' => null,
                        'reason' => 'Project dibatalkan',
                        'description' => 'Client menutup project sebelum ada kesepakatan harga final.',
                        'status' => 'Rejected',
                    ],
                ],
            ],
        ];

        foreach ($stories as $story) {
            $service = $services[$story['service']] ?? null;
            $client = $clients[$story['client']] ?? null;

            if (!$service || !$client) {
                continue;
            }

            $order = Order::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'client_id' => $client->id,
                    'brief' => $story['brief'],
                ],
                [
                    'freelancer_id' => $service->freelancer_id,
                    'status' => $story['status'],
                    'agreed_price' => $story['agreed_price'],
                    'deadline' => $story['deadline'],
                ]
            );

            $this->seedOffers($order, $story['offers'] ?? []);
            $this->seedNegotiations($order, $story['negotiations'] ?? []);
            $this->seedTransactions($order, $story['transactions'] ?? []);

            if (isset($story['result'])) {
                Result::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'version' => $story['result']['version'],
                    ],
                    [
                        'file_url' => $story['result']['file_url'],
                        'result_mode' => $story['result']['result_mode'],
                        'note' => $story['result']['note'],
                    ]
                );
            }

            if (isset($story['review'])) {
                Review::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'rating' => $story['review']['rating'],
                        'comment' => $story['review']['comment'],
                    ]
                );
            }
        }
    }

    private function seedOffers(Order $order, array $offers): void
    {
        foreach ($offers as $offerData) {
            Offer::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'title' => $offerData['title'],
                ],
                [
                    'description' => $offerData['description'],
                    'offered_price' => $offerData['offered_price'],
                    'deadline' => $offerData['deadline'],
                    'status' => $offerData['status'],
                ]
            );
        }
    }

    private function seedNegotiations(Order $order, array $negotiations): void
    {
        foreach ($negotiations as $negotiationData) {
            Negotiation::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'sender' => $negotiationData['sender'],
                    'message' => $negotiationData['message'],
                ],
                [
                    'proposed_price' => $negotiationData['proposed_price'],
                    'reason' => $negotiationData['reason'],
                    'description' => $negotiationData['description'],
                    'status' => $negotiationData['status'],
                ]
            );
        }
    }

    private function seedTransactions(Order $order, array $transactions): void
    {
        foreach ($transactions as $transactionData) {
            Transaction::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'type' => $transactionData['type'],
                ],
                [
                    'amount' => $transactionData['amount'],
                    'status' => $transactionData['status'],
                ]
            );
        }
    }

    private function seedLokers(array $categories, array $clients, array $freelancers): void
    {
        $lokerDefinitions = [
            [
                'title' => 'Landing Page Program Magang Sekolah',
                'client' => 'school_ops',
                'category' => 'Web Development',
                'description' => 'Membutuhkan landing page sederhana untuk promosi program magang sekolah dengan CTA konsultasi cepat.',
                'budget_min' => 1200000,
                'budget_max' => 2200000,
                'deadline' => now()->addDays(8)->toDateString(),
                'status' => 'Open',
                'applications' => [
                    [
                        'freelancer' => 'main_freelancer',
                        'proposal' => 'Saya siap membuat landing page yang ringkas, cepat dimuat, dan mudah dipresentasikan kepada calon mitra.',
                        'proposed_price' => 1800000,
                        'status' => 'Pending',
                    ],
                    [
                        'freelancer' => 'it_support',
                        'proposal' => 'Saya bisa bantu implementasi landing page sekaligus dokumentasi agar tim internal mudah melanjutkan update konten.',
                        'proposed_price' => 1950000,
                        'status' => 'Pending',
                    ],
                ],
            ],
            [
                'title' => 'Desain Poster Open Recruitment Organisasi',
                'client' => 'event_coord',
                'category' => 'Desain Grafis',
                'description' => 'Perlu poster utama dan turunan konten untuk open recruitment organisasi dengan visual yang informatif.',
                'budget_min' => 300000,
                'budget_max' => 650000,
                'deadline' => now()->addDays(6)->toDateString(),
                'status' => 'Open',
                'applications' => [
                    [
                        'freelancer' => 'designer',
                        'proposal' => 'Saya bisa menyiapkan konsep poster utama beserta turunan story dan feed agar kampanye lebih konsisten.',
                        'proposed_price' => 550000,
                        'status' => 'Pending',
                    ],
                ],
            ],
            [
                'title' => 'Setup Access Point Area Perpustakaan',
                'client' => 'library_team',
                'category' => 'Jaringan Komputer',
                'description' => 'Membutuhkan bantuan konfigurasi access point dan optimasi cakupan sinyal untuk area perpustakaan.',
                'budget_min' => 850000,
                'budget_max' => 1600000,
                'deadline' => now()->addDays(7)->toDateString(),
                'status' => 'Closed',
                'applications' => [
                    [
                        'freelancer' => 'network_engineer',
                        'proposal' => 'Saya bisa audit titik akses yang ada, mengatur kanal terbaik, dan memberi dokumentasi rekomendasi lanjutan.',
                        'proposed_price' => 1450000,
                        'status' => 'Approved',
                    ],
                    [
                        'freelancer' => 'main_freelancer',
                        'proposal' => 'Saya dapat membantu koordinasi setup dasar dan dokumentasi hasil pengujian area perpustakaan.',
                        'proposed_price' => 1500000,
                        'status' => 'Rejected',
                    ],
                ],
            ],
            [
                'title' => 'Maintenance Website Informasi Alumni',
                'client' => 'main_client',
                'category' => 'IT Support',
                'description' => 'Butuh maintenance ringan dan backup berkala untuk website informasi alumni selama masa penerimaan anggota baru.',
                'budget_min' => 700000,
                'budget_max' => 1300000,
                'deadline' => now()->addDays(9)->toDateString(),
                'status' => 'Closed',
                'applications' => [
                    [
                        'freelancer' => 'it_support',
                        'proposal' => 'Saya siap menangani maintenance bulanan dan membuat checklist pekerjaan mingguan untuk tim client.',
                        'proposed_price' => 1150000,
                        'status' => 'Approved',
                    ],
                ],
            ],
            [
                'title' => 'Video Highlight Kegiatan Workshop',
                'client' => 'umkm_owner',
                'category' => 'Multimedia',
                'description' => 'Membutuhkan editor video untuk merapikan footage workshop menjadi video highlight singkat untuk materi pitching.',
                'budget_min' => 650000,
                'budget_max' => 1200000,
                'deadline' => now()->addDays(10)->toDateString(),
                'status' => 'Open',
                'applications' => [],
            ],
        ];

        foreach ($lokerDefinitions as $item) {
            $client = $clients[$item['client']] ?? null;
            $category = $categories[$item['category']] ?? null;

            if (!$client || !$category) {
                continue;
            }

            $loker = Loker::updateOrCreate(
                [
                    'client_id' => $client->id,
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

            $this->seedLokerApplications($loker, $item['applications'], $freelancers);
        }
    }

    private function seedLokerApplications(Loker $loker, array $applications, array $freelancers): void
    {
        foreach ($applications as $item) {
            $freelancer = $freelancers[$item['freelancer']] ?? null;

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
