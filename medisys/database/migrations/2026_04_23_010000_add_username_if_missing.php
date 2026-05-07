<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'username')) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('ALTER TABLE users ADD COLUMN username VARCHAR(255) UNIQUE NULL');
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('username')->unique()->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'username')) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS username');
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('username');
                });
            }
        }
    }
};
