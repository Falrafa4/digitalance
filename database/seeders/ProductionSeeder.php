<?php

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\SkomdaStudent;
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
        // ADMIN
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

        // SKOMDA STUDENTS
        // progress...
        SkomdaStudent::firstOrCreate(
            [
                'nis' => '000000001',
                'name' => 'Skomda Student 1',
                'email' => 'student1@student.smktelkom-sda.sch.id',
                'class' => 'XI SIJA 2',
                'major' => 'SIJA',
            ]
        );
    }
}
