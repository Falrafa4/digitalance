<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationComposerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_layout_uses_notification_composer(): void
    {
        $administrator = Administrator::create([
            'name' => 'Test Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $recentNotification = Notification::create([
            'title' => 'Notifikasi Baru',
            'message' => 'Pesan notifikasi terbaru masih tampil di drawer.',
            'type' => 'success',
            'role' => 'admin',
            'user_id' => $administrator->id,
            'link' => '/admin',
            'is_read' => false,
            'is_kept' => false,
        ]);

        $oldNotification = Notification::create([
            'title' => 'Notifikasi Lama',
            'message' => 'Pesan ini harus terhapus oleh composer.',
            'type' => 'warning',
            'role' => 'admin',
            'user_id' => $administrator->id,
            'link' => '/admin',
            'is_read' => false,
            'is_kept' => false,
        ]);

        Notification::query()
            ->whereKey($recentNotification->id)
            ->update([
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);

        Notification::query()
            ->whereKey($oldNotification->id)
            ->update([
                'created_at' => now()->subDays(31),
                'updated_at' => now()->subDays(31),
            ]);

        $response = $this->actingAs($administrator, 'administrator')->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Notifikasi Baru');
        $response->assertSeeText('Pesan notifikasi terbaru masih tampil di drawer.');
        $response->assertSeeText('1 belum dibaca');

        $this->assertDatabaseHas('notifications', [
            'id' => $recentNotification->id,
        ]);

        $this->assertDatabaseMissing('notifications', [
            'id' => $oldNotification->id,
        ]);
    }
}