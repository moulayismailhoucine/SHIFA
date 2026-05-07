<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enhance nursing_orders with full scheduling and result tracking
        Schema::table('nursing_orders', function (Blueprint $table) {
            $table->text('instructions')->nullable()->after('notes');   // Detailed task instructions
            $table->string('scheduled_time')->nullable()->after('instructions'); // e.g. "08:00,14:00,20:00"
            $table->date('start_date')->nullable()->after('scheduled_time');
            $table->date('end_date')->nullable()->after('start_date');
            $table->integer('interval_hours')->nullable()->after('end_date'); // recurring every X hours
            $table->text('result')->nullable()->after('interval_hours'); // nurse-recorded result
            $table->timestamp('completed_at')->nullable()->after('result');
            $table->timestamp('last_executed_at')->nullable()->after('completed_at');
            $table->boolean('is_overdue')->default(false)->after('last_executed_at');
        });

        // Nurse Doctor audit log
        Schema::create('nurse_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nurse_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('nursing_order_id')->nullable()->constrained('nursing_orders')->onDelete('set null');
            $table->string('action');       // e.g. 'task_completed', 'vitals_recorded', 'note_added'
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nurse_audit_logs');

        Schema::table('nursing_orders', function (Blueprint $table) {
            $table->dropColumn([
                'instructions','scheduled_time','start_date','end_date',
                'interval_hours','result','completed_at','last_executed_at','is_overdue'
            ]);
        });
    }
};
