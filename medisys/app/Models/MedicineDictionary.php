<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineDictionary extends Model
{
    protected $fillable = [
        'name',
        'explanation',
        'explanation_ar',
    ];
}
