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
        Schema::table('ordonnances', function (Blueprint $table) {
            $table->text('explanation')->nullable()->after('instructions');
            $table->boolean('explanation_generated')->default(false)->after('explanation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordonnances', function (Blueprint $table) {
            $table->dropColumn(['explanation', 'explanation_generated']);
        });
    }
};
