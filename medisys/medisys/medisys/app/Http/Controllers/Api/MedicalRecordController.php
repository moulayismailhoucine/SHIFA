<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalRecord::with(['patient', 'doctor.user']);

        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->user()->isDoctor()) {
            // Doctor sees only their own records
            $query->where('doctor_id', $request->user()->doctor->id);
        }

        $records = $query->orderBy('visit_date', 'desc')->paginate(15);

        return response()->json(['success' => true, 'data' => $records]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'    => 'required|exists:patients,id',
            'diagnosis'     => 'required|string|max:500',
            'notes'         => 'nullable|string',
            'visit_type'    => 'nullable|in:consultation,follow_up,emergency,routine',
            'temperature'   => 'nullable|numeric|between:30,45',
            'blood_pressure'=> 'nullable|string|max:20',
            'heart_rate'    => 'nullable|integer|between:20,300',
            'weight'        => 'nullable|numeric|between:0,500',
            'height'        => 'nullable|numeric|between:0,300',
            'visit_date'    => 'required|date',
        ]);

        $doctorId = $request->user()->doctor->id;

        $record = MedicalRecord::create(array_merge($validated, ['doctor_id' => $doctorId]));

        return response()->json([
            'success' => true,
            'message' => 'Medical record created.',
            'data'    => $record->load(['patient', 'doctor.user']),
        ], 201);
    }

    public function show(MedicalRecord $medicalRecord)
    {
        return response()->json([
            'success' => true,
            'data'    => $medicalRecord->load(['patient', 'doctor.user', 'ordonnances']),
        ]);
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'diagnosis'     => 'sometimes|string|max:500',
            'notes'         => 'nullable|string',
            'visit_type'    => 'nullable|in:consultation,follow_up,emergency,routine',
            'temperature'   => 'nullable|numeric|between:30,45',
            'blood_pressure'=> 'nullable|string|max:20',
            'heart_rate'    => 'nullable|integer|between:20,300',
            'weight'        => 'nullable|numeric|between:0,500',
            'height'        => 'nullable|numeric|between:0,300',
            'visit_date'    => 'sometimes|date',
        ]);

        $medicalRecord->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Medical record updated.',
            'data'    => $medicalRecord->fresh(['patient', 'doctor.user']),
        ]);
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();
        return response()->json(['success' => true, 'message' => 'Medical record deleted.']);
    }
}
