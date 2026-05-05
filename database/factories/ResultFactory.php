<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Result>
 */
class ResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $notes = [
            'Versi awal sudah termasuk halaman utama, halaman layanan, dan formulir kontak.',
            'Perbaikan sesuai revisi klien: optimasi tampilan mobile dan perapian layout hero.',
            'Final delivery siap produksi, sudah melalui pengujian dasar lintas browser.',
            'Update minor: penyesuaian warna brand dan perbaikan validasi form checkout.',
        ];

        return [
            'order_id' => Order::inRandomOrder()->first()->id,
            'file_url' => 'https://files.digitalance.test/deliverables/project-' . fake()->numberBetween(100, 999) . '.zip',
            'note' => fake()->randomElement($notes),
            'version' => 'v' . fake()->numberBetween(1, 3) . '.' . fake()->numberBetween(0, 9),
        ];
    }
}
