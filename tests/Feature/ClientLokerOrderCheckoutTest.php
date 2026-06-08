<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Loker;
use App\Models\LokerApplication;
use App\Models\Order;
use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientLokerOrderCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_accept_and_open_checkout_for_loker_order(): void
    {
        [$client, $order] = $this->makeLokerOrderFixture();

        $this->actingAs($client, 'client')
            ->post(route('client.orders.accept', $order))
            ->assertRedirect(route('client.orders.checkout', $order));

        $this->actingAs($client, 'client')
            ->get(route('client.orders.checkout', $order))
            ->assertOk()
            ->assertSee('Pembayaran');
    }

    private function makeLokerOrderFixture(): array
    {
        $client = Client::create([
            'name' => 'Client Checkout',
            'email' => 'client-checkout@example.com',
            'phone' => '081222222222',
            'password' => Hash::make('password'),
            'profile_photo' => 'profiles/placeholder.webp',
        ]);

        $student = SkomdaStudent::create([
            'nis' => fake()->unique()->numerify('#########'),
            'name' => 'Freelancer Checkout',
            'email' => 'freelancer-checkout@example.com',
            'phone' => '083333333333',
            'class' => 'XI SIJA 1',
            'major' => 'SIJA',
            'is_registered' => true,
        ]);

        $freelancer = Freelancer::create([
            'student_id' => $student->id,
            'bio' => 'Freelancer untuk test checkout loker.',
            'profile_photo' => 'profiles/placeholder.webp',
            'password' => Hash::make('password'),
            'status' => 'Approved',
        ]);

        $category = ServiceCategory::create([
            'name' => 'UI Design',
            'description' => 'Kategori untuk test checkout loker.',
            'is_active' => true,
        ]);

        $loker = Loker::create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Butuh UI Designer',
            'description' => 'Mencari freelancer untuk desain halaman checkout yang rapi dan jelas.',
            'budget_min' => 400000,
            'budget_max' => 900000,
            'deadline' => now()->addDays(5)->toDateString(),
            'status' => 'Open',
        ]);

        $application = LokerApplication::create([
            'loker_id' => $loker->id,
            'freelancer_id' => $freelancer->id,
            'proposal' => 'Saya siap membantu mengerjakan desain checkout dengan struktur yang jelas dan revisi yang terukur.',
            'proposed_price' => 750000,
            'status' => 'Approved',
        ]);

        $order = Order::create([
            'service_id' => null,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'loker_application_id' => $application->id,
            'brief' => $loker->title . ' - ' . $loker->description,
            'status' => 'Pending',
            'agreed_price' => 750000,
        ]);

        return [$client, $order];
    }
}
