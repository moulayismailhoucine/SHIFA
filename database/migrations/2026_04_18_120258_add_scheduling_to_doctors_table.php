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
        Schema::table('doctors', function (Blueprint $table) {
            $table->json('working_days')->nullable(); // e.g. ["Monday", "Tuesday"]
            $table->time('working_hours_start')->nullable(); // e.g. 08:00:00
            $table->time('working_hours_end')->nullable(); // e.g. 16:00:00
            $table->integer('treatment_time')->default(30); // in minutes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['working_days', 'working_hours_start', 'working_hours_end', 'treatment_time']);
        });
    }
};
