<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SkomdaStudent>
 */
class SkomdaStudentFactory extends Factory
{
    public function definition(): array
    {
        $faker = Faker::create();

        $class = $faker->randomElement(['X', 'XI', 'XII', 'XIII']);
        $major = $faker->randomElement(['TJAT', 'SIJA']);

        if ($class === 'XIII') {
            $major = 'SIJA';
        }

        return [
            'nis' => $faker->unique()->numerify('#########'),
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'class' => $class,
            'major' => $major,
        ];
    }
}
