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
        Schema::table('users', function (Blueprint $table) {
            $table->string('code')->nullable()->unique();
            $table->enum('role', ['admin', 'doctor', 'nurse', 'patient', 'pharmacy', 'lab'])->default('doctor')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->enum('role', ['admin', 'doctor', 'patient', 'pharmacy', 'lab'])->default('doctor')->change();
        });
    }
};
