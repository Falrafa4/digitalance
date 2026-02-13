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
        Schema::create('skomda_students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 9);
            $table->string('name');
            $table->string('email')->unique();
            $table->string('class');
            $table->enum('major', ['TJAT', 'SIJA']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skomda_students');
    }
};
