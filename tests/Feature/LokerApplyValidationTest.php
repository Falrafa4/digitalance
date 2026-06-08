<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Loker;
use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LokerApplyValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_freelancer_cannot_apply_above_loker_budget_max(): void
    {
        $freelancer = $this->makeFreelancer('loker-validation@example.com');
        $loker = $this->makeLoker(900000);

        $response = $this->actingAs($freelancer, 'freelancer')->post(route('freelancer.loker.apply', $loker), [
            'proposal' => 'Saya siap membantu mengerjakan project ini dengan komunikasi rutin dan alur kerja yang terstruktur.',
            'proposed_price' => 950000,
        ]);

        $response->assertSessionHasErrors(['proposed_price']);

        $this->assertDatabaseMissing('loker_applications', [
            'loker_id' => $loker->id,
            'freelancer_id' => $freelancer->id,
            'proposed_price' => 950000,
        ]);
    }

    public function test_freelancer_can_apply_with_price_equal_to_budget_max(): void
    {
        $freelancer = $this->makeFreelancer('loker-validation-ok@example.com');
        $loker = $this->makeLoker(900000);

        $response = $this->actingAs($freelancer, 'freelancer')->post(route('freelancer.loker.apply', $loker), [
            'proposal' => 'Saya siap membantu mengerjakan project ini dengan komunikasi rutin dan alur kerja yang terstruktur.',
            'proposed_price' => 900000,
        ]);

        $response->assertRedirect(route('freelancer.loker.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('loker_applications', [
            'loker_id' => $loker->id,
            'freelancer_id' => $freelancer->id,
            'proposed_price' => 900000,
            'status' => 'Pending',
        ]);
    }

    private function makeFreelancer(string $email): Freelancer
    {
        $student = SkomdaStudent::create([
            'nis' => fake()->unique()->numerify('#########'),
            'name' => 'Freelancer Validasi',
            'email' => $email,
            'phone' => '083333333333',
            'class' => 'XI SIJA 1',
            'major' => 'SIJA',
            'is_registered' => true,
        ]);

        return Freelancer::create([
            'student_id' => $student->id,
            'bio' => 'Freelancer untuk test validasi loker.',
            'profile_photo' => 'profiles/placeholder.webp',
            'password' => Hash::make('password'),
            'status' => 'Approved',
        ]);
    }

    private function makeLoker(float $budgetMax): Loker
    {
        $client = Client::create([
            'name' => 'Client Validasi',
            'email' => 'client-validasi@example.com',
            'phone' => '081222222222',
            'password' => Hash::make('password'),
            'profile_photo' => 'profiles/placeholder.webp',
        ]);

        $category = ServiceCategory::create([
            'name' => 'Desain Grafis',
            'description' => 'Kategori untuk test validasi loker.',
            'is_active' => true,
        ]);

        return Loker::create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Butuh Desainer Poster Event',
            'description' => 'Membutuhkan freelancer untuk mendesain poster event sekolah dengan revisi maksimal dua kali.',
            'budget_min' => 400000,
            'budget_max' => $budgetMax,
            'deadline' => now()->addDays(5)->toDateString(),
            'status' => 'Open',
        ]);
    }
}
