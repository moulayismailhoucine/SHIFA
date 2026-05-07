<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Ordonnance;
use App\Jobs\GeneratePrescriptionExplanationJob;

echo "Triggering prescription explanation job...\n";

$prescription = Ordonnance::find(1);
if ($prescription) {
    GeneratePrescriptionExplanationJob::dispatch($prescription);
    echo "Job dispatched for prescription ID: " . $prescription->id . "\n";
} else {
    echo "Prescription not found\n";
}

echo "Done.\n";
