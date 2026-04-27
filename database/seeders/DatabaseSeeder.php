<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Production-grade Seeder
     * Principles:
     * - Ordered by dependency
     * - Deterministic (data jelas, tidak chaos)
     * - Safe for migrate:fresh --seed
     * - Mudah debug jika ada error
     */
    public function run(): void
    {
        /*
        |------------------------------------------------------------------
        | 1. MASTER ADMIN DATA
        |------------------------------------------------------------------
        */
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


        /*
        |------------------------------------------------------------------
        | 2. BASE USER DATA (independent)
        |------------------------------------------------------------------
        */
        $students = SkomdaStudent::factory(50)->create();
        $clients = Client::factory(10)->create();


        /*
        |------------------------------------------------------------------
        | 3. FREELANCER DATA
        | Hindari recycle() dulu jika relasi belum benar-benar stabil.
        | Lebih aman explicit create().
        |------------------------------------------------------------------
        */
        $freelancers = Freelancer::factory(10)->create();


        /*
        |------------------------------------------------------------------
        | 4. MASTER CATEGORY DATA
        |------------------------------------------------------------------
        */
        $categories = [
            [
                'name' => 'Web Development',
                'description' => 'Layanan terkait pengembangan web dan pemrograman.',
            ],
            [
                'name' => 'Desain Grafis',
                'description' => 'Jasa desain grafis untuk kebutuhan promosi dan branding.',
            ],
            [
                'name' => 'Jaringan Komputer',
                'description' => 'Layanan instalasi dan konfigurasi jaringan.',
            ],
            [
                'name' => 'IT Support',
                'description' => 'Layanan dukungan teknis dan maintenance sistem IT.',
            ],
            [
                'name' => 'Internet of Things (IoT)',
                'description' => 'Layanan pengembangan solusi IoT.',
            ],
            [
                'name' => 'Multimedia',
                'description' => 'Layanan terkait pengembangan multimedia.',
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }


        /*
        |------------------------------------------------------------------
        | 5. SERVICE + PORTOFOLIO
        |------------------------------------------------------------------
        */
        $services = Service::factory(20)->create();
        Portofolio::factory(20)->create();


        /*
        |------------------------------------------------------------------
        | 6. TRANSACTION FLOW
        | Order -> Offer -> Negotiation -> Transaction -> Result -> Review
        |------------------------------------------------------------------
        */
        $orders = Order::factory(30)->create();

        $offers = Offer::factory(30)->create();

        Negotiation::factory(10)->create();

        Transaction::factory(15)->create();

        Result::factory(15)->create();


        /*
        |------------------------------------------------------------------
        | 7. REVIEW
        | Buat review hanya jika order belum punya review
        |------------------------------------------------------------------
        */
        Order::doesntHave('review')
            ->get()
            ->each(function ($order) {
                Review::factory()->create([
                    'order_id' => $order->id,
                ]);
            });
    }
}
