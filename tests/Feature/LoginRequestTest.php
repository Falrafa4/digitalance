<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_does_not_require_password_confirmation(): void
    {
        $client = Client::factory()->create([
            'email' => 'client@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login-process'), [
            'email' => $client->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('client.dashboard'));
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($client, 'client');
    }
}
