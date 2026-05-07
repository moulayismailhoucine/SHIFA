<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\MedicalRecord;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MedicationSummaryController extends Controller
{
    private string $pythonBin = 'C:/Users/H/AppData/Local/Programs/Python/Python313/python.exe';

    public function index()
    {
        $patients = Patient::all();  // Patient has name directly, no user relation
        return view('medication-summary', compact('patients'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
        ]);

        $patient = Patient::with(['medicalRecords', 'ordonnances', 'nurseNotes', 'vitalSigns'])->findOrFail($request->patient_id);

        // ── Patient name directly on model ─────────────────────────────────
        $patientName = $patient->name ?? 'Unknown';
        $medications = [];
        foreach ($patient->ordonnances as $ord) {
            $raw = $ord->medications ?? [];
            // Handle string JSON, plain string, or array
            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                $raw = is_array($decoded) ? $decoded : array_map('trim', explode(',', $raw));
            }
            foreach ((array) $raw as $m) {
                // Each item may be a string or an array ['name'=>..., 'dose'=>...]
                if (is_array($m)) {
                    $text = trim(implode(' ', array_filter([$m['name'] ?? '', $m['dose'] ?? ''])));
                } else {
                    $text = trim((string) $m);
                }
                if ($text) $medications[] = $text;
            }
        }
        $medications = array_values(array_unique(array_filter($medications)));

        // ── Collect diagnoses from medical records ─────────────────────────
        $diagnoses = $patient->medicalRecords
            ->pluck('diagnosis')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // ── Latest vitals ──────────────────────────────────────────────────
        $vitals = 'N/A';
        $latestVital = $patient->vitalSigns->sortByDesc('created_at')->first();
        if ($latestVital) {
            $parts = [];
            if ($latestVital->blood_pressure_systolic && $latestVital->blood_pressure_diastolic) {
                $parts[] = "BP:{$latestVital->blood_pressure_systolic}/{$latestVital->blood_pressure_diastolic}";
            }
            if ($latestVital->heart_rate)     $parts[] = "HR:{$latestVital->heart_rate}bpm";
            if ($latestVital->temperature)    $parts[] = "Temp:{$latestVital->temperature}°C";
            if ($latestVital->oxygen_saturation) $parts[] = "SpO2:{$latestVital->oxygen_saturation}%";
            if ($parts) $vitals = implode(', ', $parts);
        } else {
            $latest = $patient->medicalRecords->sortByDesc('visit_date')->first();
            if ($latest) {
                $parts = [];
                if ($latest->blood_pressure) $parts[] = "BP:{$latest->blood_pressure}";
                if ($latest->heart_rate)     $parts[] = "HR:{$latest->heart_rate}bpm";
                if ($latest->temperature)    $parts[] = "Temp:{$latest->temperature}°C";
                if ($parts) $vitals = implode(', ', $parts);
            }
        }

        // ── Latest Nurse Note ──────────────────────────────────────────────
        $latestNote = $patient->nurseNotes->sortByDesc('created_at')->first();
        if ($latestNote) {
            $vitals .= " | Recent Note: " . $latestNote->note;
        }

        $patientName = $patient->name ?? 'Unknown';
        $age         = $patient->age ?? 'N/A';

        if (empty($medications)) {
            return back()->with('error', 'No medication history found for this patient.');
        }

        // ── Run Python NLP script ──────────────────────────────────────────
        $summary     = null;
        $drugDetails = [];
        $warnings    = [];
        $nlpMethod   = 'Rule-based NLP';
        $aiSuccess   = false;

        try {
            $script = base_path('ml_scripts/summarize_medications.py');

            if (file_exists($script)) {
                $process = new Process([
                    $this->pythonBin,
                    $script,
                    '--medications', implode(', ', $medications),
                    '--diagnoses',   implode(', ', $diagnoses),
                    '--vitals',      $vitals,
                    '--patient_name', $patientName,
                    '--age',          (string) $age,
                ]);
                $process->setTimeout(90);
                $process->run();

                if ($process->isSuccessful()) {
                    $output = json_decode($process->getOutput(), true);
                    if ($output && $output['status'] === 'success') {
                        $summary     = $output['summary'];
                        $drugDetails = $output['drug_details'] ?? [];
                        $warnings    = $output['interaction_warnings'] ?? [];
                        $nlpMethod   = $output['nlp_method'] ?? 'Rule-based NLP';
                        $aiSuccess   = true;
                    }
                } else {
                    Log::warning('Medication summarizer Python error: ' . $process->getErrorOutput());
                }
            }
        } catch (\Exception $e) {
            Log::error('MedicationSummaryController: ' . $e->getMessage());
        }

        // ── Gemini API fallback ────────────────────────────────────────────
        if (!$aiSuccess) {
            [$summary, $nlpMethod] = $this->geminiSummarize($medications, $diagnoses, $vitals, $patientName, $age);
        }

        return back()->with('result', [
            'patient_name' => $patientName,
            'age'          => $age,
            'vitals'       => $vitals,
            'medications'  => $medications,
            'diagnoses'    => $diagnoses,
            'drug_details' => $drugDetails,
            'warnings'     => $warnings,
            'summary'      => $summary,
            'nlp_method'   => $nlpMethod,
        ]);
    }

    /** JSON endpoint called by doctor's patient-view via AJAX */
    public function generateJson(Request $request)
    {
        $request->validate(['patient_id' => 'required|exists:patients,id']);
        $patient = Patient::with(['medicalRecords', 'ordonnances', 'nurseNotes', 'vitalSigns'])->findOrFail($request->patient_id);

        $patientName = $patient->name ?? 'Unknown';
        $medications = [];
        foreach ($patient->ordonnances as $ord) {
            $raw = $ord->medications ?? [];
            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                $raw = is_array($decoded) ? $decoded : array_map('trim', explode(',', $raw));
            }
            foreach ((array) $raw as $m) {
                $text = is_array($m) ? trim(implode(' ', array_filter([$m['name'] ?? '', $m['dose'] ?? '']))) : trim((string) $m);
                if ($text) $medications[] = $text;
            }
        }
        $medications = array_values(array_unique(array_filter($medications)));

        if (empty($medications)) {
            return response()->json(['success' => false, 'message' => 'No medication history found for this patient.']);
        }

        $diagnoses = $patient->medicalRecords->pluck('diagnosis')->filter()->unique()->values()->toArray();
        $latestVital = $patient->vitalSigns->sortByDesc('created_at')->first();
        $vitals = 'N/A';
        if ($latestVital) {
            $parts = [];
            if ($latestVital->blood_pressure_systolic && $latestVital->blood_pressure_diastolic) {
                $parts[] = "BP:{$latestVital->blood_pressure_systolic}/{$latestVital->blood_pressure_diastolic}";
            }
            if ($latestVital->heart_rate)     $parts[] = "HR:{$latestVital->heart_rate}bpm";
            if ($latestVital->temperature)    $parts[] = "Temp:{$latestVital->temperature}°C";
            if ($latestVital->oxygen_saturation) $parts[] = "SpO2:{$latestVital->oxygen_saturation}%";
            if ($parts) $vitals = implode(', ', $parts);
        } else {
            $latest = $patient->medicalRecords->sortByDesc('visit_date')->first();
            if ($latest) {
                $parts = [];
                if ($latest->blood_pressure) $parts[] = "BP:{$latest->blood_pressure}";
                if ($latest->heart_rate)     $parts[] = "HR:{$latest->heart_rate}bpm";
                if ($latest->temperature)    $parts[] = "Temp:{$latest->temperature}°C";
                if ($parts) $vitals = implode(', ', $parts);
            }
        }
        
        $latestNote = $patient->nurseNotes->sortByDesc('created_at')->first();
        if ($latestNote) {
            $vitals .= " | Recent Note: " . $latestNote->note;
        }
        $age = $patient->age ?? 'N/A';

        // Run Python
        $summary = null; $drugDetails = []; $warnings = []; $nlpMethod = 'Rule-based NLP'; $aiSuccess = false;
        try {
            $script = base_path('ml_scripts/summarize_medications.py');
            if (file_exists($script)) {
                $process = new Process([$this->pythonBin, $script,
                    '--medications', implode(', ', $medications),
                    '--diagnoses',   implode(', ', $diagnoses),
                    '--vitals',      $vitals,
                    '--patient_name', $patientName,
                    '--age',          (string) $age,
                ]);
                $process->setTimeout(90);
                $process->run();
                if ($process->isSuccessful()) {
                    $out = json_decode($process->getOutput(), true);
                    if ($out && $out['status'] === 'success') {
                        $summary = $out['summary']; $drugDetails = $out['drug_details'] ?? [];
                        $warnings = $out['interaction_warnings'] ?? []; $nlpMethod = $out['nlp_method'] ?? 'Rule-based NLP';
                        $aiSuccess = true;
                    }
                }
            }
        } catch (\Exception $e) { Log::error('MedSummaryJson: ' . $e->getMessage()); }

        if (!$aiSuccess) {
            [$summary, $nlpMethod] = $this->geminiSummarize($medications, $diagnoses, $vitals, $patientName, $age);
        }

        return response()->json(['success' => true, 'result' => [
            'patient_name' => $patientName, 'age' => $age, 'vitals' => $vitals,
            'medications' => $medications, 'diagnoses' => $diagnoses,
            'drug_details' => $drugDetails, 'warnings' => $warnings,
            'summary' => $summary, 'nlp_method' => $nlpMethod,
        ]]);
    }

    /** Gemini API fallback when Python is unavailable */
    private function geminiSummarize(array $meds, array $diagnoses, string $vitals, string $name, $age): array
    {
        $apiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY', '');
        if (!$apiKey) {
            return [$this->phpRuleBasedSummary($meds, $diagnoses, $vitals), 'PHP Rule-based'];
        }

        try {
            $prompt = "You are a clinical pharmacist AI. Write a concise medical medication summary for a doctor.\n\n"
                    . "Patient: {$name}, Age: {$age}\n"
                    . "Diagnoses: " . implode(', ', $diagnoses) . "\n"
                    . "Medications: " . implode(', ', $meds) . "\n"
                    . "Vitals: {$vitals}\n\n"
                    . "Write a 3-4 sentence clinical summary covering: therapeutic goal, key medications with purposes, "
                    . "and monitoring recommendations. Be precise and professional.";

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(15)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['maxOutputTokens' => 300],
                ]);

            $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($text) return [trim($text), 'Gemini 2.0 Flash'];
        } catch (\Exception $e) {
            Log::warning('Gemini fallback failed: ' . $e->getMessage());
        }

        return [$this->phpRuleBasedSummary($meds, $diagnoses, $vitals), 'PHP Rule-based'];
    }

    /** Final fallback: pure PHP rule-based summary */
    private function phpRuleBasedSummary(array $meds, array $diagnoses, string $vitals): string
    {
        $count = count($meds);
        $dx    = $diagnoses ? implode(', ', $diagnoses) : 'unspecified conditions';
        $medList = implode(', ', $meds);

        return "Patient is currently managed for {$dx}. The medication regimen consists of {$count} "
             . "agent(s): {$medList}. Current vitals: {$vitals}. "
             . "Regular follow-up, medication adherence, and monitoring for adverse effects are recommended. "
             . "Review all medications for potential interactions and adjust therapy as needed.";
    }
}
