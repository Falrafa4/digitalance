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
        Schema::table('negotiations', function (Blueprint $table) {
            $table->decimal('proposed_price', 12, 2)->nullable()->after('message');
            $table->string('reason', 500)->nullable()->after('proposed_price');
            $table->text('description')->nullable()->after('reason');
            $table->string('status', 50)->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('negotiations', function (Blueprint $table) {
            $table->dropColumn(['proposed_price', 'reason', 'description', 'status']);
        });
    }
};
