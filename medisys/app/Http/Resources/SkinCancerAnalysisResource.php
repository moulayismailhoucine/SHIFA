<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkinCancerAnalysisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Parse the probability from notes (since it was saved there)
        preg_match('/Risk Probability: ([\d.]+)%/', $this->note, $probMatches);
        preg_match('/Diagnosis: ([A-Za-z]+)/', $this->note, $diagMatches);

        $probability = $probMatches[1] ?? null;
        $diagnosis = $diagMatches[1] ?? 'Unknown';

        return [
            'success' => true,
            'message' => 'Image analyzed successfully.',
            'data' => [
                'result_id' => $this->id,
                'patient_id' => $this->patient_id,
                'image_url' => asset('storage/' . $this->file_path),
                'ai_analysis' => [
                    'diagnosis' => $diagnosis,
                    'risk_probability_percentage' => $probability,
                    'is_high_risk' => $probability > 50,
                ],
                'created_at' => $this->created_at->toDateTimeString(),
            ]
        ];
    }
}
