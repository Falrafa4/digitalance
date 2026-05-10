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
            $table->enum('status', ['Pending', 'Approved', 'Suspended', 'Rejected'])->default('Pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Approved', 'Suspended'])->default('Pending')->change();
        });
    }
};
