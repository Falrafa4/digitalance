<?php

namespace Database\Seeders;

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
    }
}
