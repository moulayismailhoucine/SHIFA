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
        Schema::table('nurses', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->constrained()->onDelete('set null');
        });

        Schema::table('nurse_notes', function (Blueprint $table) {
            $table->integer('pain_level')->nullable(); // 0-10
            $table->string('attachment_path')->nullable();
        });

        Schema::create('nursing_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('nurse_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('type'); // Medication, Monitoring, Care Procedure
            $table->string('dosage_method')->nullable();
            $table->string('schedule')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['Pending', 'Ongoing', 'Completed'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nursing_orders');

        Schema::table('nurse_notes', function (Blueprint $table) {
            $table->dropColumn(['pain_level', 'attachment_path']);
        });

        Schema::table('nurses', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropColumn('doctor_id');
        });
    }
};
