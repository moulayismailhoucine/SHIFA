<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LabResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'laboratory_id',
        'file_path',
        'file_type',
        'title',
        'note',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
