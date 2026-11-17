<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Portofolio;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SkomdaStudent;
use App\Support\GroqClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientAiRecommendationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_access_ai_recommendations_page(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($client, 'client')->get(route('client.ai-recommendations'));

        $response->assertStatus(200);
        $response->assertSeeText('Rekomendasi Freelancer AI');
    }

    public function test_local_fallback_tokenizer_removes_stopwords_and_preserves_tech(): void
    {
        $tokens = GroqClient::tokenize('Butuh Developer yang bisa javascript dan node.js untuk proyek web');

        $this->assertNotContains('yang', $tokens);
        $this->assertNotContains('dan', $tokens);
        $this->assertNotContains('bisa', $tokens);
        $this->assertNotContains('untuk', $tokens);
        $this->assertNotContains('butuh', $tokens);
        $this->assertContains('developer', $tokens);
        $this->assertContains('javascript', $tokens);
        $this->assertContains('nodejs', $tokens);
        $this->assertContains('web', $tokens);
    }

    public function test_ai_recommendation_never_outputs_stopwords_in_analysis(): void
    {
        $client = Client::factory()->create();

        $category = ServiceCategory::create([
            'name' => 'Web Development',
            'description' => 'Web dev services',
            'is_active' => true,
        ]);

        $studentDev = SkomdaStudent::factory()->create(['name' => 'Budi Fullstack', 'major' => 'SIJA']);
        $freelancerDev = Freelancer::factory()->create([
            'student_id' => $studentDev->id,
            'status' => 'Approved',
            'bio' => 'Fullstack web developer berpengalaman.'
        ]);
        $serviceDev = Service::factory()->create([
            'freelancer_id' => $freelancerDev->id,
            'category_id' => $category->id,
            'title' => 'Pembuatan Website Fullstack'
        ]);
        Portofolio::factory()->create([
            'service_id' => $serviceDev->id,
            'title' => 'Sistem Informasi Web Dashboard',
            'description' => 'Aplikasi dashboard berbasis JavaScript dan Node.js'
        ]);

        $studentNet = SkomdaStudent::factory()->create(['name' => 'Joko Network', 'major' => 'TJAT']);
        $freelancerNet = Freelancer::factory()->create([
            'student_id' => $studentNet->id,
            'status' => 'Approved',
            'bio' => 'Teknisi instalasi fiber optic dan router cisco.'
        ]);

        $response = $this->actingAs($client, 'client')->get(route('client.ai-recommendations', [
            'q' => 'Butuh Developer yang bisa javascript dan node.js'
        ]));

        $response->assertStatus(200);
        
        $content = $response->getContent();
        // Ensure no stopword array output exists
        $this->assertStringNotContainsString('[yang, dan]', $content);
        $this->assertStringNotContainsString('[yang]', $content);
        $this->assertStringNotContainsString('[dan]', $content);

        // Verify recommendations data in view
        $recommendations = $response->viewData('recommendations');
        $this->assertNotEmpty($recommendations);

        // Top recommendation should be the developer
        $topRec = $recommendations[0];
        $this->assertEquals($freelancerDev->id, $topRec['freelancer']->id);
        $this->assertGreaterThan(50, $topRec['score']);
    }
}
