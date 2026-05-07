<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    /**
     * Get recent unread alerts for dashboard.
     */
    public function recent()
    {
        $user = Auth::user();

        $query = Alert::with('patient')
            ->where('is_read', false)
            ->orderBy('created_at', 'desc');

        $alerts = $query->take(10)->get();

        return response()->json([
            'alerts' => $alerts->map(fn($alert) => [
                'id' => $alert->id,
                'patient_id' => $alert->patient_id,
                'patient_name' => $alert->patient?->name ?? 'Unknown',
                'type' => $alert->type,
                'type_label' => $alert->type_label,
                'message' => $alert->message,
                'severity' => $alert->severity,
                'badge_class' => $alert->badge_class,
                'created_at' => $alert->created_at->diffForHumans(),
                'created_at_formatted' => $alert->created_at->format('M d, Y H:i'),
            ]),
            'unread_count' => Alert::where('is_read', false)->count(),
        ]);
    }

    /**
     * Mark an alert as read.
     */
    public function markRead(Alert $alert)
    {
        $alert->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
