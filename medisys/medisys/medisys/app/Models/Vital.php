<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vital extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'nurse_id',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'glucose_level',
        'heart_rate',
        'temperature',
        'oxygen_saturation',
        'weight',
        'notes',
    ];

    protected $casts = [
        'glucose_level' => 'decimal:2',
        'temperature' => 'decimal:1',
        'weight' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function getBloodPressureAttribute()
    {
        return "{$this->blood_pressure_systolic}/{$this->blood_pressure_diastolic}";
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
