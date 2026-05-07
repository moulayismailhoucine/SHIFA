<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Patient;
use App\Models\Vital;

class AlertService
{
    /**
     * Check a vital record and create alerts for abnormal values.
     */
    public static function checkVitals(Vital $vital): array
    {
        $alerts = [];
        $patient = $vital->patient;
        $patientName = $patient ? $patient->name : 'Unknown Patient';

        // Rule 1: High glucose (> 180)
        if ($vital->glucose_level !== null && $vital->glucose_level > 180) {
            $alerts[] = Alert::create([
                'patient_id' => $vital->patient_id,
                'type' => 'high_glucose',
                'message' => "Glucose level is elevated at {$vital->glucose_level} mg/dL for patient {$patientName} (recorded at {$vital->created_at->format('M d, Y H:i')}).",
            ]);
        }

        // Rule 2: High BP systolic (> 140)
        if ($vital->blood_pressure_systolic > 140) {
            $alerts[] = Alert::create([
                'patient_id' => $vital->patient_id,
                'type' => 'high_bp',
                'message' => "Blood pressure is elevated at {$vital->blood_pressure_systolic}/{$vital->blood_pressure_diastolic} mmHg for patient {$patientName} (recorded at {$vital->created_at->format('M d, Y H:i')}).",
            ]);
        }

        // Rule 3: High heart rate (> 120)
        if ($vital->heart_rate > 120) {
            $alerts[] = Alert::create([
                'patient_id' => $vital->patient_id,
                'type' => 'high_heart_rate',
                'message' => "Heart rate is elevated at {$vital->heart_rate} bpm for patient {$patientName} (recorded at {$vital->created_at->format('M d, Y H:i')}).",
            ]);
        }

        return $alerts;
    }

    /**
     * Get severity badge for a vital value.
     */
    public static function getGlucoseSeverity(?float $value): string
    {
        if ($value === null) return 'normal';
        if ($value > 180) return 'critical';
        if ($value > 140) return 'warning';
        return 'normal';
    }

    public static function getBPSeverity(int $systolic, int $diastolic): string
    {
        if ($systolic > 140 || $diastolic > 90) return 'critical';
        if ($systolic > 120 || $diastolic > 80) return 'warning';
        return 'normal';
    }

    public static function getHeartRateSeverity(int $value): string
    {
        if ($value > 120 || $value < 50) return 'critical';
        if ($value > 100 || $value < 60) return 'warning';
        return 'normal';
    }

    public static function getTemperatureSeverity(float $value): string
    {
        if ($value > 38.5 || $value < 35.5) return 'critical';
        if ($value > 37.5 || $value < 36.0) return 'warning';
        return 'normal';
    }

    public static function getOxygenSeverity(int $value): string
    {
        if ($value < 90) return 'critical';
        if ($value < 95) return 'warning';
        return 'normal';
    }

    public static function getSeverityLabel(string $severity): string
    {
        return match ($severity) {
            'critical' => 'Critical',
            'warning' => 'Warning',
            default => 'Normal',
        };
    }

    public static function getSeverityBadgeClass(string $severity): string
    {
        return match ($severity) {
            'critical' => 'badge-critical',
            'warning' => 'badge-warning',
            default => 'badge-success',
        };
    }
}
