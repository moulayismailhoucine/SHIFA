<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'diagnosis',
        'notes',
        'visit_type',
        'temperature',
        'blood_pressure',
        'heart_rate',
        'weight',
        'height',
        'visit_date',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'temperature' => 'decimal:1',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class);
    }
}
