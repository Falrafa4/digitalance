<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->enum('result_mode', ['file', 'link'])->default('file')->after('file_url');
        });

        DB::table('results')
            ->where('file_url', 'like', 'http://%')
            ->orWhere('file_url', 'like', 'https://%')
            ->update(['result_mode' => 'link']);

        DB::table('results')
            ->where('file_url', 'not like', 'http://%')
            ->where('file_url', 'not like', 'https://%')
            ->update(['result_mode' => 'file']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            if (Schema::hasColumn('results', 'result_mode')) {
                $table->dropColumn('result_mode');
            }
        });
    }
};
