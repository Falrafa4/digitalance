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
        $firstNames = ['Arga', 'Rafa', 'Dimas', 'Yusuf', 'Tegar', 'Nisa', 'Lala', 'Syifa', 'Adit', 'Farhan'];
        $lastNames = ['Saputra', 'Prasetyo', 'Maulana', 'Permana', 'Fadilah', 'Putri', 'Anjani', 'Salsabila'];
        $class = fake()->randomElement(['X', 'XI', 'XII', 'XIII']);
        $major = fake()->randomElement(['TJAT', 'SIJA']);

        if ($class === 'XIII') {
            $major = 'SIJA';
        }

        $name = fake()->randomElement($firstNames) . ' ' . fake()->randomElement($lastNames);

        return [
            'nis' => fake()->unique()->numerify('#########'),
            'name' => $name,
            'email' => fake()->unique()->userName() . '@student.skomda.sch.id',
            'class' => $class,
            'major' => $major,
        ];
    }
}
