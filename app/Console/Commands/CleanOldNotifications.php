<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class CleanOldNotifications extends Command
{
    protected $signature = 'notifications:clean-old';

    protected $description = 'Hapus notifikasi yang berumur lebih dari 30 hari';

    public function handle(): int
    {
        $deleted = Notification::where('created_at', '<', now()->subDays(30))->delete();
        $this->info("Berhasil menghapus {$deleted} notifikasi lama.");

        return Command::SUCCESS;
    }
}
