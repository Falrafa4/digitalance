<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $comments = [
            5 => [
                'Hasil sangat rapi, komunikasi cepat, dan sesuai brief dari awal.',
                'Pengerjaan tepat waktu, revisi ditangani cepat, sangat direkomendasikan.',
            ],
            4 => [
                'Secara keseluruhan bagus, hanya ada revisi kecil di bagian layout.',
                'Kualitas kerja baik dan responsif, akan kerja sama lagi untuk proyek berikutnya.',
            ],
            3 => [
                'Pekerjaan selesai, namun ada beberapa bagian yang perlu diarahkan ulang.',
                'Cukup memuaskan, semoga koordinasi bisa lebih cepat di proyek berikutnya.',
            ],
        ];

        $rating = fake()->randomElement([3, 4, 4, 5, 5]);

        return [
            'rating' => $rating,
            'comment' => fake()->randomElement($comments[$rating]),
        ];
    }
}
