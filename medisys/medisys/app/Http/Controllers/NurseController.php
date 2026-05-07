<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\VitalSign;
use App\Models\NurseNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NurseController extends Controller
{
    /**
     * Display the nurse dashboard.
     */
    public function dashboard()
    {
        $totalPatients = Patient::count();
        $myVitalsToday = VitalSign::where('recorded_by', Auth::id())
            ->whereDate('created_at', today())
            ->count();
        
        $myNotesToday = NurseNote::where('nurse_id', Auth::id())
            ->whereDate('created_at', today())
            ->count();

        $recentVitals = VitalSign::with('patient')
            ->where('recorded_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $recentNotes = NurseNote::with('patient')
            ->where('nurse_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentPatients = Patient::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('nurse.dashboard', compact(
            'totalPatients', 
            'myVitalsToday', 
            'myNotesToday',
            'recentVitals', 
            'recentNotes',
            'recentPatients'
        ));
    }

    /**
     * Display list of patients.
     */
    public function patients()
    {
        $patients = Patient::orderBy('created_at', 'desc')->paginate(10);
        
        return view('nurse.patients', compact('patients'));
    }

    /**
     * Store vitals for a patient.
     */
    public function storeVitals(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'blood_pressure_systolic' => 'required|integer|min:50|max:250',
            'blood_pressure_diastolic' => 'required|integer|min:30|max:150',
            'heart_rate' => 'required|integer|min:40|max:200',
            'temperature' => 'required|numeric|min:30|max:45',
            'oxygen_saturation' => 'required|integer|min:70|max:100',
            'respiratory_rate' => 'required|integer|min:8|max:40',
            'notes' => 'nullable|string|max:500'
        ]);

        $vital = VitalSign::create([
            'patient_id' => $patient->id,
            'recorded_by' => Auth::id(),
            'blood_pressure_systolic' => $validated['blood_pressure_systolic'],
            'blood_pressure_diastolic' => $validated['blood_pressure_diastolic'],
            'heart_rate' => $validated['heart_rate'],
            'temperature' => $validated['temperature'],
            'oxygen_saturation' => $validated['oxygen_saturation'],
            'respiratory_rate' => $validated['respiratory_rate'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Vitals recorded successfully for ' . $patient->name);
    }

    /**
     * Store nurse notes for a patient.
     */
    public function storeNote(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:1000',
            'type' => 'required|in:observation,care,medication,other'
        ]);

        $note = NurseNote::create([
            'patient_id' => $patient->id,
            'nurse_id' => Auth::id(),
            'note' => $validated['note'],
            'type' => $validated['type'],
        ]);

        return redirect()->back()
            ->with('success', 'Nurse note added successfully for ' . $patient->name);
    }

    /**
     * Show patient details for nurse.
     */
    public function showPatient(Patient $patient)
    {
        $patient->load(['vitalSigns' => function($query) {
            $query->orderBy('created_at', 'desc')->take(10);
        }, 'nurseNotes' => function($query) {
            $query->orderBy('created_at', 'desc')->take(10);
        }]);

        return view('nurse.patient-details', compact('patient'));
    }

    /**
     * Show vitals history for a patient.
     */
    public function patientVitals(Patient $patient)
    {
        $vitals = VitalSign::where('patient_id', $patient->id)
            ->with('recorder')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('nurse.patient-vitals', compact('patient', 'vitals'));
    }

    /**
     * Show nurse notes history for a patient.
     */
    public function patientNotes(Patient $patient)
    {
        $notes = NurseNote::where('patient_id', $patient->id)
            ->with('nurse')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('nurse.patient-notes', compact('patient', 'notes'));
    }

    /**
     * Delete a vital sign record.
     */
    public function deleteVital(VitalSign $vital)
    {
        // Check if the vital was recorded by the current nurse
        if ($vital->recorded_by !== Auth::id()) {
            abort(403, 'You can only delete vitals you recorded.');
        }

        $vital->delete();
        return redirect()->back()->with('success', 'Vital sign record deleted successfully.');
    }

    /**
     * Delete a nurse note.
     */
    public function deleteNote(NurseNote $note)
    {
        // Check if the note was written by the current nurse
        if ($note->nurse_id !== Auth::id()) {
            abort(403, 'You can only delete notes you wrote.');
        }

        $note->delete();
        return redirect()->back()->with('success', 'Nurse note deleted successfully.');
    }

    /**
     * Update nurse note.
     */
    public function updateNote(Request $request, NurseNote $note)
    {
        // Check if the note was written by the current nurse
        if ($note->nurse_id !== Auth::id()) {
            abort(403, 'You can only edit notes you wrote.');
        }

        $validated = $request->validate([
            'note' => 'required|string|max:1000',
            'type' => 'required|in:observation,care,medication,other'
        ]);

        $note->update($validated);

        return redirect()->back()->with('success', 'Nurse note updated successfully.');
    }

    /**
     * Get patient statistics for dashboard.
     */
    public function getPatientStats()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'patients_today' => Patient::whereDate('created_at', today())->count(),
            'my_vitals_today' => VitalSign::where('recorded_by', Auth::id())
                ->whereDate('created_at', today())->count(),
            'my_notes_today' => NurseNote::where('nurse_id', Auth::id())
                ->whereDate('created_at', today())->count(),
        ];

        return response()->json($stats);
    }
}
