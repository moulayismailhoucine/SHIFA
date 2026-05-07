<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiAnalysis;
use App\Services\AiDiagnosisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AiAnalysisController extends Controller
{
    protected AiDiagnosisService $aiService;

    public function __construct(AiDiagnosisService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index(Request $request)
    {
        $query = AiAnalysis::with(['patient', 'doctor.user'])->latest();

        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->user() && $request->user()->isDoctor()) {
            $query->where('doctor_id', $request->user()->doctor->id);
        }

        return response()->json(['success' => true, 'data' => $query->paginate(20)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'image'      => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'image_type' => 'required|in:skin,xray',
            'doctor_notes' => 'nullable|string',
        ]);

        $doctorId = $request->user()->doctor->id;

        // Store image
        $imagePath = $request->file('image')->store('ai_analyses/' . $validated['image_type'], 'public');

        // Call AI service
        $aiResult = $this->aiService->analyzeImage($imagePath, $validated['image_type']);

        if (!$aiResult['success']) {
            Storage::disk('public')->delete($imagePath);
            return response()->json([
                'success' => false,
                'message' => $aiResult['error'] ?? 'AI analysis failed.',
            ], 500);
        }

        // Save analysis
        $analysis = AiAnalysis::create([
            'patient_id'      => $validated['patient_id'],
            'doctor_id'       => $doctorId,
            'image_path'      => $imagePath,
            'image_type'      => $validated['image_type'],
            'ai_prediction'   => $aiResult['prediction'],
            'confidence_score'  => $aiResult['confidence'],
            'concern_level'   => $aiResult['concern_level'],
            'details'         => [
                'all_predictions' => $aiResult['all_predictions'],
                'ai_type'         => $aiResult['type'],
            ],
            'doctor_notes'    => $validated['doctor_notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'AI analysis completed.',
            'data'    => $analysis->load(['patient', 'doctor.user']),
        ], 201);
    }

    public function show(AiAnalysis $aiAnalysis)
    {
        return response()->json([
            'success' => true,
            'data'    => $aiAnalysis->load(['patient', 'doctor.user']),
        ]);
    }

    public function update(Request $request, AiAnalysis $aiAnalysis)
    {
        $validated = $request->validate([
            'status'       => 'sometimes|in:pending,reviewed,confirmed,dismissed',
            'doctor_notes' => 'sometimes|string',
        ]);

        $aiAnalysis->update($validated);

        return response()->json(['success' => true, 'data' => $aiAnalysis->fresh()]);
    }

    public function destroy(AiAnalysis $aiAnalysis)
    {
        Storage::disk('public')->delete($aiAnalysis->image_path);
        $aiAnalysis->delete();

        return response()->json(['success' => true, 'message' => 'Analysis deleted.']);
    }

    public function health()
    {
        return response()->json([
            'success' => true,
            'ai_service_online' => $this->aiService->isHealthy(),
        ]);
    }
}
