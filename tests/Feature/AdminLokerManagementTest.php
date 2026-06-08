<?php

namespace Tests\Feature;

use App\Models\Administrator;
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

class AdminLokerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_filtered_loker_index(): void
    {
        $admin = $this->createAdmin();
        $categoryA = ServiceCategory::create([
            'name' => 'Web Development',
            'description' => 'Web projects',
            'is_active' => true,
        ]);
        $categoryB = ServiceCategory::create([
            'name' => 'Design',
            'description' => 'Design projects',
            'is_active' => true,
        ]);
        $clientA = Client::factory()->create(['name' => 'Client Laravel']);
        $clientB = Client::factory()->create(['name' => 'Client Design']);

        Loker::create([
            'client_id' => $clientA->id,
            'category_id' => $categoryA->id,
            'title' => 'Laravel Dashboard Monitoring',
            'description' => 'Membutuhkan freelancer untuk membangun dashboard monitoring admin yang lengkap.',
            'budget_min' => 1000000,
            'budget_max' => 2000000,
            'deadline' => now()->addWeek()->toDateString(),
            'status' => 'Open',
        ]);

        Loker::create([
            'client_id' => $clientB->id,
            'category_id' => $categoryB->id,
            'title' => 'Brand Identity Design',
            'description' => 'Membutuhkan desainer untuk menyusun identitas visual brand baru.',
            'budget_min' => 800000,
            'budget_max' => 1200000,
            'deadline' => now()->addDays(10)->toDateString(),
            'status' => 'Closed',
        ]);

        $response = $this->actingAs($admin, 'administrator')->get(route('admin.loker.index', [
            'status' => 'Open',
            'category' => $categoryA->id,
            'q' => 'Laravel',
        ]));

        $response->assertOk();
        $response->assertSee('Laravel Dashboard Monitoring');
        $response->assertDontSee('Brand Identity Design');
        $response->assertSee('Client Laravel');
    }

    public function test_admin_can_approve_pending_loker_application_and_create_order(): void
    {
        $admin = $this->createAdmin();
        [$client, $loker, $application, $freelancer] = $this->createPendingApplicationScenario();

        $response = $this->actingAs($admin, 'administrator')
            ->post(route('admin.loker.applications.approve', $application));

        $response->assertRedirect(route('admin.loker.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loker_applications', [
            'id' => $application->id,
            'status' => 'Approved',
        ]);
        $this->assertDatabaseHas('lokkers', [
            'id' => $loker->id,
            'status' => 'Closed',
        ]);
        $order = Order::query()->where('loker_application_id', $application->id)->first();
        $this->assertNotNull($order);
        $this->assertSame($client->id, $order->client_id);
        $this->assertSame($freelancer->id, $order->freelancer_id);
        $this->assertSame('Pending', $order->status);
        $this->assertEquals(1750000, $order->agreed_price);
    }

    public function test_admin_can_reject_pending_loker_application(): void
    {
        $admin = $this->createAdmin();
        [, $loker, $application] = $this->createPendingApplicationScenario();

        $response = $this->actingAs($admin, 'administrator')
            ->post(route('admin.loker.applications.reject', $application));

        $response->assertRedirect(route('admin.loker.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loker_applications', [
            'id' => $application->id,
            'status' => 'Rejected',
        ]);
        $this->assertDatabaseHas('lokkers', [
            'id' => $loker->id,
            'status' => 'Open',
        ]);
        $this->assertDatabaseMissing('orders', [
            'loker_application_id' => $application->id,
        ]);
    }

    public function test_admin_can_toggle_loker_status(): void
    {
        $admin = $this->createAdmin();
        $client = Client::factory()->create();
        $loker = Loker::create([
            'client_id' => $client->id,
            'title' => 'Implementasi CMS',
            'description' => 'Membutuhkan pengembangan CMS internal dengan role admin dan editor.',
            'budget_min' => 1200000,
            'budget_max' => 2500000,
            'deadline' => now()->addDays(14)->toDateString(),
            'status' => 'Open',
        ]);

        $response = $this->actingAs($admin, 'administrator')
            ->patch(route('admin.loker.update', $loker), [
                'status' => 'Closed',
            ]);

        $response->assertRedirect(route('admin.loker.index'));
        $this->assertDatabaseHas('lokkers', [
            'id' => $loker->id,
            'status' => 'Closed',
        ]);
    }

    public function test_admin_can_delete_loker(): void
    {
        $admin = $this->createAdmin();
        [, $loker, $application] = $this->createPendingApplicationScenario();

        $response = $this->actingAs($admin, 'administrator')
            ->delete(route('admin.loker.destroy', $loker));

        $response->assertRedirect(route('admin.loker.index'));
        $this->assertDatabaseMissing('lokkers', [
            'id' => $loker->id,
        ]);
        $this->assertDatabaseMissing('loker_applications', [
            'id' => $application->id,
        ]);
    }

    public function test_non_admin_cannot_access_admin_loker_page(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($client, 'client')
            ->get(route('admin.loker.index'));

        $response->assertRedirect(route('login'));
    }

    private function createAdmin(): Administrator
    {
        return Administrator::query()->create([
            'name' => 'Admin Digitalance',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    private function createPendingApplicationScenario(): array
    {
        $category = ServiceCategory::create([
            'name' => 'Development',
            'description' => 'Development category',
            'is_active' => true,
        ]);
        $client = Client::factory()->create([
            'name' => 'PT Digitalance Client',
        ]);
        $student = SkomdaStudent::factory()->create([
            'name' => 'Naufal Freelancer',
            'major' => 'SIJA',
        ]);
        $freelancer = Freelancer::factory()->create([
            'student_id' => $student->id,
            'status' => 'Approved',
        ]);
        $loker = Loker::create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Admin Loker Monitoring',
            'description' => 'Membutuhkan freelancer untuk membantu monitoring admin dan integrasi laporan proyek.',
            'budget_min' => 1500000,
            'budget_max' => 2000000,
            'deadline' => now()->addDays(7)->toDateString(),
            'status' => 'Open',
        ]);
        $application = LokerApplication::create([
            'loker_id' => $loker->id,
            'freelancer_id' => $freelancer->id,
            'proposal' => 'Saya siap membangun dashboard monitoring dengan pengalaman Laravel dan data reporting.',
            'proposed_price' => 1750000,
            'status' => 'Pending',
        ]);

        return [$client, $loker, $application, $freelancer];
    }
}
