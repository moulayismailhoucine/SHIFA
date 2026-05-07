<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medical_record_id',
        'recommended_doctors',
        'recommended_tests',
        'symptoms_triggers',
        'reasoning',
        'priority',
        'is_active',
        'expires_at',
        'source',
    ];

    protected $casts = [
        'recommended_doctors' => 'array',
        'recommended_tests' => 'array',
        'symptoms_triggers' => 'array',
        'priority' => 'string',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
