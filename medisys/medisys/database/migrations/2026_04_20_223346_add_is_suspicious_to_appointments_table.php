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
        Schema::table('appointments', function (Blueprint $table) {
            $table->boolean('is_suspicious')->default(false)->after('status');
            $table->text('fraud_reason')->nullable()->after('is_suspicious');
            $table->integer('fraud_score')->nullable()->after('fraud_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['is_suspicious', 'fraud_reason', 'fraud_score']);
        });
    }
};
