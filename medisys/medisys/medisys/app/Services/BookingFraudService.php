<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingFraudService
{
    /**
     * Detect fraudulent booking attempts
     *
     * @param string $ipAddress
     * @param string $email
     * @param string $phone
     * @param int $patientId
     * @return array
     */
    public function detectFraud(string $ipAddress, string $email, string $phone, int $patientId = null): array
    {
        $fraudIndicators = [];

        // Rule 1: Check booking frequency from same IP
        $recentBookingsCount = $this->getRecentBookingsCount($ipAddress);
        if ($recentBookingsCount > 5) {
            $fraudIndicators[] = [
                'type' => 'high_frequency',
                'score' => 40,
                'reason' => 'More than 5 bookings from same IP in last hour',
                'is_suspicious' => true
            ];
        }

        // Rule 2: Check for multiple accounts with same contact info
        $duplicateAccounts = $this->checkDuplicateAccounts($email, $phone, $patientId);
        if ($duplicateAccounts['count'] > 1) {
            $fraudIndicators[] = [
                'type' => 'duplicate_accounts',
                'score' => 35,
                'reason' => 'Multiple accounts found with same contact information',
                'is_suspicious' => true,
                'details' => $duplicateAccounts['details']
            ];
        }

        // Rule 3: Check for suspicious patterns
        $suspiciousPatterns = $this->checkSuspiciousPatterns($email, $phone);
        if (!empty($suspiciousPatterns)) {
            $fraudIndicators[] = [
                'type' => 'suspicious_pattern',
                'score' => 25,
                'reason' => 'Suspicious booking patterns detected',
                'is_suspicious' => true,
                'details' => $suspiciousPatterns
            ];
        }

        // Calculate overall fraud score
        $totalScore = 0;
        foreach ($fraudIndicators as $indicator) {
            $totalScore += $indicator['score'];
        }

        // Determine if booking is suspicious
        $isSuspicious = !empty($fraudIndicators) || $totalScore >= 30;

        return [
            'is_suspicious' => $isSuspicious,
            'fraud_score' => $totalScore,
            'indicators' => $fraudIndicators,
            'risk_level' => $this->getRiskLevel($totalScore),
            'recommendation' => $this->getRecommendation($totalScore)
        ];
    }

    /**
     * Get recent booking count from IP address
     *
     * @param string $ipAddress
     * @return int
     */
    private function getRecentBookingsCount(string $ipAddress): int
    {
        $cacheKey = "booking_count_{$ipAddress}";
        
        // Get current count from cache
        $count = Cache::get($cacheKey, 0);
        
        // Increment for this booking
        Cache::put($cacheKey, $count + 1, 3600); // 1 hour
        
        return $count + 1;
    }

    /**
     * Check for duplicate accounts with same contact info
     *
     * @param string $email
     * @param string $phone
     * @param int|null $excludePatientId
     * @return array
     */
    private function checkDuplicateAccounts(string $email, string $phone, ?int $excludePatientId = null): array
    {
        $duplicates = [];

        // Check email duplicates
        if (!empty($email)) {
            $emailBookings = DB::table('appointments')
                ->where('patient_email', $email)
                ->when($excludePatientId, function ($query, $excludePatientId) {
                    return $query->where('patient_id', '!=', $excludePatientId);
                })
                ->count();

            if ($emailBookings > 1) {
                $duplicates[] = [
                    'field' => 'email',
                    'value' => $email,
                    'count' => $emailBookings
                ];
            }
        }

        // Check phone duplicates
        if (!empty($phone)) {
            $phoneBookings = DB::table('appointments')
                ->where('patient_phone', $phone)
                ->when($excludePatientId, function ($query, $excludePatientId) {
                    return $query->where('patient_id', '!=', $excludePatientId);
                })
                ->count();

            if ($phoneBookings > 1) {
                $duplicates[] = [
                    'field' => 'phone',
                    'value' => $phone,
                    'count' => $phoneBookings
                ];
            }
        }

        return [
            'count' => count($duplicates),
            'details' => $duplicates
        ];
    }

    /**
     * Check for suspicious patterns in email/phone
     *
     * @param string $email
     * @param string $phone
     * @return array
     */
    private function checkSuspiciousPatterns(string $email, string $phone): array
    {
        $patterns = [];

        // Check for suspicious email patterns
        if (!empty($email)) {
            // Temporary email services
            $tempEmailServices = ['10minutemail', 'tempmail', 'guerrillamail', 'yopmail', 'mailinator'];
            foreach ($tempEmailServices as $service) {
                if (strpos(strtolower($email), $service) !== false) {
                    $patterns[] = "Temporary email service detected: {$service}";
                }
            }

            // Check for sequential patterns in email
            if (preg_match('/^[a-zA-Z]+\d+@/', $email)) {
                $patterns[] = 'Sequential email pattern detected';
            }

            // Check for suspicious phone patterns
            if (!empty($phone)) {
                // Same number repeated (1234567890)
                if (preg_match('/^(\d)\1{7,}$/', $phone)) {
                    $patterns[] = 'Repeated phone number pattern';
                }

                // Invalid format
                if (strlen($phone) < 10 || !preg_match('/^[\d\s\-\(\d+\)\s\d+$/', $phone)) {
                    $patterns[] = 'Invalid phone format';
                }
            }
        }

        return $patterns;
    }

    /**
     * Get risk level based on fraud score
     *
     * @param int $score
     * @return string
     */
    private function getRiskLevel(int $score): string
    {
        if ($score >= 50) {
            return 'critical';
        } elseif ($score >= 30) {
            return 'high';
        } elseif ($score >= 20) {
            return 'medium';
        } elseif ($score >= 10) {
            return 'low';
        }
        
        return 'minimal';
    }

    /**
     * Get recommendation based on fraud score
     *
     * @param int $score
     * @return string
     */
    private function getRecommendation(int $score): string
    {
        if ($score >= 50) {
            return 'Block booking immediately and require manual verification';
        } elseif ($score >= 30) {
            return 'Flag for manual review and consider temporary suspension';
        } elseif ($score >= 20) {
            return 'Monitor closely and consider additional verification';
        } elseif ($score >= 10) {
            return 'Standard verification process recommended';
        }
        
        return 'No action needed';
    }
}
