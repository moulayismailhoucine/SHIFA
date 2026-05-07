<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PrescriptionAIService;
use App\Models\Ordonnance;

class TestPrescriptionAI extends Command
{
    protected $signature = 'test:prescription-ai';
    protected $description = 'Test prescription AI service';

    public function handle()
    {
        $this->info('Testing Prescription AI Service...');
        
        // Check prescriptions without explanations
        $prescriptions = Ordonnance::whereNull('explanation')->orWhere('explanation_generated', false)->get();
        $this->info('Prescriptions without explanation: ' . $prescriptions->count());

        if ($prescriptions->count() > 0) {
            $sample = $prescriptions->first();
            $this->info('Sample prescription ID: ' . $sample->id);
            $this->info('Medications: ' . json_encode($sample->medications));
            $this->info('Instructions: ' . $sample->instructions);
            
            // Test AI service
            $aiService = new PrescriptionAIService();
            $medications = json_encode($sample->medications);
            $instructions = $sample->instructions;
            $result = $aiService->generateExplanation($medications, $instructions);
            
            $this->info('AI Service Result:');
            $this->info('Success: ' . ($result['success'] ? 'true' : 'false'));
            $this->info('Error: ' . ($result['error'] ?? 'none'));
            $this->info('Explanation: ' . ($result['explanation'] ?? 'none'));
        } else {
            $this->info('No prescriptions to test');
        }
        
        $this->info('Test completed.');
        return 0;
    }
}
