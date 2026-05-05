<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $offerTemplates = [
            ['title' => 'Penawaran Paket Basic', 'description' => 'Mencakup desain utama, 1 kali revisi, dan deployment ke hosting client.'],
            ['title' => 'Penawaran Paket Standard', 'description' => 'Mencakup implementasi penuh, 2 kali revisi, dokumentasi singkat, dan support 14 hari.'],
            ['title' => 'Penawaran Paket Priority', 'description' => 'Pengerjaan diprioritaskan dengan update progres berkala dan support teknis 30 hari.'],
            ['title' => 'Revisi Penawaran Tahap 2', 'description' => 'Penyesuaian ruang lingkup berdasarkan hasil diskusi, termasuk tambahan fitur yang disepakati.'],
        ];

        $template = fake()->randomElement($offerTemplates);

        return [
            'order_id' => Order::inRandomOrder()->first()->id,
            'title' => $template['title'],
            'description' => $template['description'],
            'offered_price' => fake()->numberBetween(500000, 4500000),
            'deadline' => fake()->dateTimeBetween('+4 days', '+40 days'),
            'status' => fake()->randomElement(['Sent', 'Accepted', 'Rejected', 'Expired']),
        ];
    }
}
