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
        Schema::table('freelancers', function (Blueprint $table) {
            $table->string('career_track')->nullable()->after('reject_reason');
            $table->enum('career_track_status', ['None', 'Pending', 'Approved', 'Rejected'])->default('None')->after('career_track');
            $table->text('career_track_notes')->nullable()->after('career_track_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            $table->dropColumn(['career_track', 'career_track_status', 'career_track_notes']);
        });
    }
};
