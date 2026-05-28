<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('profile_photo')->default('profiles/placeholder.webp')->after('phone');
        });

        Schema::table('freelancers', function (Blueprint $table) {
            $table->string('profile_photo')->default('profiles/placeholder.webp')->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });

        Schema::table('freelancers', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });
    }
};
