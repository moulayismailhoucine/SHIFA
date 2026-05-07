<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LabResult;
use App\Http\Resources\SkinCancerAnalysisResource;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SkinCancerAnalysisController extends Controller
{
    /**
     * Analyze skin image using the CNN model
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'patient_id' => 'required|exists:patients,id',
            'notes' => 'nullable|string',
        ]);

        try {
            // 1. Store the image
            $imagePath = $request->file('image')->store('skin_images', 'public');
            $fullImagePath = Storage::disk('public')->path($imagePath);

            // 2. Call the Python script (mock implementation)
            $pythonScriptPath = base_path('ml_scripts/predict_skin_cancer.py');
            $modelPath = base_path('ml_scripts/models/isic_cnn_model.h5');

            // Ensure the script exists
            if (!file_exists($pythonScriptPath)) {
                throw new \Exception("ML model script not found. Please ensure the Python environment is setup.");
            }

            $process = new Process(['python', $pythonScriptPath, '--image', $fullImagePath, '--model', $modelPath]);
            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('Python ML Process Failed: ' . $process->getErrorOutput());
                throw new ProcessFailedException($process);
            }

            $output = $process->getOutput();
            $result = json_decode($output, true);

            if (!$result || !isset($result['risk_probability'])) {
                throw new \Exception("Invalid output from ML model.");
            }

            // 3. Save as a Lab Result for the doctor
            $labResult = LabResult::create([
                'patient_id' => $request->patient_id,
                'laboratory_id' => auth()->user()->laboratory_id ?? null, // Optional, depending on who runs it
                'title' => 'AI Skin Cancer Risk Analysis',
                'file_path' => $imagePath,
                'file_type' => 'image',
                'note' => "AI Analysis Notes:\n- Risk Probability: " . ($result['risk_probability'] * 100) . "%\n- Diagnosis: " . $result['diagnosis'] . "\n\nAdditional Notes: " . $request->notes,
            ]);

            // 4. Return via Resource
            return new SkinCancerAnalysisResource($labResult);

        } catch (\Exception $e) {
            Log::error('Skin Cancer Analysis Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze image: ' . $e->getMessage()
            ], 500);
        }
    }
}
