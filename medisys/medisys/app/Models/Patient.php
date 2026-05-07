<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'photo',
        'age',
        'gender',
        'phone',
        'email',
        'nfc_uid',
        'blood_type',
        'allergies',
        'address',
        'date_of_birth',
        'emergency_contact',
        'is_active',
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    protected $hidden = [
        'nfc_uid',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_of_birth' => 'date',
    ];

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

    public function latestRecord()
    {
        return $this->hasOne(MedicalRecord::class)->latestOfMany();
    }

    public function labResults()
    {
        return $this->hasMany(LabResult::class);
    }

    public function vitals()
    {
        return $this->hasMany(Vital::class);
    }

    public function nurseNotes()
    {
        return $this->hasMany(NurseNote::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class)->orderBy('created_at', 'desc');
    }

    public function latestVital()
    {
        return $this->hasOne(Vital::class)->latestOfMany();
    }
}
