<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NurseAuditLog extends Model
{
    protected $fillable = [
        'nurse_id', 'patient_id', 'nursing_order_id',
        'action', 'details', 'ip_address',
    ];

    public function nurse()   { return $this->belongsTo(User::class, 'nurse_id'); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function order()   { return $this->belongsTo(NursingOrder::class, 'nursing_order_id'); }

    /** Convenience static logger */
    public static function log(string $action, array $context = []): void
    {
        static::create([
            'nurse_id'         => auth()->id(),
            'patient_id'       => $context['patient_id'] ?? null,
            'nursing_order_id' => $context['order_id'] ?? null,
            'action'           => $action,
            'details'          => isset($context['details']) ? json_encode($context['details']) : null,
            'ip_address'       => request()->ip(),
        ]);
    }
}
