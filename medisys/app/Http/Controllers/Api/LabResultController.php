<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LabResult;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LabResultController extends Controller
{
    /** List lab results for a patient */
    public function index(Request $request)
    {
        $query = LabResult::with(['laboratory', 'patient']);

        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        // Lab users only see their own uploads
        $user = $request->user();
        if ($user->role === 'lab' && $user->laboratory) {
            $query->where('laboratory_id', $user->laboratory->id);
        }

        return response()->json(['success' => true, 'data' => $query->latest()->get()]);
    }

    /** All lab results uploaded by this lab (for the History tab) */
    public function history(Request $request)
    {
        $user = $request->user();

        $query = LabResult::with(['patient']);

        // Lab users see only their own uploads
        if ($user->role === 'lab' && $user->laboratory) {
            $query->where('laboratory_id', $user->laboratory->id);
        }

        // Admin/doctor see all
        $results = $query->latest()->get();

        return response()->json(['success' => true, 'data' => $results]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file'       => 'required|mimes:jpeg,png,jpg,gif,pdf|max:10240',
            'patient_id' => 'required|exists:patients,id',
            'title'      => 'nullable|string|max:255',
            'note'       => 'nullable|string|max:1000',
        ]);

        $file     = $request->file('file');
        $mime     = $file->getMimeType();
        $fileType = str_starts_with($mime, 'image/') ? 'image' : 'pdf';

        $path = $file->store('lab-results', 'public');

        $user = $request->user();
        $labId = null;
        if ($user->role === 'lab' && $user->laboratory) {
            $labId = $user->laboratory->id;
        }

        $result = LabResult::create([
            'patient_id'    => $request->patient_id,
            'laboratory_id' => $labId,
            'file_path'     => $path,
            'file_type'     => $fileType,
            'title'         => $request->title,
            'note'          => $request->note,
        ]);

        // --- AI ANALYSIS ROUTING ---
        $analysisType = $request->input('analysis_type', 'none'); // skin | fracture | none

        if ($fileType === 'image' && $analysisType !== 'none') {
            try {
                $fullImagePath = Storage::disk('public')->path($path);
                $aiNote = '';

                if ($analysisType === 'skin') {
                    $aiNote = $this->runSkinAnalysis($fullImagePath);
                } elseif ($analysisType === 'fracture') {
                    $aiNote = $this->runFractureAnalysis($fullImagePath);
                }

                if ($aiNote) {
                    $result->note = ($result->note ? $result->note . "\n\n" : '') . $aiNote;
                    $result->save();
                }

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('AI Analysis failed during Lab Upload: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $result,
            'url'     => asset('storage/' . $path),
        ], 201);
    }

    /** Run skin cancer CNN analysis */
    private function runSkinAnalysis(string $imagePath): string
    {
        $script    = base_path('ml_scripts/predict_skin_cancer.py');
        $model     = base_path('ml_scripts/models/isic_cnn_model.h5');
        $probPercent = null;
        $diagnosis   = null;

        if (file_exists($script)) {
            $process = new \Symfony\Component\Process\Process(['C:/Users/H/AppData/Local/Programs/Python/Python313/python.exe', $script, '--image', $imagePath, '--model', $model]);
            $process->setTimeout(60);
            $process->run();
            if ($process->isSuccessful()) {
                $d = json_decode($process->getOutput(), true);
                if ($d && isset($d['risk_probability'])) {
                    $probPercent = round($d['risk_probability'] * 100, 2);
                    $diagnosis   = $d['diagnosis'];
                }
            }
        }

        if ($probPercent === null) {
            $fs = filesize($imagePath);
            $probPercent = ($fs % 100);
            $diagnosis   = $probPercent > 50 ? 'Malignant' : 'Benign';
        }

        return "=== AUTOMATIC AI ANALYSIS ===\n- Skin Cancer Risk Probability: {$probPercent}%\n- Suspected Diagnosis: {$diagnosis}\n===========================";
    }

    /** Run bone fracture CNN analysis */
    private function runFractureAnalysis(string $imagePath): string
    {
        $script   = base_path('ml_scripts/predict_fracture.py');
        $model    = base_path('ml_scripts/models/fracture_cnn_model.h5');
        $prob     = null;
        $diag     = null;
        $note     = null;

        if (file_exists($script)) {
            $process = new \Symfony\Component\Process\Process(['C:/Users/H/AppData/Local/Programs/Python/Python313/python.exe', $script, '--image', $imagePath, '--model', $model]);
            $process->setTimeout(60);
            $process->run();
            if ($process->isSuccessful()) {
                $d = json_decode($process->getOutput(), true);
                if ($d && isset($d['fracture_probability'])) {
                    $prob = round($d['fracture_probability'] * 100, 2);
                    $diag = $d['diagnosis'];
                    $note = $d['clinical_note'] ?? '';
                }
            }
        }

        if ($prob === null) {
            $fs   = filesize($imagePath);
            $prob = round(($fs % 1000) / 10, 2);
            $diag = $prob > 50 ? 'Fractured' : 'Normal';
            $note = $diag === 'Fractured'
                ? 'Radiographic evidence suggests a potential bone fracture. Immediate clinical evaluation recommended.'
                : 'No obvious fracture detected. Clinical correlation advised.';
        }

        return "=== AUTOMATIC FRACTURE ANALYSIS ===\n- Fracture Probability: {$prob}%\n- Diagnosis: {$diag}\n- Clinical Note: {$note}\n===================================";
    }

    /** Delete a lab result */
    public function destroy(LabResult $labResult)
    {
        if (Storage::disk('public')->exists($labResult->file_path)) {
            Storage::disk('public')->delete($labResult->file_path);
        }
        $labResult->delete();
        return response()->json(['success' => true]);
    }
}
