<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientOrderAttachmentUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_submit_attachment_upload_form_without_file(): void
    {
        Storage::fake('public');
        [$client, $order] = $this->makeOrderFixture();

        $this->actingAs($client, 'client')
            ->from(route('client.orders.show', $order))
            ->post(route('client.orders.attachments.store', $order), [])
            ->assertRedirect(route('client.orders.show', $order))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Attachment berhasil diupload');

        $this->assertDatabaseCount('order_attachments', 0);
    }

    private function makeOrderFixture(): array
    {
        $client = Client::create([
            'name' => 'Client Attachment',
            'email' => 'client-attachment@example.com',
            'phone' => '081222222222',
            'password' => Hash::make('password'),
            'profile_photo' => 'profiles/placeholder.webp',
        ]);

        $student = SkomdaStudent::create([
            'nis' => fake()->unique()->numerify('#########'),
            'name' => 'Freelancer Attachment',
            'email' => 'freelancer-attachment@example.com',
            'phone' => '083333333333',
            'class' => 'XI SIJA 1',
            'major' => 'SIJA',
            'is_registered' => true,
        ]);

        $freelancer = Freelancer::create([
            'student_id' => $student->id,
            'bio' => 'Freelancer untuk test upload attachment.',
            'profile_photo' => 'profiles/placeholder.webp',
            'password' => Hash::make('password'),
            'status' => 'Approved',
        ]);

        $category = ServiceCategory::create([
            'name' => 'Attachment Test',
            'description' => 'Kategori untuk test upload attachment.',
            'is_active' => true,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'freelancer_id' => $freelancer->id,
            'title' => 'Attachment Service',
            'description' => 'Layanan untuk test upload attachment.',
            'price_min' => 100000,
            'price_max' => 200000,
            'delivery_time' => 5,
            'status' => 'Approved',
        ]);

        $order = Order::create([
            'service_id' => $service->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'brief' => 'Brief upload attachment.',
            'status' => 'Pending',
        ]);

        return [$client, $order];
    }
}
