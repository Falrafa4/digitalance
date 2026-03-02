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
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Administrator::create([
            'email' => 'admin1@email.com',
            'password' => bcrypt('admin123'), // Default password for admin
        ]);
        Administrator::create([
            'email' => 'admin2@email.com',
            'password' => bcrypt('admin123'), // Default password for admin
        ]);

        Client::factory(10)->create();
        Freelancer::factory(10)->recycle(SkomdaStudent::factory(100)->create())->create();

        ServiceCategory::create([
            'name' => 'Web Development',
            'description' => 'Layanan terkait pengembangan web dan pemrograman.',
        ]);
        ServiceCategory::create([
            'name' => 'Desain Grafis',
            'description' => 'Jasa desain grafis untuk kebutuhan promosi, branding, dan lainnya.',
        ]);
        ServiceCategory::create([
            'name' => 'Jaringan Komputer',
            'description' => 'Layanan terkait instalasi dan konfigurasi jaringan.',
        ]);
        ServiceCategory::create([
            'name' => 'IT Support',
            'description' => 'Layanan terkait dukungan teknis dan pemeliharaan sistem IT.',
        ]);
        ServiceCategory::create([
            'name' => 'Internet of Things (IoT)',
            'description' => 'Layanan terkait pengembangan dan implementasi solusi Internet of Things (IoT).',
        ]);
        ServiceCategory::create([
            'name' => 'Multimedia',
            'description' => 'Layanan terkait pengembangan dan implementasi solusi multimedia.',
        ]);

        Service::factory(20)->create();
        Portofolio::factory(20)->create();
        
        Order::factory(30)->create();
        Offer::factory(30)->create();
        Negotiation::factory(10)->create();
        Transaction::factory(15)->create();
        Result::factory(15)->create();
        Order::doesntHave('review')->get()->each(function ($order) {
            Review::factory()->create([
                'order_id' => $order->id,
            ]);
        });
        // Review::factory(15)->create();
    }
}
