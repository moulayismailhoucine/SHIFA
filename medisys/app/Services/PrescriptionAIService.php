<?php

namespace App\Services;

use App\Models\MedicineDictionary;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Support\Facades\Log;

class PrescriptionAIService
{
    /**
     * Generate explanation for prescription using local Medicine Dictionary
     *
     * @param string|array $medications
     * @param string $instructions
     * @param string $language
     * @return array
     */
    public function generateExplanation($medications, string $instructions, string $language = 'ar'): array
    {
        try {
            $medicationsText = is_array($medications) ? implode(', ', $medications) : $medications;
            
            // Fetch all medicines from our dictionary
            $allMedicines = MedicineDictionary::all();
            
            $foundExplanations = [];
            $medsList = strtolower($medicationsText);

            foreach ($allMedicines as $medicine) {
                // If the medicine name is found in the prescribed medications
                if (str_contains($medsList, strtolower($medicine->name))) {
                    $explanationText = $language === 'ar' && !empty($medicine->explanation_ar) 
                        ? $medicine->explanation_ar 
                        : $medicine->explanation;
                        
                    $foundExplanations[] = "**{$medicine->name}**: {$explanationText}";
                }
            }

            if (empty($foundExplanations)) {
                $defaultMsg = $language === 'ar' 
                    ? "يرجى اتباع تعليمات الطبيب: {$instructions}" 
                    : "Please follow doctor's instructions: {$instructions}";
                    
                return [
                    'success' => true,
                    'explanation' => $defaultMsg
                ];
            }

            $intro = $language === 'ar' ? "شرح الأدوية الموصوفة:\n" : "Prescribed Medications Explanation:\n";
            $explanation = $intro . implode("\n", $foundExplanations);

            if (!empty($instructions)) {
                $instructionsHeader = $language === 'ar' ? "\n\nتعليمات إضافية:\n" : "\n\nAdditional Instructions:\n";
                $explanation .= $instructionsHeader . $instructions;
            }

            return [
                'success' => true,
                'explanation' => $explanation
            ];

        } catch (\Exception $e) {
            Log::error('PrescriptionAIService: Exception occurred', [
                'message' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => 'Dictionary service error'
            ];
        }
    }

    /**
     * Generate a summary explanation of the patient's general status
     * and update the latest medical record.
     *
     * @param Patient $patient
     * @return bool
     */
    public function updatePatientGeneralStatusSummary(Patient $patient): bool
    {
        try {
            $latestRecord = MedicalRecord::where('patient_id', $patient->id)
                ->orderBy('visit_date', 'desc')
                ->first();

            if (!$latestRecord) {
                return false;
            }

            // Generate a summary based on recent activity, prescriptions, vitals, etc.
            $summaryParts = [];
            
            if ($latestRecord->diagnosis) {
                $summaryParts[] = "Current Diagnosis: " . $latestRecord->diagnosis;
            }
            
            if ($latestRecord->notes) {
                $summaryParts[] = "Notes: " . $latestRecord->notes;
            }

            // You could query recent Ordonnances
            $recentOrdonnance = $patient->ordonnances()->orderBy('created_at', 'desc')->first();
            if ($recentOrdonnance) {
                $meds = is_array($recentOrdonnance->medications) 
                    ? implode(', ', $recentOrdonnance->medications) 
                    : $recentOrdonnance->medications;
                $summaryParts[] = "Recent Medications: " . $meds;
            }

            // Vitals
            $vitalsStr = [];
            if ($latestRecord->blood_pressure) $vitalsStr[] = "BP: {$latestRecord->blood_pressure}";
            if ($latestRecord->heart_rate) $vitalsStr[] = "HR: {$latestRecord->heart_rate} bpm";
            if ($latestRecord->temperature) $vitalsStr[] = "Temp: {$latestRecord->temperature}°C";
            
            if (!empty($vitalsStr)) {
                $summaryParts[] = "Latest Vitals: " . implode(', ', $vitalsStr);
            }

            $summary = implode("\n", $summaryParts);

            if (empty($summary)) {
                $summary = "No significant recent medical data available for summary.";
            }

            // Save to medical record
            $latestRecord->general_status_summary = $summary;
            $latestRecord->save();

            return true;

        } catch (\Exception $e) {
            Log::error('PrescriptionAIService summary error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Detect patient language from their profile or default to Arabic
     *
     * @param mixed $patient
     * @return string
     */
    public function detectPatientLanguage($patient = null): string
    {
        return 'ar';
    }
}

