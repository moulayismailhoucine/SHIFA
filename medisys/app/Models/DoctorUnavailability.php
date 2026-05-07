<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorUnavailability extends Model
{
    protected $fillable = [
        'doctor_id',
        'start_date',
        'end_date',
        'reason',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
