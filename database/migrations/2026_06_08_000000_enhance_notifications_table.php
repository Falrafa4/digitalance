<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('category', 50)->nullable()->after('type');
            $table->boolean('is_archived')->default(false)->after('is_kept');
            $table->timestamp('read_at')->nullable()->after('is_read');
            $table->string('group_key', 191)->nullable()->after('link');

            $table->index(['role', 'user_id', 'is_read']);
            $table->index(['role', 'user_id', 'is_archived']);
            $table->index(['role', 'user_id', 'is_kept']);
            $table->index('group_key');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['role', 'user_id', 'is_read']);
            $table->dropIndex(['role', 'user_id', 'is_archived']);
            $table->dropIndex(['role', 'user_id', 'is_kept']);
            $table->dropIndex(['group_key']);

            $table->dropColumn(['category', 'is_archived', 'read_at', 'group_key']);
        });
    }
};
