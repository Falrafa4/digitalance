<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $user) {
            $user->id();
            $user->string('title');
            $user->text('message');
            $user->string('type')->default('info'); // info, success, warning, danger
            $user->string('role')->nullable(); // admin, client, freelancer
            $user->unsignedBigInteger('user_id')->nullable(); // Target user id
            $user->string('link')->nullable(); // Clickable link
            $user->boolean('is_read')->default(false);
            $user->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
