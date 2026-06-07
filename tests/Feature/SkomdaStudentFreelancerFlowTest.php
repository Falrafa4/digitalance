<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Freelancer;
use App\Models\SkomdaStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SkomdaStudentFreelancerFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_skomda_student_filter_excludes_students_who_are_already_freelancers(): void
    {
        $administrator = Administrator::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $standaloneStudent = SkomdaStudent::create([
            'nis' => '100001',
            'name' => 'Siswa Biasa',
            'email' => 'siswa-biasa@example.com',
            'phone' => '0811111111',
            'class' => 'XI RPL 1',
            'major' => 'SIJA',
        ]);

        $freelancerStudent = SkomdaStudent::create([
            'nis' => '100002',
            'name' => 'Siswa Freelancer',
            'email' => 'siswa-freelancer@example.com',
            'phone' => '0822222222',
            'class' => 'XI RPL 2',
            'major' => 'TJAT',
        ]);

        Freelancer::create([
            'student_id' => $freelancerStudent->id,
            'bio' => 'Freelancer aktif',
            'profile_photo' => 'profiles/placeholder.webp',
            'password' => Hash::make('password'),
            'status' => 'Approved',
        ]);
        $freelancerStudent->forceFill(['is_registered' => true])->save();

        $response = $this->actingAs($administrator, 'administrator')->get(route('admin.clients.index', ['role' => 'Skomda Student']));

        $response->assertOk();
        $response->assertSeeText('Skomda Students');

        preg_match('/<tbody[^>]*>(.*?)<\/tbody>/s', $response->getContent(), $matches);
        $tbody = $matches[1] ?? '';

        $this->assertStringContainsString('Siswa Biasa', $tbody);
        $this->assertStringNotContainsString('Siswa Freelancer', $tbody);
        $this->assertSame(1, preg_match_all('/<tr\b/i', $tbody));

        $aliasResponse = $this->actingAs($administrator, 'administrator')->get(route('admin.clients.index', ['role' => 'Siswa Skomda']));

        $aliasResponse->assertOk();
        preg_match('/<tbody[^>]*>(.*?)<\/tbody>/s', $aliasResponse->getContent(), $aliasMatches);
        $aliasTbody = $aliasMatches[1] ?? '';

        $this->assertStringContainsString('Siswa Biasa', $aliasTbody);
        $this->assertStringNotContainsString('Siswa Freelancer', $aliasTbody);

        $freelancerResponse = $this->actingAs($administrator, 'administrator')->get(route('admin.clients.index', ['role' => 'Freelancer']));

        $freelancerResponse->assertOk();

        preg_match('/<tbody[^>]*>(.*?)<\/tbody>/s', $freelancerResponse->getContent(), $freelancerMatches);
        $freelancerTbody = $freelancerMatches[1] ?? '';

        $this->assertStringContainsString('Siswa Freelancer', $freelancerTbody);
        $this->assertStringNotContainsString('Siswa Biasa', $freelancerTbody);
        $this->assertSame(1, preg_match_all('/<tr\b/i', $freelancerTbody));

        $this->assertDatabaseHas('skomda_students', [
            'id' => $standaloneStudent->id,
        ]);
    }

    public function test_deleting_a_freelancer_account_keeps_the_student_record_available_again(): void
    {
        $freelancerStudent = SkomdaStudent::create([
            'nis' => '200001',
            'name' => 'Calon Freelancer',
            'email' => 'calon-freelancer@example.com',
            'phone' => '0833333333',
            'class' => 'XI RPL 3',
            'major' => 'SIJA',
        ]);

        $freelancer = Freelancer::create([
            'student_id' => $freelancerStudent->id,
            'bio' => 'Siap kerja freelance',
            'profile_photo' => 'profiles/placeholder.webp',
            'password' => Hash::make('password'),
            'status' => 'Approved',
        ]);
        $freelancerStudent->forceFill(['is_registered' => true])->save();

        $response = $this->actingAs($freelancer, 'freelancer')->post(route('freelancer.delete'), [
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseMissing('freelancers', [
            'id' => $freelancer->id,
        ]);

        $this->assertDatabaseHas('skomda_students', [
            'id' => $freelancerStudent->id,
            'name' => 'Calon Freelancer',
            'is_registered' => false,
        ]);
    }
}
