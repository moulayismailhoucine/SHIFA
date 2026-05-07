<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Pharmacy;
use App\Models\Laboratory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────
        User::create([
            'name'     => 'System Administrator',
            'email'    => env('ADMIN_EMAIL', 'admin@shifa.local'),
            'username' => 'admin',
            'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin@1234')),
            'role'     => 'admin',
        ]);

        // ── Doctors ────────────────────────────────────────────────
        $doctorsData = [
            ['name' => 'Dr. Ahmed Benali',  'email' => 'ahmed.benali@shifa.local',  'specialty' => 'Cardiologie'],
            ['name' => 'Dr. Fatima Zohra',  'email' => 'fatima.zohra@shifa.local',  'specialty' => 'Pédiatrie'],
            ['name' => 'Dr. Karim Hadj',    'email' => 'karim.hadj@shifa.local',    'specialty' => 'Neurologie'],
            ['name' => 'Dr. Sara Meziane',  'email' => 'sara.meziane@shifa.local',  'specialty' => 'Dermatologie'],
        ];

        foreach ($doctorsData as $d) {
            $user = User::create([
                'name'     => $d['name'],
                'email'    => $d['email'],
                'password' => Hash::make('Doctor@1234'),
                'role'     => 'doctor',
            ]);
            Doctor::create([
                'user_id'   => $user->id,
                'specialty' => $d['specialty'],
                'phone'     => '+213 5' . rand(10000000, 99999999),
            ]);
        }

        // ── Nurses ────────────────────────────────────────────────
        User::create([
            'name'     => 'Head Nurse Sarah',
            'email'    => 'nurse@shifa.local',
            'username' => 'nurse',
            'password' => Hash::make('Nurse@1234'),
            'role'     => 'nurse',
        ]);

        // ── Patients ───────────────────────────────────────────────
        $patients = [
            ['name' => 'Mohammed Amine',  'age' => 34, 'gender' => 'male',   'nfc_uid' => 'NFC001ABC'],
            ['name' => 'Aicha Mansouri',  'age' => 27, 'gender' => 'female', 'nfc_uid' => 'NFC002DEF'],
            ['name' => 'Omar Belkacem',   'age' => 52, 'gender' => 'male',   'nfc_uid' => 'NFC003GHI'],
            ['name' => 'Nadia Cherif',    'age' => 19, 'gender' => 'female', 'nfc_uid' => 'NFC004JKL'],
        ];

        foreach ($patients as $p) {
            Patient::create(array_merge($p, [
                'phone'      => '+213 6' . rand(10000000, 99999999),
                'blood_type' => ['A+', 'A-', 'B+', 'O+', 'AB+'][rand(0, 4)],
            ]));
        }

        // ── Pharmacies (email + password login) ────────────────────
        $pharmacies = [
            [
                'name'     => 'Pharmacie Al Shifa',
                'email'    => 'alshifa@pharmacie.shifa',
                'username' => 'pharmacy_alshifa',
                'address'  => '12 Rue Didouche Mourad, Alger',
                'phone'    => '+213 21 000 001',
                'password' => 'Pharma@1234',
            ],
            [
                'name'     => 'Pharmacie Centrale',
                'email'    => 'centrale@pharmacie.shifa',
                'username' => 'pharmacy_centrale',
                'address'  => '45 Av. Ben Mhidi, Oran',
                'phone'    => '+213 41 000 002',
                'password' => 'Pharma@1234',
            ],
        ];

        foreach ($pharmacies as $ph) {
            $user = User::create([
                'name'     => $ph['name'],
                'email'    => $ph['email'],
                'username' => $ph['username'],
                'password' => Hash::make($ph['password']),
                'role'     => 'pharmacy',
            ]);
            Pharmacy::create([
                'user_id' => $user->id,
                'name'    => $ph['name'],
                'address' => $ph['address'],
                'phone'   => $ph['phone'],
                'email'   => $ph['email'],
            ]);
        }

        // ── Laboratories (email + password login) ──────────────────
        $labs = [
            [
                'name'           => 'Laboratoire Bio-Médical',
                'email'          => 'bio@labo.shifa',
                'username'       => 'lab_bio',
                'address'        => '7 Rue Larbi Ben M\'hidi, Alger',
                'specialization' => 'Analyses biologiques',
                'password'       => 'Lab@1234',
            ],
            [
                'name'           => 'Labo Imagerie Médicale',
                'email'          => 'imagerie@labo.shifa',
                'username'       => 'lab_imagerie',
                'address'        => '3 Bd Colonel Amirouche, Constantine',
                'specialization' => 'Radiologie & IRM',
                'password'       => 'Lab@1234',
            ],
        ];

        foreach ($labs as $lb) {
            $user = User::create([
                'name'     => $lb['name'],
                'email'    => $lb['email'],
                'username' => $lb['username'],
                'password' => Hash::make($lb['password']),
                'role'     => 'lab',
            ]);
            Laboratory::create([
                'user_id'        => $user->id,
                'name'           => $lb['name'],
                'address'        => $lb['address'],
                'email'          => $lb['email'],
                'specialization' => $lb['specialization'],
            ]);
        }
    }
}
