<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\AiAnalysis;
use App\Models\Patient;
use App\Services\AiDiagnosisService;
use Illuminate\Http\Request;

class AiDiagnosisController extends Controller
{
    public function index(Request $request)
    {
        $aiService = new AiDiagnosisService();

        $patients = Patient::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'age', 'gender']);

        $analyses = AiAnalysis::with(['patient', 'doctor.user'])
            ->latest()
            ->limit(20)
            ->get();

        return view('doctor.ai-diagnosis', [
            'patients' => $patients,
            'analyses' => $analyses,
            'aiServiceOnline' => $aiService->isHealthy(),
        ]);
    }
}
