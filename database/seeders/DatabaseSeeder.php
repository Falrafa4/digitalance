<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Freelancer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->call(SkomdaStudentSeeder::class);
            $this->call(ProductionSeeder::class);
        } else {
            $this->call(SkomdaStudentSeeder::class);
            $this->call(DevelopmentSeeder::class);
        }

        Client::whereNull('profile_photo')->update([
            'profile_photo' => 'profiles/placeholder.webp',
        ]);

        Freelancer::whereNull('profile_photo')->update([
            'profile_photo' => 'profiles/placeholder.webp',
        ]);
    }
}
