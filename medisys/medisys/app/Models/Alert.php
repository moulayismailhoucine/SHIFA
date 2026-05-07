<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'type',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function getSeverityAttribute(): string
    {
        return match ($this->type) {
            'high_glucose' => 'warning',
            'high_bp' => 'critical',
            'high_heart_rate' => 'warning',
            default => 'warning',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'high_glucose' => 'High Glucose',
            'high_bp' => 'High Blood Pressure',
            'high_heart_rate' => 'High Heart Rate',
            default => 'Alert',
        };
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'high_glucose' => 'badge-warning',
            'high_bp' => 'badge-critical',
            'high_heart_rate' => 'badge-warning',
            default => 'badge-warning',
        };
    }
}
