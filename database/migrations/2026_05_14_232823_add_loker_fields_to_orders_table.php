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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('freelancer_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            $table->foreignId('loker_application_id')->nullable()->after('freelancer_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['freelancer_id']);
            $table->dropForeign(['loker_application_id']);
            $table->dropColumn(['freelancer_id', 'loker_application_id']);
        });
    }
};
