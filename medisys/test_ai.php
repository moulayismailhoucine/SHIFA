<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\PrescriptionAIService;
use App\Models\Ordonnance;

echo "=== Testing Prescription AI Service ===\n";

// Check prescriptions without explanations
$prescriptions = Ordonnance::whereNull('explanation')->orWhere('explanation_generated', false)->get();
echo "Prescriptions without explanation: " . $prescriptions->count() . "\n";

if ($prescriptions->count() > 0) {
    $sample = $prescriptions->first();
    echo "Sample prescription ID: " . $sample->id . "\n";
    echo "Medications: " . json_encode($sample->medications) . "\n";
    echo "Instructions: " . $sample->instructions . "\n";
    
    // Test AI service
    $aiService = new PrescriptionAIService();
    $result = $aiService->generateExplanation($sample);
    
    echo "AI Service Result:\n";
    echo "Success: " . ($result['success'] ? 'true' : 'false') . "\n";
    echo "Error: " . ($result['error'] ?? 'none') . "\n";
    echo "Explanation: " . ($result['explanation'] ?? 'none') . "\n";
}

echo "=== End Test ===\n";
