<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->string('image_path');
            $table->string('image_type')->default('skin'); // skin, xray
            $table->string('ai_prediction');
            $table->decimal('confidence_score', 5, 2);
            $table->enum('concern_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->json('details')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'confirmed', 'dismissed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analyses');
    }
};
