<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SkomdaStudent>
 */
class SkomdaStudentFactory extends Factory
{
    public function definition(): array
    {
        $class = fake()->randomElement(['X', 'XI', 'XII', 'XIII']);
        $major = fake()->randomElement(['TJAT', 'SIJA']);

        if ($class === 'XIII') {
            $major = 'SIJA';
        }

        return [
            'nis' => fake()->unique()->numerify('#########'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'class' => $class,
            'major' => $major,
        ];
    }
}
