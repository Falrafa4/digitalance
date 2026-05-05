<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Freelancer>
 */
class FreelancerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bios = [
            'Freelancer web developer dengan fokus Laravel dan sistem dashboard UMKM.',
            'UI/UX designer yang terbiasa membuat landing page konversi tinggi untuk jasa lokal.',
            'Teknisi jaringan berpengalaman untuk instalasi router, access point, dan CCTV IP.',
            'Fullstack developer untuk pembuatan website company profile dan portal pemesanan.',
            'Spesialis maintenance website, optimasi performa, dan perbaikan bug produksi.',
        ];

        return [
            'student_id' => \App\Models\SkomdaStudent::inRandomOrder()->first()->id,
            'bio' => fake()->randomElement($bios),
            'password' => bcrypt('password'), // Default password
            'status' => fake()->randomElement(['Pending', 'Approved', 'Approved', 'Suspended']),
        ];
    }
}
