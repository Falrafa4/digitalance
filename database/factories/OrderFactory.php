<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $briefTemplates = [
            'Butuh landing page untuk promosi paket servis bulanan, sertakan section testimoni dan tombol chat cepat.',
            'Kami ingin website company profile dengan 6 halaman utama dan formulir konsultasi online.',
            'Tolong buat dashboard admin untuk rekap pesanan harian dan export laporan ke Excel.',
            'Perlu desain feed Instagram 12 postingan untuk kampanye grand opening bulan depan.',
            'Mohon setup jaringan kantor 2 lantai dengan pembagian akses tim operasional dan tamu.',
            'Butuh maintenance website selama 3 bulan termasuk backup mingguan dan pemantauan error.',
        ];

        $status = fake()->randomElement(['Pending', 'Negotiated', 'Paid', 'In Progress', 'Revision', 'Completed', 'Cancelled']);
        $agreedPrice = in_array($status, ['Pending', 'Cancelled'], true) ? null : fake()->numberBetween(500000, 4000000);

        return [
            'service_id' => Service::inRandomOrder()->first()->id,
            'client_id' => Client::inRandomOrder()->first()->id,
            'brief' => fake()->randomElement($briefTemplates),
            'status' => $status,
            'agreed_price' => $agreedPrice,
            'deadline' => in_array($status, ['Completed', 'Cancelled'], true)
                ? fake()->dateTimeBetween('-2 months', '-2 days')
                : fake()->dateTimeBetween('+3 days', '+45 days'),
        ];
    }
}
