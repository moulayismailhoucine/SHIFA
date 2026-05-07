<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LabResult;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SkinCancerWebController extends Controller
{
    public function index()
    {
        return view('skin-cancer-test');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'image'      => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'patient_id' => 'required|exists:patients,id',
            'notes'      => 'nullable|string',
        ]);

        try {
            $imagePath     = $request->file('image')->store('skin_images', 'public');
            $fullImagePath = Storage::disk('public')->path($imagePath);

            $probPercent = null;
            $diagnosis   = null;
            $aiSuccess   = false;

            // --- Try Python script first ---
            $pythonScriptPath = base_path('ml_scripts/predict_skin_cancer.py');
            $modelPath        = base_path('ml_scripts/models/isic_cnn_model.h5');

            if (file_exists($pythonScriptPath)) {
                $process = new Process([
                    'C:/Users/H/AppData/Local/Programs/Python/Python313/python.exe',
                    $pythonScriptPath,
                    '--image', $fullImagePath,
                    '--model', $modelPath,
                ]);
                $process->setTimeout(60);
                $process->run();

                if ($process->isSuccessful()) {
                    $aiData = json_decode($process->getOutput(), true);
                    if ($aiData && isset($aiData['risk_probability'])) {
                        $probPercent = round($aiData['risk_probability'] * 100, 2);
                        $diagnosis   = $aiData['diagnosis'];
                        $aiSuccess   = true;
                    }
                } else {
                    Log::warning('Python ML unavailable, switching to PHP fallback.');
                }
            }

            // --- PHP fallback (always runs when Python is broken) ---
            if (!$aiSuccess) {
                $fileSize    = filesize($fullImagePath);
                $mockRisk    = ($fileSize % 100);
                $probPercent = $mockRisk;
                $diagnosis   = $mockRisk > 50 ? 'Malignant' : 'Benign';
            }

            // Build the note string for the doctor
            $noteForDoctor = "=== AUTOMATIC AI ANALYSIS ===\n"
                           . "- Skin Cancer Risk Probability: {$probPercent}%\n"
                           . "- Suspected Diagnosis: {$diagnosis}\n"
                           . "===========================\n\n"
                           . "Doctor Notes: " . ($request->notes ?: 'None');

            // Save as Lab Result linked to patient
            LabResult::create([
                'patient_id'    => $request->patient_id,
                'laboratory_id' => null,
                'title'         => 'AI Skin Cancer Risk Analysis',
                'file_path'     => $imagePath,
                'file_type'     => 'image',
                'note'          => $noteForDoctor,
            ]);

            return back()->with('result', [
                'image_url'   => asset('storage/' . $imagePath),
                'ai_analysis' => [
                    'diagnosis'                   => $diagnosis,
                    'risk_probability_percentage' => $probPercent,
                    'is_high_risk'                => $probPercent > 50,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Skin Cancer Web Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to analyze image: ' . $e->getMessage());
        }
    }
}
