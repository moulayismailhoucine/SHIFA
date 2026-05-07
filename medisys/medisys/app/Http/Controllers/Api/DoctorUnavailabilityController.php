<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorUnavailability;
use Illuminate\Http\Request;

class DoctorUnavailabilityController extends Controller
{
    public function index(Request $request)
    {
        $query = DoctorUnavailability::query();

        if ($request->user()->isDoctor()) {
            $query->where('doctor_id', $request->user()->doctor->id);
        } elseif ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        return response()->json(['success' => true, 'data' => $query->latest()->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:255',
        ]);

        $doctorId = $request->user()->doctor->id;

        $unavailability = DoctorUnavailability::create(array_merge($validated, [
            'doctor_id' => $doctorId,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Unavailability added.',
            'data'    => $unavailability,
        ], 201);
    }

    public function destroy(DoctorUnavailability $doctorUnavailability)
    {
        $doctorUnavailability->delete();
        return response()->json(['success' => true, 'message' => 'Unavailability removed.']);
    }
}
