<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serviceCatalog = [
            ['title' => 'Pembuatan Landing Page Produk', 'description' => 'Membangun landing page responsif untuk kampanye iklan dengan fokus pada CTA, kecepatan akses, dan integrasi WhatsApp.'],
            ['title' => 'Website Company Profile Sekolah', 'description' => 'Membuat website profil institusi lengkap dengan halaman program, galeri kegiatan, dan formulir kontak.'],
            ['title' => 'Desain Feed Instagram Brand', 'description' => 'Mendesain template feed Instagram konsisten untuk kebutuhan promosi bulanan dan branding visual.'],
            ['title' => 'Setup Jaringan Kantor Kecil', 'description' => 'Instalasi topologi jaringan dasar kantor termasuk konfigurasi router, SSID tamu, dan pembagian bandwidth.'],
            ['title' => 'Maintenance Website Bulanan', 'description' => 'Perawatan website mencakup update plugin, backup berkala, monitoring uptime, dan perbaikan minor bug.'],
            ['title' => 'Integrasi Payment Gateway', 'description' => 'Implementasi pembayaran online ke website dengan notifikasi transaksi dan validasi status pembayaran.'],
            ['title' => 'Pembuatan Dashboard Admin', 'description' => 'Membangun dashboard admin untuk manajemen data, filter laporan, dan ekspor rekap operasional.'],
        ];

        $service = fake()->randomElement($serviceCatalog);
        $priceMin = fake()->numberBetween(350000, 2500000);
        $priceMax = $priceMin + fake()->numberBetween(200000, 1800000);

        return [
            'category_id' => \App\Models\ServiceCategory::inRandomOrder()->first()->id,
            'freelancer_id' => \App\Models\Freelancer::inRandomOrder()->first()->id,
            'title' => $service['title'],
            'description' => $service['description'],
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'delivery_time' => fake()->numberBetween(2, 21),
            'status' => fake()->randomElement(['Approved', 'Approved', 'Pending', 'Draft']),
        ];
    }
}
