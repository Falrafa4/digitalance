<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Negotiation;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Result;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
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

        $this->patchJson('/api/v1/services/'.$service->id, [
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

        $this->getJson('/api/v1/orders/'.$order->id)
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

    public function test_freelancer_can_create_and_update_offer_on_canonical_route(): void
    {
        [, , $freelancer, $order] = $this->makeOrderFixture();

        Sanctum::actingAs($freelancer);

        $response = $this->postJson('/api/v1/offers', [
            'order_id' => $order->id,
            'title' => 'Penawaran API',
            'description' => 'Penawaran dibuat dari REST API canonical.',
            'offered_price' => 1750000,
            'deadline' => now()->addDays(7)->toDateString(),
        ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Penawaran API')
            ->assertJsonPath('data.status', 'Sent');

        $offerId = $response->json('data.id');

        $this->patchJson('/api/v1/offers/'.$offerId, [
            'title' => 'Penawaran API Revisi',
            'offered_price' => 1650000,
        ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Penawaran API Revisi');

        $this->assertDatabaseHas('offers', [
            'id' => $offerId,
            'title' => 'Penawaran API Revisi',
            'status' => 'Sent',
        ]);
    }

    public function test_client_can_accept_sent_offer_on_canonical_route(): void
    {
        [, $client, , $order] = $this->makeOrderFixture();
        $offer = $this->makeOffer($order);

        Sanctum::actingAs($client);

        $this->postJson('/api/v1/offers/'.$offer->id.'/accept')
            ->assertOk()
            ->assertJsonPath('data.status', 'Accepted');

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'status' => 'Accepted',
        ]);
    }

    public function test_freelancer_cannot_view_offer_owned_by_another_freelancer(): void
    {
        [, , , $order] = $this->makeOrderFixture();
        $offer = $this->makeOffer($order);
        $otherFreelancer = $this->makeFreelancer('other-offer-freelancer@example.com');

        Sanctum::actingAs($otherFreelancer);

        $this->getJson('/api/v1/offers/'.$offer->id)
            ->assertForbidden();
    }

    public function test_client_can_create_price_negotiation_on_canonical_route(): void
    {
        Event::fake();
        [, $client, , $order] = $this->makeOrderFixture();

        Sanctum::actingAs($client);

        $this->postJson('/api/v1/negotiations', [
            'order_id' => $order->id,
            'reason' => 'Budget perlu disesuaikan.',
            'new_price' => 1250000,
            'description' => 'Scope tetap sama, hanya penyesuaian harga.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.sender', 'client')
            ->assertJsonPath('data.status', 'Pending')
            ->assertJsonPath('data.reason', 'Budget perlu disesuaikan.');

        $this->assertDatabaseHas('negotiations', [
            'order_id' => $order->id,
            'sender' => 'client',
            'proposed_price' => 1250000,
            'status' => 'Pending',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Negotiated',
            'agreed_price' => 1250000,
        ]);
    }

    public function test_freelancer_can_accept_negotiation_on_canonical_route(): void
    {
        [, , $freelancer, $order] = $this->makeOrderFixture();
        $negotiation = $this->makeNegotiation($order);

        Sanctum::actingAs($freelancer);

        $this->postJson('/api/v1/negotiations/'.$negotiation->id.'/accept')
            ->assertOk()
            ->assertJsonPath('data.status', 'Accepted');

        $this->assertDatabaseHas('negotiations', [
            'id' => $negotiation->id,
            'status' => 'Accepted',
        ]);
    }

    public function test_freelancer_cannot_view_negotiation_owned_by_another_freelancer(): void
    {
        [, , , $order] = $this->makeOrderFixture();
        $negotiation = $this->makeNegotiation($order);
        $otherFreelancer = $this->makeFreelancer('other-negotiation-freelancer@example.com');

        Sanctum::actingAs($otherFreelancer);

        $this->getJson('/api/v1/negotiations/'.$negotiation->id)
            ->assertForbidden();
    }

    public function test_freelancer_can_create_result_link_on_canonical_route(): void
    {
        [, , $freelancer, $order] = $this->makeOrderFixture();

        Sanctum::actingAs($freelancer);

        $this->postJson('/api/v1/results', [
            'order_id' => $order->id,
            'result_mode' => 'link',
            'result_link' => 'https://files.digitalance.test/result.zip',
            'note' => 'Final delivery via API.',
            'version' => 'v1.0',
        ])
            ->assertCreated()
            ->assertJsonPath('data.order_id', $order->id)
            ->assertJsonPath('data.result_mode', 'link')
            ->assertJsonPath('data.version', 'v1.0');

        $this->assertDatabaseHas('results', [
            'order_id' => $order->id,
            'file_url' => 'https://files.digitalance.test/result.zip',
            'result_mode' => 'link',
            'version' => 'v1.0',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'In Progress',
        ]);
    }

    public function test_client_can_view_result_owned_by_their_order(): void
    {
        [, $client, , $order] = $this->makeOrderFixture();
        $result = $this->makeResult($order);

        Sanctum::actingAs($client);

        $this->getJson('/api/v1/results/'.$result->id)
            ->assertOk()
            ->assertJsonPath('data.id', $result->id)
            ->assertJsonPath('data.order.client.id', $client->id);
    }

    public function test_freelancer_cannot_view_result_owned_by_another_freelancer(): void
    {
        [, , , $order] = $this->makeOrderFixture();
        $result = $this->makeResult($order);
        $otherFreelancer = $this->makeFreelancer('other-result-freelancer@example.com');

        Sanctum::actingAs($otherFreelancer);

        $this->getJson('/api/v1/results/'.$result->id)
            ->assertForbidden();
    }

    public function test_admin_can_update_result_with_put_on_canonical_route(): void
    {
        [$admin, , , $order] = $this->makeOrderFixture();
        $result = $this->makeResult($order);

        Sanctum::actingAs($admin);

        $this->putJson('/api/v1/results/'.$result->id, [
            'note' => 'Catatan hasil diperbarui oleh admin.',
            'version' => 'v1.1',
        ])
            ->assertOk()
            ->assertJsonPath('data.note', 'Catatan hasil diperbarui oleh admin.')
            ->assertJsonPath('data.version', 'v1.1');

        $this->assertDatabaseHas('results', [
            'id' => $result->id,
            'note' => 'Catatan hasil diperbarui oleh admin.',
            'version' => 'v1.1',
        ]);
    }

    public function test_admin_can_delete_result_on_canonical_route(): void
    {
        [$admin, , , $order] = $this->makeOrderFixture();
        $result = $this->makeResult($order);

        Sanctum::actingAs($admin);

        $this->deleteJson('/api/v1/results/'.$result->id)
            ->assertOk();

        $this->assertDatabaseMissing('results', [
            'id' => $result->id,
        ]);
    }

    public function test_client_can_create_review_on_canonical_route(): void
    {
        [, $client, , $order] = $this->makeOrderFixture();
        $order->update(['status' => 'Completed']);

        Sanctum::actingAs($client);

        $this->postJson('/api/v1/reviews', [
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Hasil sangat rapi dan komunikatif.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.order_id', $order->id)
            ->assertJsonPath('data.rating', 5);

        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'rating' => 5,
        ]);
    }

    public function test_client_can_view_review_owned_by_their_order(): void
    {
        [, $client, , $order] = $this->makeOrderFixture();
        $review = $this->makeReview($order);

        Sanctum::actingAs($client);

        $this->getJson('/api/v1/reviews/'.$review->id)
            ->assertOk()
            ->assertJsonPath('data.id', $review->id)
            ->assertJsonPath('data.order.client.id', $client->id);
    }

    public function test_other_client_cannot_view_review_owned_by_different_order(): void
    {
        [, , , $order] = $this->makeOrderFixture();
        $review = $this->makeReview($order);

        $otherClient = Client::create([
            'name' => 'Other Client',
            'email' => 'other-client-review@example.com',
            'phone' => '081333333333',
            'password' => Hash::make('password'),
            'profile_photo' => 'profiles/placeholder.webp',
        ]);

        Sanctum::actingAs($otherClient);

        $this->getJson('/api/v1/reviews/'.$review->id)
            ->assertForbidden();
    }

    public function test_admin_can_update_review_with_put_on_canonical_route(): void
    {
        [$admin, , , $order] = $this->makeOrderFixture();
        $review = $this->makeReview($order);

        Sanctum::actingAs($admin);

        $this->putJson('/api/v1/reviews/'.$review->id, [
            'rating' => 4,
            'comment' => 'Review diperbarui oleh admin.',
        ])
            ->assertOk()
            ->assertJsonPath('data.rating', 4)
            ->assertJsonPath('data.comment', 'Review diperbarui oleh admin.');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 4,
            'comment' => 'Review diperbarui oleh admin.',
        ]);
    }

    public function test_owner_client_can_delete_review_on_canonical_route(): void
    {
        [, $client, , $order] = $this->makeOrderFixture();
        $review = $this->makeReview($order);

        Sanctum::actingAs($client);

        $this->deleteJson('/api/v1/reviews/'.$review->id)
            ->assertOk();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_admin_can_create_transaction_on_canonical_route(): void
    {
        [$admin, , , $order] = $this->makeOrderFixture();

        Sanctum::actingAs($admin);

        $this->postJson('/api/v1/transactions', [
            'order_id' => $order->id,
            'amount' => 1500000,
            'type' => 'Full',
            'status' => 'Paid',
        ])
            ->assertCreated()
            ->assertJsonPath('data.order_id', $order->id)
            ->assertJsonPath('data.amount', 1500000)
            ->assertJsonPath('data.type', 'Full')
            ->assertJsonPath('data.status', 'Paid');

        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'type' => 'Full',
            'status' => 'Paid',
        ]);
    }

    public function test_client_can_view_transaction_owned_by_their_order(): void
    {
        [, $client, , $order] = $this->makeOrderFixture();
        $transaction = $this->makeTransaction($order);

        Sanctum::actingAs($client);

        $this->getJson('/api/v1/transactions/'.$transaction->id)
            ->assertOk()
            ->assertJsonPath('data.id', $transaction->id)
            ->assertJsonPath('data.order.client.id', $client->id);
    }

    public function test_other_freelancer_cannot_view_transaction_owned_by_different_order(): void
    {
        [, , , $order] = $this->makeOrderFixture();
        $transaction = $this->makeTransaction($order);
        $otherFreelancer = $this->makeFreelancer('other-transaction-freelancer@example.com');

        Sanctum::actingAs($otherFreelancer);

        $this->getJson('/api/v1/transactions/'.$transaction->id)
            ->assertForbidden();
    }

    public function test_admin_can_update_transaction_with_put_on_canonical_route(): void
    {
        [$admin, , , $order] = $this->makeOrderFixture();
        $transaction = $this->makeTransaction($order);

        Sanctum::actingAs($admin);

        $this->putJson('/api/v1/transactions/'.$transaction->id, [
            'amount' => 1750000,
            'type' => 'Refund',
            'status' => 'Failed',
        ])
            ->assertOk()
            ->assertJsonPath('data.amount', 1750000)
            ->assertJsonPath('data.type', 'Refund')
            ->assertJsonPath('data.status', 'Failed');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 1750000,
            'type' => 'Refund',
            'status' => 'Failed',
        ]);
    }

    public function test_admin_can_delete_transaction_on_canonical_route(): void
    {
        [$admin, , , $order] = $this->makeOrderFixture();
        $transaction = $this->makeTransaction($order);

        Sanctum::actingAs($admin);

        $this->deleteJson('/api/v1/transactions/'.$transaction->id)
            ->assertOk();

        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id,
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

    private function makeOffer(Order $order): Offer
    {
        return Offer::create([
            'order_id' => $order->id,
            'title' => 'Penawaran Test API',
            'description' => 'Penawaran untuk test API.',
            'offered_price' => 1500000,
            'deadline' => now()->addDays(7)->toDateString(),
            'status' => 'Sent',
        ]);
    }

    private function makeNegotiation(Order $order): Negotiation
    {
        return Negotiation::create([
            'order_id' => $order->id,
            'sender' => 'client',
            'message' => 'Negosiasi harga: Budget perlu disesuaikan.',
            'proposed_price' => 1250000,
            'reason' => 'Budget perlu disesuaikan.',
            'description' => 'Scope tetap sama.',
            'status' => 'Pending',
        ]);
    }

    private function makeResult(Order $order): Result
    {
        return Result::create([
            'order_id' => $order->id,
            'file_url' => 'https://files.digitalance.test/result.zip',
            'result_mode' => 'link',
            'note' => 'Result untuk test API.',
            'version' => 'v1.0',
        ]);
    }

    private function makeReview(Order $order): Review
    {
        return Review::create([
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Review untuk test API.',
        ]);
    }

    private function makeTransaction(Order $order): Transaction
    {
        return Transaction::create([
            'order_id' => $order->id,
            'amount' => 1500000,
            'type' => 'Full',
            'status' => 'Paid',
        ]);
    }
}
