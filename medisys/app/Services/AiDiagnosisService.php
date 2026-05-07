<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AiDiagnosisService
{
    protected string $endpoint;

    public function __construct()
    {
        $this->endpoint = config('services.ai.endpoint', 'http://127.0.0.1:8001');
    }

    /**
     * Send image to AI microservice for analysis
     */
    public function analyzeImage(string $imagePath, string $type = 'skin'): array
    {
        $fullPath = storage_path('app/public/' . $imagePath);

        if (!file_exists($fullPath)) {
            return [
                'success' => false,
                'error' => 'Image file not found.',
            ];
        }

        try {
            $response = Http::timeout(60)
                ->attach('file', fopen($fullPath, 'r'), basename($fullPath))
                ->post($this->endpoint . '/analyze', ['type' => $type]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'error' => 'AI service error: ' . $response->body(),
                ];
            }

            $data = $response->json();

            // Map concern level from float score
            $score = $data['confidence'] ?? 0;
            $concernLevel = $this->mapConcernLevel($score, $data['concern_level'] ?? null);

            return [
                'success' => true,
                'prediction' => $data['prediction'] ?? 'Unknown',
                'confidence' => round($score, 2),
                'concern_level' => $concernLevel,
                'all_predictions' => $data['all_predictions'] ?? [],
                'type' => $data['type'] ?? $type,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Map AI confidence to concern level
     */
    protected function mapConcernLevel(float $confidence, ?string $aiConcern = null): string
    {
        if ($aiConcern) {
            return $aiConcern;
        }

        if ($confidence >= 90) {
            return 'critical';
        }
        if ($confidence >= 75) {
            return 'high';
        }
        if ($confidence >= 50) {
            return 'medium';
        }
        return 'low';
    }

    /**
     * Check if AI service is healthy
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->endpoint . '/health');
            return $response->successful() && ($response->json()['status'] ?? '') === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }
}
