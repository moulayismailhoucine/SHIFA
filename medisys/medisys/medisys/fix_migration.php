<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Map migration filenames to table names for create migrations
$migrationsToCheck = [
    '2026_04_19_233954_create_fraud_attempts_table' => 'fraud_attempts',
    '2026_04_19_201029_create_contact_messages_table' => 'contact_messages',
    '2026_04_19_234056_create_contact_messages_table' => null, // duplicate, skip table check
    '2026_04_19_234455_update_contact_messages_table_structure' => null, // update migration
    '2026_04_21_133507_create_vitals_table' => 'vitals',
    '2026_04_21_132858_create_vital_signs_table' => 'vital_signs',
    '2026_04_21_143016_create_nurse_notes_table' => 'nurse_notes',
    '2026_04_22_140000_create_nurses_table' => 'nurses',
    '2026_04_21_214000_create_alerts_table' => 'alerts',
    '2026_04_20_140000_create_chats_table' => 'chats',
    '2026_04_17_234732_create_lab_results_table' => 'lab_results',
    '2026_04_18_082332_create_doctor_unavailabilities_table' => 'doctor_unavailabilities',
    '2026_04_20_221534_create_recommendations_table' => 'recommendations',
];

$removed = 0;
foreach ($migrationsToCheck as $migration => $table) {
    if ($table === null) continue;
    $exists = DB::select("SHOW TABLES LIKE '{$table}'");
    if (empty($exists)) {
        DB::table('migrations')->where('migration', $migration)->delete();
        echo "Removed record for missing table: {$table}\n";
        $removed++;
    } else {
        echo "Table exists: {$table}\n";
    }
}

echo "\nDone. Removed {$removed} migration records for missing tables.\n";
echo "Run 'php artisan migrate' to create missing tables.\n";
