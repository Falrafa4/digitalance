<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokkers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['Open', 'Closed'])->default('Open');
            $table->timestamps();
        });

        Schema::create('loker_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loker_id')->constrained('lokkers')->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained()->cascadeOnDelete();
            $table->text('proposal')->nullable();
            $table->decimal('proposed_price', 12, 2)->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loker_applications');
        Schema::dropIfExists('lokkers');
    }
};
