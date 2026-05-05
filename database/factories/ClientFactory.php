<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstNames = ['Raka', 'Nadya', 'Fajar', 'Dinda', 'Rizky', 'Aulia', 'Bagas', 'Naila', 'Galih', 'Citra'];
        $lastNames = ['Pratama', 'Saputra', 'Maharani', 'Wibowo', 'Putri', 'Ramadhan', 'Permata', 'Handayani'];
        $domains = ['gmail.com', 'yahoo.com', 'outlook.com'];

        $name = fake()->randomElement($firstNames) . ' ' . fake()->randomElement($lastNames);
        $emailHandle = strtolower(str_replace(' ', '.', $name)) . fake()->numberBetween(10, 99);

        return [
            'name' => $name,
            'email' => fake()->unique()->lexify($emailHandle . '@' . fake()->randomElement($domains)),
            'password' => bcrypt('password'), // Default password for clients
            'phone' => '+628' . fake()->numerify('##########'),
        ];
    }
}
