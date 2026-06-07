<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RestApiCanonicalRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_me_returns_authenticated_profile_resource(): void
    {
        $client = Client::create([
            'name' => 'Client API',
            'email' => 'client-api@example.com',
            'phone' => '081111111111',
            'password' => Hash::make('password'),
            'profile_photo' => 'profiles/placeholder.webp',
        ]);

        Sanctum::actingAs($client);

        $this->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'client-api@example.com');
    }

    public function test_orders_index_is_scoped_by_authenticated_role(): void
    {
        [$admin, $client, $freelancer, $order] = $this->makeOrderFixture();

        Sanctum::actingAs($admin);
        $this->getJson('/api/v1/orders')
            ->assertOk()
            ->assertJsonPath('data.orders.data.0.id', $order->id);

        Sanctum::actingAs($client);
        $this->getJson('/api/v1/orders')
            ->assertOk()
            ->assertJsonPath('data.0.id', $order->id);

        Sanctum::actingAs($freelancer);
        $this->getJson('/api/v1/orders')
            ->assertOk()
            ->assertJsonPath('data.0.id', $order->id);
    }

    public function test_client_cannot_update_freelancer_service(): void
    {
        [, $client, , , $service] = $this->makeOrderFixture();

        Sanctum::actingAs($client);

        $this->patchJson('/api/v1/services/' . $service->id, [
            'title' => 'Tidak boleh',
            'description' => 'Client tidak boleh update service.',
            'price_min' => 100000,
            'price_max' => 200000,
            'delivery_time' => 3,
        ])->assertForbidden();
    }

    public function test_freelancer_cannot_view_order_owned_by_another_freelancer(): void
    {
        [, , , $order] = $this->makeOrderFixture();
        $otherFreelancer = $this->makeFreelancer('other-freelancer@example.com');

        Sanctum::actingAs($otherFreelancer);

        $this->getJson('/api/v1/orders/' . $order->id)
            ->assertForbidden();
    }

    public function test_admin_can_manage_service_category_on_canonical_route(): void
    {
        $admin = Administrator::create([
            'name' => 'Admin API',
            'email' => 'admin-api@example.com',
            'password' => Hash::make('password'),
            'status' => 'Active',
        ]);

        Sanctum::actingAs($admin);

        $this->postJson('/api/v1/service-categories', [
            'name' => 'Kategori API',
            'description' => 'Kategori dibuat dari canonical REST API.',
            'is_active' => true,
        ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Kategori API');

        $this->assertDatabaseHas('service_categories', [
            'name' => 'Kategori API',
        ]);
    }

    private function makeOrderFixture(): array
    {
        $admin = Administrator::create([
            'name' => 'Admin API',
            'email' => 'admin-fixture@example.com',
            'password' => Hash::make('password'),
            'status' => 'Active',
        ]);

        $client = Client::create([
            'name' => 'Client Fixture',
            'email' => 'client-fixture@example.com',
            'phone' => '081222222222',
            'password' => Hash::make('password'),
            'profile_photo' => 'profiles/placeholder.webp',
        ]);

        $freelancer = $this->makeFreelancer('freelancer-fixture@example.com');

        $category = ServiceCategory::create([
            'name' => 'Web Development',
            'description' => 'Kategori layanan web.',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'freelancer_id' => $freelancer->id,
            'title' => 'Landing Page API',
            'description' => 'Layanan untuk test API.',
            'price_min' => 100000,
            'price_max' => 200000,
            'delivery_time' => 5,
            'status' => 'Approved',
        ]);

        $order = Order::create([
            'service_id' => $service->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'brief' => 'Brief order API.',
            'status' => 'Pending',
        ]);

        return [$admin, $client, $freelancer, $order, $service];
    }

    private function makeFreelancer(string $email): Freelancer
    {
        $student = SkomdaStudent::create([
            'nis' => fake()->unique()->numerify('#########'),
            'name' => 'Freelancer API',
            'email' => $email,
            'phone' => '083333333333',
            'class' => 'XI SIJA 1',
            'major' => 'SIJA',
            'is_registered' => true,
        ]);

        return Freelancer::create([
            'student_id' => $student->id,
            'bio' => 'Freelancer untuk test API.',
            'profile_photo' => 'profiles/placeholder.webp',
            'password' => Hash::make('password'),
            'status' => 'Approved',
        ]);
    }
}
