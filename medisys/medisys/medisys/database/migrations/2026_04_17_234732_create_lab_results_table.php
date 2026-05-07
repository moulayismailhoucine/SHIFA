<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('laboratory_id')->nullable()->constrained('laboratories')->nullOnDelete();
            $table->string('file_path');
            $table->enum('file_type', ['image', 'pdf'])->default('pdf');
            $table->string('title')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Add dispensed_by and dispensed_at to ordonnances
        Schema::table('ordonnances', function (Blueprint $table) {
            $table->foreignId('dispensed_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->timestamp('dispensed_at')->nullable()->after('dispensed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
        Schema::table('ordonnances', function (Blueprint $table) {
            $table->dropForeign(['dispensed_by']);
            $table->dropColumn(['dispensed_by', 'dispensed_at']);
        });
    }
};
