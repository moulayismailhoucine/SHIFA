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
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->time('working_hours_start')->nullable();
            $table->time('working_hours_end')->nullable();
        });

        Schema::table('laboratories', function (Blueprint $table) {
            $table->time('working_hours_start')->nullable();
            $table->time('working_hours_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->dropColumn(['working_hours_start', 'working_hours_end']);
        });

        Schema::table('laboratories', function (Blueprint $table) {
            $table->dropColumn(['working_hours_start', 'working_hours_end']);
        });
    }
};
