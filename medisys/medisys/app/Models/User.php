<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'code',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isPharmacy(): bool
    {
        return $this->role === 'pharmacy';
    }

    public function isLab(): bool
    {
        return $this->role === 'lab';
    }

    public function isNurse(): bool
    {
        return $this->role === 'nurse';
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function pharmacy()
    {
        return $this->hasOne(Pharmacy::class);
    }

    public function laboratory()
    {
        return $this->hasOne(Laboratory::class);
    }

    public function nurse()
    {
        return $this->hasOne(Nurse::class);
    }
}
