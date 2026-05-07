<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nurse extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'department', 'license_number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
