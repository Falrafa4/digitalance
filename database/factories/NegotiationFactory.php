<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Negotiation>
 */
class NegotiationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clientMessages = [
            'Bisa diturunkan sedikit untuk harga paket ini?',
            'Apakah deadline bisa dipercepat menjadi 10 hari?',
            'Saya setuju scope-nya, tapi minta tambahan 1 kali revisi.',
            'Tolong kirim rincian deliverable biar tim saya bisa cek.',
        ];

        $freelancerMessages = [
            'Bisa, saya sesuaikan jika fitur checkout disederhanakan.',
            'Deadline 10 hari memungkinkan, dengan tambahan biaya prioritas.',
            'Siap, saya tambahkan 1 kali revisi minor tanpa biaya tambahan.',
            'Berikut detail deliverable: desain, implementasi, testing, dan handover.',
        ];

        $sender = fake()->randomElement(['client', 'freelancer']);

        return [
            'order_id' => \App\Models\Order::inRandomOrder()->first()->id,
            'sender' => $sender,
            'message' => $sender === 'client'
                ? fake()->randomElement($clientMessages)
                : fake()->randomElement($freelancerMessages),
        ];
    }
}
