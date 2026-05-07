<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo',
        'specialty',
        'phone',
        'license_number',
        'bio',
        'is_active',
        'paid_amount',
        'working_days',
        'working_hours_start',
        'working_hours_end',
        'treatment_time',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'paid_amount' => 'float',
        'working_days' => 'array',
        'treatment_time' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Accessor to get name from related user
    public function getNameAttribute(): string
    {
        return $this->user?->name ?? '';
    }

    // Accessor to get email from related user
    public function getEmailAttribute(): string
    {
        return $this->user?->email ?? '';
    }
}
