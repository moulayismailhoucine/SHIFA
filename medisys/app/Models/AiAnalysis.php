<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'image_path',
        'image_type',
        'ai_prediction',
        'confidence_score',
        'concern_level',
        'details',
        'doctor_notes',
        'status',
    ];

    protected $casts = [
        'confidence_score' => 'float',
        'details' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
