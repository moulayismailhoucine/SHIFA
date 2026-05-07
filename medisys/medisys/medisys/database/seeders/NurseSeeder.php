<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class NurseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'nurse@medisys.com'],
            [
                'name' => 'Demo Nurse',
                'email' => 'nurse@medisys.com',
                'password' => Hash::make('password'),
                'role' => 'nurse',
                'code' => 'NURSE001',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Demo nurse account created successfully.');
        $this->command->info('Email: nurse@medisys.com');
        $this->command->info('Password: password');
    }
}
