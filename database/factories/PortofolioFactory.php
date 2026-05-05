<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Portofolio>
 */
class PortofolioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $portfolioSamples = [
            ['title' => 'Landing Page Website 1', 'description' => 'Halaman promosi untuk jasa servis AC dengan form lead, CTA WhatsApp, dan optimasi mobile speed.'],
            ['title' => 'Redesign Company Profile Klinik', 'description' => 'Peremajaan tampilan website klinik agar lebih modern, informatif, dan mudah diakses pasien.'],
            ['title' => 'Dashboard Monitoring Penjualan', 'description' => 'Dashboard internal untuk memantau transaksi harian, performa produk, dan laporan bulanan.'],
            ['title' => 'UI Kit Aplikasi Booking', 'description' => 'Paket komponen antarmuka untuk aplikasi booking jasa, termasuk tombol, kartu layanan, dan form checkout.'],
            ['title' => 'Setup Jaringan Lab Komputer', 'description' => 'Dokumentasi implementasi jaringan lab beserta konfigurasi access point dan pembagian VLAN dasar.'],
        ];

        $sample = fake()->randomElement($portfolioSamples);

        return [
            'service_id' => Service::inRandomOrder()->first()->id,
            'title' => $sample['title'],
            'description' => $sample['description'],
            'media_url' => 'https://picsum.photos/seed/portfolio-' . fake()->unique()->numberBetween(1, 9999) . '/1280/720',
        ];
    }
}
