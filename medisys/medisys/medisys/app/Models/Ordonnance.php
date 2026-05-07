<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class Ordonnance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'medical_record_id',
        'patient_id',
        'doctor_id',
        'type',
        'medications',
        'instructions',
        'issued_date',
        'valid_until',
        'status',
        'pdf_path',
        'is_taken',
        'dispensed_by',
        'dispensed_at',
        'dispensed_note',
        'explanation',
        'explanation_generated',
    ];

    protected $casts = [
        'medications'  => 'array',
        'issued_date'  => 'date',
        'valid_until'  => 'date',
        'is_taken'     => 'boolean',
        'dispensed_at' => 'datetime',
        'explanation_generated' => 'boolean',
    ];

    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
