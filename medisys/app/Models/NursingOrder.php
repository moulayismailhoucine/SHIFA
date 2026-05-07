<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NursingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'doctor_id', 'nurse_id',
        'type', 'dosage_method', 'schedule', 'notes', 'instructions',
        'scheduled_time', 'start_date', 'end_date', 'interval_hours',
        'result', 'status', 'completed_at', 'last_executed_at', 'is_overdue',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'completed_at'     => 'datetime',
        'last_executed_at' => 'datetime',
        'is_overdue'       => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function doctorUser()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(NurseAuditLog::class);
    }

    // ── Scopes ────────────────────────────────────────────
    public function scopeForNurse($query, int $nurseUserId)
    {
        return $query->where('nurse_id', $nurseUserId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Pending', 'Ongoing']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('is_overdue', true)->where('status', '!=', 'Completed');
    }

    // ── Helpers ───────────────────────────────────────────
    public function isOverdueNow(): bool
    {
        if ($this->end_date && $this->end_date->isPast() && $this->status !== 'Completed') {
            return true;
        }
        return false;
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'Medication'     => '#2563eb',
            'Monitoring'     => '#7c3aed',
            'Care Procedure' => '#059669',
            default          => '#374151',
        };
    }
}
