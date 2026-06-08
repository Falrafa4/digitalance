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

    private const AI_POOL_MARKER = 'AI_POOL:';
    private const AI_SIJA_COUNT = 36;
    private const AI_TJAT_COUNT = 36;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedAdministrators();

        [$clients, $baseFreelancers] = $this->seedBaseAccounts();
        $categories = $this->seedCategories();
        $aiPool = $this->seedAiFreelancerPool($categories, $baseFreelancers['main_freelancer']->student_id);

        $freelancers = array_merge($baseFreelancers, $aiPool['aliases'], $aiPool['freelancers']);
        $curatedServices = $this->seedCuratedServices($categories, $freelancers);

        $this->seedCuratedPortofolios($curatedServices);
        $this->seedAiSignalLayer($aiPool['records'], $clients);
        $this->seedOrderStories($curatedServices, $clients);
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

    private function seedBaseAccounts(): array
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

        $students = [
            'main_freelancer' => SkomdaStudent::updateOrCreate(
                ['nis' => '990000001'],
                [
                    'name' => 'Syarivatun Nisa',
                    'email' => 'freelancer1@email.com',
                    'phone' => '081300000001',
                    'class' => 'XI SIJA 2',
                    'major' => 'SIJA',
                    'is_registered' => true,
                    'avatar' => null,
                ]
            ),
        ];

        $clients = [];

        foreach ($clientDefinitions as $key => $clientData) {
            $clients[$key] = Client::updateOrCreate(
                ['email' => $clientData['email']],
                $clientData
            );
        }

        $freelancers = [
            'main_freelancer' => Freelancer::updateOrCreate(
                ['student_id' => $students['main_freelancer']->id],
                [
                    'bio' => 'Freelancer web developer yang fokus pada landing page promosi, dashboard admin, dan website profil untuk sekolah maupun UMKM.',
                    'password' => bcrypt('password'),
                    'status' => 'Approved',
                    'profile_photo' => 'profiles/placeholder.webp',
                    'career_track' => 'Web & System Integration Engineer',
                    'career_track_status' => 'Approved',
                    'career_track_notes' => 'Demo login freelancer produksi.',
                ]
            ),
        ];

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

    private function seedAiFreelancerPool(array $categories, int $excludedStudentId): array
    {
        $selectedStudents = $this->selectAiPoolStudents($excludedStudentId);
        $archetypes = $this->aiArchetypes();

        $freelancers = [];
        $aliases = [];
        $records = [];
        $majorCounters = ['SIJA' => 0, 'TJAT' => 0];
        $globalIndex = 1;

        foreach (['SIJA', 'TJAT'] as $major) {
            foreach ($selectedStudents[$major] as $student) {
                $templates = $archetypes[$major];
                $template = $templates[$majorCounters[$major] % count($templates)];
                $majorCounters[$major]++;

                $student->update([
                    'is_registered' => true,
                ]);

                $freelancer = Freelancer::updateOrCreate(
                    ['student_id' => $student->id],
                    [
                        'bio' => $template['bio'],
                        'password' => bcrypt('password'),
                        'status' => 'Approved',
                        'profile_photo' => 'profiles/placeholder.webp',
                        'career_track' => $template['career_track'],
                        'career_track_status' => 'Approved',
                        'career_track_notes' => self::AI_POOL_MARKER . $template['key'],
                    ]
                );

                $serviceTitle = $template['service_title_prefix'] . ' - ' . $student->name;
                $service = Service::updateOrCreate(
                    ['title' => $serviceTitle],
                    [
                        'category_id' => $categories[$template['category']]->id,
                        'freelancer_id' => $freelancer->id,
                        'description' => $template['service_description'],
                        'price_min' => $template['price_min'],
                        'price_max' => $template['price_max'],
                        'delivery_time' => $template['delivery_time'],
                        'status' => 'Approved',
                    ]
                );

                $key = sprintf('ai_%03d', $globalIndex);
                $globalIndex++;

                $freelancers[$key] = $freelancer;
                $records[$key] = [
                    'key' => $key,
                    'student' => $student,
                    'freelancer' => $freelancer,
                    'service' => $service,
                    'template' => $template,
                ];

                if (!empty($template['alias']) && !isset($aliases[$template['alias']])) {
                    $aliases[$template['alias']] = $freelancer;
                }
            }
        }

        $this->ensureFreelancerAliases($aliases, $records);

        return [
            'freelancers' => $freelancers,
            'aliases' => $aliases,
            'records' => $records,
        ];
    }

    private function selectAiPoolStudents(int $excludedStudentId): array
    {
        return [
            'SIJA' => $this->selectStudentsByMajor('SIJA', self::AI_SIJA_COUNT, $excludedStudentId),
            'TJAT' => $this->selectStudentsByMajor('TJAT', self::AI_TJAT_COUNT, $excludedStudentId),
        ];
    }

    private function selectStudentsByMajor(string $major, int $limit, int $excludedStudentId): array
    {
        $existingPoolIds = Freelancer::query()
            ->where('career_track_notes', 'like', self::AI_POOL_MARKER . '%')
            ->pluck('student_id')
            ->filter()
            ->all();

        $baseQuery = SkomdaStudent::query()
            ->where('major', $major)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('id', '!=', $excludedStudentId);

        $selected = (clone $baseQuery)
            ->when(!empty($existingPoolIds), fn($query) => $query->whereIn('id', $existingPoolIds), fn($query) => $query->whereRaw('1 = 0'))
            ->orderBy('class')
            ->orderBy('name')
            ->get();

        $remaining = $limit - $selected->count();

        if ($remaining > 0) {
            $newCandidates = (clone $baseQuery)
                ->whereNotIn('id', $selected->pluck('id'))
                ->whereDoesntHave('freelancer')
                ->orderBy('class')
                ->orderBy('name')
                ->limit($remaining)
                ->get();

            $selected = $selected->concat($newCandidates);
            $remaining = $limit - $selected->count();
        }

        if ($remaining > 0) {
            $fallbackCandidates = (clone $baseQuery)
                ->whereNotIn('id', $selected->pluck('id'))
                ->orderBy('class')
                ->orderBy('name')
                ->limit($remaining)
                ->get();

            $selected = $selected->concat($fallbackCandidates);
        }

        return $selected->take($limit)->values()->all();
    }

    private function ensureFreelancerAliases(array &$aliases, array $records): void
    {
        $fallbacks = [
            'designer' => 'Desain Grafis',
            'network_engineer' => 'Jaringan Komputer',
            'it_support' => 'IT Support',
            'iot_specialist' => 'Internet of Things (IoT)',
            'multimedia' => 'Multimedia',
        ];

        foreach ($fallbacks as $alias => $categoryName) {
            if (isset($aliases[$alias])) {
                continue;
            }

            foreach ($records as $record) {
                if (($record['template']['category'] ?? null) === $categoryName) {
                    $aliases[$alias] = $record['freelancer'];
                    break;
                }
            }
        }
    }

    private function aiArchetypes(): array
    {
        return [
            'SIJA' => [
                [
                    'key' => 'laravel_web_dev',
                    'alias' => null,
                    'category' => 'Web Development',
                    'career_track' => 'Web & System Integration Engineer',
                    'bio' => 'Freelancer SIJA yang fokus pada Laravel, MySQL, landing page responsif, dan pengembangan fitur website sekolah maupun UMKM.',
                    'service_title_prefix' => 'Jasa Laravel & MySQL untuk Website',
                    'service_description' => 'Mengerjakan website Laravel, dashboard admin, integrasi MySQL, formulir pendaftaran, dan landing page promosi yang responsif.',
                    'price_min' => 900000,
                    'price_max' => 2100000,
                    'delivery_time' => 8,
                    'portfolio_title_prefix' => 'Implementasi Laravel untuk Website',
                    'portfolio_description' => 'Contoh pengerjaan website Laravel dengan struktur database MySQL, form input, dan dashboard monitoring sederhana.',
                    'completed_brief' => 'Membutuhkan developer Laravel yang paham MySQL untuk membangun fitur website internal dan landing page informatif.',
                    'review_comment' => 'Implementasi Laravel dan database-nya rapi, komunikasi enak, dan progres jelas.',
                ],
                [
                    'key' => 'dashboard_admin',
                    'alias' => null,
                    'category' => 'Web Development',
                    'career_track' => 'Web Operations & Dashboard Specialist',
                    'bio' => 'Terbiasa membuat dashboard admin, rekap data, fitur filter, dan tampilan monitoring untuk kebutuhan operasional harian.',
                    'service_title_prefix' => 'Dashboard Admin & Rekap Data',
                    'service_description' => 'Pembuatan dashboard admin, tabel data, filter pencarian, statistik operasional, dan tampilan rekap untuk kebutuhan internal.',
                    'price_min' => 1100000,
                    'price_max' => 2400000,
                    'delivery_time' => 10,
                    'portfolio_title_prefix' => 'Dashboard Operasional Internal',
                    'portfolio_description' => 'Contoh dashboard admin untuk rekap peserta, monitoring progress, dan filter data presentasi.',
                    'completed_brief' => 'Perlu dashboard admin sederhana untuk rekap peserta, statistik kegiatan, dan export laporan dasar.',
                    'review_comment' => 'Dashboard yang dibuat mudah dipakai dan cocok untuk kebutuhan operasional tim kami.',
                ],
                [
                    'key' => 'api_backend',
                    'alias' => null,
                    'category' => 'Web Development',
                    'career_track' => 'API & Backend Integration Specialist',
                    'bio' => 'Fokus pada backend Laravel, integrasi API, validasi data, dan pengelolaan proses bisnis berbasis database.',
                    'service_title_prefix' => 'Backend Laravel & Integrasi API',
                    'service_description' => 'Membantu pengembangan backend Laravel, validasi data, endpoint API sederhana, dan integrasi proses bisnis dengan database.',
                    'price_min' => 1200000,
                    'price_max' => 2500000,
                    'delivery_time' => 11,
                    'portfolio_title_prefix' => 'Backend Integrasi Data',
                    'portfolio_description' => 'Contoh backend untuk integrasi data internal, autentikasi dasar, dan manajemen proses berbasis Laravel.',
                    'completed_brief' => 'Butuh backend Laravel untuk integrasi data, validasi form, dan proses API sederhana antar modul.',
                    'review_comment' => 'Pengerjaan backend cukup solid dan membantu tim memahami alur datanya.',
                ],
                [
                    'key' => 'it_support',
                    'alias' => 'it_support',
                    'category' => 'IT Support',
                    'career_track' => 'IT Support & Operations Specialist',
                    'bio' => 'Siap membantu maintenance website, instalasi aplikasi kerja, troubleshooting software, dan pendampingan teknis pengguna.',
                    'service_title_prefix' => 'IT Support Website & Aplikasi',
                    'service_description' => 'Maintenance website, instalasi software, pengecekan bug ringan, optimasi perangkat, dan bantuan operasional harian.',
                    'price_min' => 500000,
                    'price_max' => 1200000,
                    'delivery_time' => 5,
                    'portfolio_title_prefix' => 'Maintenance Website & Sistem',
                    'portfolio_description' => 'Contoh maintenance website sekolah, backup data, dan checklist troubleshooting aplikasi kerja.',
                    'completed_brief' => 'Website organisasi memerlukan maintenance, backup data, dan troubleshooting form yang sering error.',
                    'review_comment' => 'Respons cepat dan cocok untuk kebutuhan maintenance rutin serta troubleshooting ringan.',
                ],
                [
                    'key' => 'iot_prototype',
                    'alias' => 'iot_specialist',
                    'category' => 'Internet of Things (IoT)',
                    'career_track' => 'IoT Systems Integrator',
                    'bio' => 'Freelancer SIJA yang mengerjakan prototype IoT, monitoring sensor, dashboard pembacaan data, dan otomasi sederhana.',
                    'service_title_prefix' => 'Prototype IoT Monitoring Sensor',
                    'service_description' => 'Membantu membuat prototype IoT untuk monitoring suhu, kelembapan, dan dashboard pembacaan data sederhana.',
                    'price_min' => 1500000,
                    'price_max' => 3200000,
                    'delivery_time' => 14,
                    'portfolio_title_prefix' => 'Prototype IoT Monitoring',
                    'portfolio_description' => 'Contoh prototype monitoring sensor dengan dashboard pembacaan suhu dan notifikasi sederhana.',
                    'completed_brief' => 'Dibutuhkan prototype IoT untuk monitoring sensor suhu dan dashboard data yang mudah dipresentasikan.',
                    'review_comment' => 'Prototype IoT yang dibuat cukup meyakinkan dan mudah dijelaskan saat demo.',
                ],
                [
                    'key' => 'ui_content_support',
                    'alias' => 'designer',
                    'category' => 'Desain Grafis',
                    'career_track' => 'Digital UI & Content Support Specialist',
                    'bio' => 'Terbiasa membantu desain konten digital, poster promosi, materi presentasi, dan layout visual untuk kebutuhan kampanye sekolah.',
                    'service_title_prefix' => 'Desain Poster Promosi & Konten',
                    'service_description' => 'Pengerjaan poster promosi sekolah, layout feed sosial media, materi presentasi, dan konten visual yang informatif.',
                    'price_min' => 350000,
                    'price_max' => 850000,
                    'delivery_time' => 4,
                    'portfolio_title_prefix' => 'Poster Promosi & Konten Sekolah',
                    'portfolio_description' => 'Contoh poster promosi, konten kampanye acara, dan layout visual untuk kebutuhan publikasi digital.',
                    'completed_brief' => 'Butuh desain poster promosi sekolah dan materi konten visual untuk publikasi acara serta presentasi.',
                    'review_comment' => 'Desain poster dan kontennya jelas, enak dilihat, dan cepat direvisi saat dibutuhkan.',
                ],
            ],
            'TJAT' => [
                [
                    'key' => 'network_setup',
                    'alias' => 'network_engineer',
                    'category' => 'Jaringan Komputer',
                    'career_track' => 'Network & Infrastructure Specialist',
                    'bio' => 'Freelancer TJAT untuk setup jaringan komputer, penarikan topologi dasar, konfigurasi access point, dan dokumentasi implementasi.',
                    'service_title_prefix' => 'Setup Jaringan & Access Point',
                    'service_description' => 'Membantu setup jaringan komputer, pemasangan access point, pengecekan topologi, dan konfigurasi koneksi untuk lab atau kantor kecil.',
                    'price_min' => 800000,
                    'price_max' => 1700000,
                    'delivery_time' => 6,
                    'portfolio_title_prefix' => 'Setup Jaringan & Access Point',
                    'portfolio_description' => 'Dokumentasi setup jaringan dasar, pengaturan access point, dan pembagian koneksi untuk kebutuhan sekolah.',
                    'completed_brief' => 'Perlu setup jaringan komputer dan access point untuk ruang kerja dengan pembagian akses yang stabil.',
                    'review_comment' => 'Konfigurasi jaringan rapi, penjelasan teknisnya mudah dipahami, dan hasil koneksi stabil.',
                ],
                [
                    'key' => 'wireless_optimization',
                    'alias' => null,
                    'category' => 'Jaringan Komputer',
                    'career_track' => 'Wireless Optimization Specialist',
                    'bio' => 'Fokus pada optimasi WiFi, audit sinyal, penataan channel access point, dan troubleshooting koneksi area padat pengguna.',
                    'service_title_prefix' => 'Optimasi WiFi & Sinyal Access Point',
                    'service_description' => 'Audit WiFi, optimasi access point, pengaturan channel, dan perbaikan stabilitas jaringan pada area ramai pengguna.',
                    'price_min' => 700000,
                    'price_max' => 1500000,
                    'delivery_time' => 5,
                    'portfolio_title_prefix' => 'Optimasi WiFi Area Ramai',
                    'portfolio_description' => 'Contoh optimasi access point dan analisis sinyal untuk area perpustakaan, laboratorium, atau ruang rapat.',
                    'completed_brief' => 'Butuh optimasi WiFi dan access point agar koneksi lebih stabil saat dipakai banyak pengguna sekaligus.',
                    'review_comment' => 'Optimasi WiFi terasa berdampak dan rekomendasi teknisnya masuk akal untuk kami terapkan.',
                ],
                [
                    'key' => 'router_hotspot',
                    'alias' => null,
                    'category' => 'Jaringan Komputer',
                    'career_track' => 'Router & Hotspot Configuration Specialist',
                    'bio' => 'Berpengalaman pada konfigurasi router, hotspot sederhana, pembagian bandwidth, dan troubleshooting internet untuk operasional lapangan.',
                    'service_title_prefix' => 'Konfigurasi Router & Hotspot',
                    'service_description' => 'Konfigurasi router, hotspot user, pembagian bandwidth dasar, serta troubleshooting koneksi internet dan perangkat jaringan.',
                    'price_min' => 750000,
                    'price_max' => 1600000,
                    'delivery_time' => 5,
                    'portfolio_title_prefix' => 'Konfigurasi Router & Hotspot',
                    'portfolio_description' => 'Contoh implementasi hotspot sederhana, pengaturan bandwidth, dan dokumentasi konfigurasi router.',
                    'completed_brief' => 'Membutuhkan konfigurasi router dan hotspot sederhana untuk area tamu dengan pembagian bandwidth yang rapi.',
                    'review_comment' => 'Konfigurasi router berjalan baik dan dokumentasinya membantu saat tim kami melanjutkan pengelolaan.',
                ],
                [
                    'key' => 'it_support_lapangan',
                    'alias' => null,
                    'category' => 'IT Support',
                    'career_track' => 'Field IT Support Specialist',
                    'bio' => 'Siap membantu IT support lapangan, instalasi perangkat kerja, troubleshooting software dasar, dan pengecekan perangkat operasional.',
                    'service_title_prefix' => 'IT Support Lapangan & Perangkat',
                    'service_description' => 'Bantuan teknis lapangan untuk instalasi perangkat, troubleshooting software, maintenance ringan, dan pendampingan operasional.',
                    'price_min' => 450000,
                    'price_max' => 1100000,
                    'delivery_time' => 4,
                    'portfolio_title_prefix' => 'IT Support Lapangan',
                    'portfolio_description' => 'Contoh bantuan teknis lapangan untuk perangkat kerja, software, dan maintenance operasional sederhana.',
                    'completed_brief' => 'Perlu IT support lapangan untuk instalasi perangkat kerja dan troubleshooting kendala software pada tim operasional.',
                    'review_comment' => 'Pendampingan teknis di lapangan cukup membantu dan pengerjaan kendala perangkat terasa cepat.',
                ],
                [
                    'key' => 'cctv_infrastructure',
                    'alias' => null,
                    'category' => 'Jaringan Komputer',
                    'career_track' => 'CCTV & Network Infrastructure Specialist',
                    'bio' => 'Menangani kebutuhan dasar CCTV IP, infrastruktur kabel jaringan, pengecekan perangkat, dan penataan koneksi untuk area kerja.',
                    'service_title_prefix' => 'Infrastruktur CCTV & Kabel Jaringan',
                    'service_description' => 'Pemasangan dasar CCTV IP, pengecekan kabel jaringan, penataan infrastruktur koneksi, dan troubleshooting perangkat pendukung.',
                    'price_min' => 900000,
                    'price_max' => 1900000,
                    'delivery_time' => 7,
                    'portfolio_title_prefix' => 'Implementasi CCTV & Infrastruktur',
                    'portfolio_description' => 'Dokumentasi pemasangan infrastruktur CCTV IP dan penataan kabel jaringan untuk kebutuhan keamanan area.',
                    'completed_brief' => 'Dibutuhkan bantuan infrastruktur jaringan dan CCTV dasar untuk area kerja serta ruang penyimpanan.',
                    'review_comment' => 'Pekerjaan infrastruktur dan pengecekan perangkatnya rapi serta membantu saat setup di lapangan.',
                ],
                [
                    'key' => 'multimedia_documentation',
                    'alias' => 'multimedia',
                    'category' => 'Multimedia',
                    'career_track' => 'Multimedia Documentation Specialist',
                    'bio' => 'Fokus pada video editing dokumentasi, highlight kegiatan, subtitle presentasi, dan pengolahan materi visual untuk pitching.',
                    'service_title_prefix' => 'Video Editing Dokumentasi & Highlight',
                    'service_description' => 'Membantu video editing dokumentasi kegiatan, highlight workshop, subtitle, transisi ringan, dan materi visual presentasi.',
                    'price_min' => 600000,
                    'price_max' => 1300000,
                    'delivery_time' => 6,
                    'portfolio_title_prefix' => 'Video Editing Dokumentasi',
                    'portfolio_description' => 'Contoh video editing dokumentasi, highlight kegiatan, dan materi visual untuk kebutuhan promosi atau pitching.',
                    'completed_brief' => 'Mencari freelancer untuk video editing dokumentasi kegiatan dan pembuatan highlight singkat untuk presentasi.',
                    'review_comment' => 'Video editing dokumentasinya pas untuk kebutuhan presentasi dan mudah dipakai saat pitching.',
                ],
            ],
        ];
    }

    private function seedCuratedServices(array $categories, array $freelancers): array
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
                'freelancer' => 'iot_specialist',
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

    private function seedCuratedPortofolios(array $services): void
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

    private function seedAiSignalLayer(array $records, array $clients): void
    {
        $recordList = array_values($records);
        $clientPool = array_values($clients);
        $reviewRatings = [5, 5, 4, 4, 5, 3, 5, 4, 4, 5, 3, 4];
        $reviewComments = [
            'Freelancer ini cukup paham kebutuhan kami dan hasil akhirnya terasa relevan.',
            'Komunikasi bagus, pengerjaan stabil, dan cocok untuk kebutuhan presentasi digital.',
            'Hasil sesuai kebutuhan, revisi ringan cepat ditangani, dan progres jelas.',
            'Deliverable rapi dan membantu tim kami saat menyiapkan demo produk.',
            'Sangat cocok untuk kebutuhan proyek sekolah dan dokumentasinya mudah dipahami.',
            'Pengerjaan cukup baik dan membantu saat kami butuh hasil yang cepat dipresentasikan.',
            'Skill teknisnya terasa matang dan hasilnya sesuai ekspektasi dari brief awal.',
            'Output akhir mudah dipakai ulang oleh tim kami untuk operasional selanjutnya.',
            'Freelancer cukup adaptif terhadap perubahan brief dan komunikasi lancar.',
            'Kerja sama berjalan baik, hasilnya meyakinkan, dan penyelesaian tepat waktu.',
            'Butuh sedikit arahan di awal, tetapi hasil akhirnya tetap relevan untuk kebutuhan kami.',
            'Secara keseluruhan hasilnya bagus dan membantu pengujian fitur marketplace kami.',
        ];

        foreach ($recordList as $index => $record) {
            if ($index % 3 === 0) {
                $portfolioNumber = ($index % 4) + 1;

                Portofolio::updateOrCreate(
                    [
                        'service_id' => $record['service']->id,
                        'title' => $record['template']['portfolio_title_prefix'] . ' - ' . $record['student']->name,
                    ],
                    [
                        'description' => $record['template']['portfolio_description'],
                        'media_url' => 'portofolios/portofolio-' . $portfolioNumber . '.webp',
                    ]
                );
            }

            if ($index % 6 === 0) {
                $client = $clientPool[(int) floor($index / 6) % count($clientPool)];
                $order = Order::updateOrCreate(
                    [
                        'service_id' => $record['service']->id,
                        'client_id' => $client->id,
                        'brief' => $record['template']['completed_brief'],
                    ],
                    [
                        'freelancer_id' => $record['freelancer']->id,
                        'status' => 'Completed',
                        'agreed_price' => $record['service']->price_max,
                        'deadline' => now()->subDays(5 + $index)->toDateString(),
                    ]
                );

                Transaction::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'type' => 'Full',
                    ],
                    [
                        'amount' => $record['service']->price_max,
                        'status' => 'Paid',
                    ]
                );

                $reviewIndex = (int) floor($index / 6);
                Review::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'rating' => $reviewRatings[$reviewIndex],
                        'comment' => $reviewComments[$reviewIndex] . ' ' . $record['template']['review_comment'],
                    ]
                );

                if ($index % 12 === 0) {
                    $extraBrief = $record['template']['completed_brief'] . ' - tahap lanjutan dan optimalisasi implementasi.';
                    $extraOrder = Order::updateOrCreate(
                        [
                            'service_id' => $record['service']->id,
                            'client_id' => $client->id,
                            'brief' => $extraBrief,
                        ],
                        [
                            'freelancer_id' => $record['freelancer']->id,
                            'status' => 'Completed',
                            'agreed_price' => $record['service']->price_max,
                            'deadline' => now()->subDays(12 + $index)->toDateString(),
                        ]
                    );

                    Transaction::updateOrCreate(
                        [
                            'order_id' => $extraOrder->id,
                            'type' => 'Full',
                        ],
                        [
                            'amount' => $record['service']->price_max,
                            'status' => 'Paid',
                        ]
                    );
                }
            }
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
                'applications' => [
                    [
                        'freelancer' => 'multimedia',
                        'proposal' => 'Saya siap menyusun video highlight kegiatan dengan ritme yang pas untuk kebutuhan pitching dan dokumentasi.',
                        'proposed_price' => 980000,
                        'status' => 'Pending',
                    ],
                ],
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
