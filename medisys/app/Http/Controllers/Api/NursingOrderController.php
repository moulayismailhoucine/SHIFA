<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NursingOrder;
use App\Models\NurseAuditLog;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;

class NursingOrderController extends Controller
{
    // ── Index: Nurses see only their orders; Doctors see only their orders ──
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = NursingOrder::with(['patient:id,name,blood_type,age,gender', 'doctorUser:id,name', 'nurse:id,name']);

        if ($user->role === 'nurse') {
            // Strict: nurse sees only tasks explicitly assigned to them
            $query->where('nurse_id', $user->id);
        } elseif ($user->role === 'doctor') {
            // Doctor sees only orders they created
            $query->where('doctor_id', $user->id);
        } elseif ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Refresh overdue flags
        $query->each(function ($o) {
            if ($o->isOverdueNow() && !$o->is_overdue) {
                $o->update(['is_overdue' => true]);
            }
        });

        $orders = $query->latest()->get();

        // For nurses: strip restricted patient fields — return only what they need
        if ($user->role === 'nurse') {
            $orders->transform(function ($o) {
                return [
                    'id'           => $o->id,
                    'type'         => $o->type,
                    'dosage_method'=> $o->dosage_method,
                    'instructions' => $o->instructions,
                    'schedule'     => $o->schedule,
                    'scheduled_time'=> $o->scheduled_time,
                    'start_date'   => $o->start_date?->format('Y-m-d'),
                    'end_date'     => $o->end_date?->format('Y-m-d'),
                    'interval_hours'=> $o->interval_hours,
                    'status'       => $o->status,
                    'result'       => $o->result,
                    'is_overdue'   => $o->is_overdue,
                    'completed_at' => $o->completed_at,
                    'notes'        => $o->notes,
                    // LIMITED patient view — name + ID only
                    'patient'      => [
                        'id'   => $o->patient?->id,
                        'name' => $o->patient?->name,
                    ],
                    'doctor'       => ['name' => $o->doctorUser?->name],
                    'created_at'   => $o->created_at,
                ];
            });
        }

        return response()->json(['success' => true, 'data' => $orders]);
    }

    // ── Store: Only doctors can create tasks and assign to nurses ──
    public function store(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role, ['doctor', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Only doctors can create nursing tasks.'], 403);
        }

        $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'nurse_id'       => 'required|exists:users,id',
            'type'           => 'required|in:Medication,Monitoring,Care Procedure',
            'dosage_method'  => 'nullable|string|max:100',
            'schedule'       => 'nullable|string|max:200',
            'instructions'   => 'nullable|string|max:1000',
            'scheduled_time' => 'nullable|string',       // "08:00,14:00"
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'interval_hours' => 'nullable|integer|min:1|max:24',
            'notes'          => 'nullable|string',
        ]);

        // Verify nurse belongs to this doctor
        $nurseUser = \App\Models\User::find($request->nurse_id);
        if ($nurseUser && $nurseUser->nurse) {
            if ($user->role === 'doctor' && $nurseUser->nurse->doctor_id != $user->id) {
                return response()->json(['success' => false, 'message' => 'This nurse is not assigned to you.'], 403);
            }
        }

        $order = NursingOrder::create([
            ...$request->only([
                'patient_id','nurse_id','type','dosage_method','schedule',
                'instructions','scheduled_time','start_date','end_date',
                'interval_hours','notes'
            ]),
            'doctor_id' => $user->role === 'doctor' ? $user->id : $request->doctor_id,
            'status'    => 'Pending',
        ]);

        NurseAuditLog::create([
            'nurse_id'         => $request->nurse_id,
            'patient_id'       => $request->patient_id,
            'nursing_order_id' => $order->id,
            'action'           => 'task_assigned',
            'details'          => json_encode(['type' => $order->type, 'by_doctor' => $user->name]),
            'ip_address'       => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nursing task created and assigned successfully.',
            'data'    => $order->load(['patient:id,name', 'nurse:id,name']),
        ]);
    }

    // ── Show single order ──
    public function show(Request $request, NursingOrder $nursingOrder)
    {
        $user = $request->user();
        if ($user->role === 'nurse' && $nursingOrder->nurse_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }
        if ($user->role === 'doctor' && $nursingOrder->doctor_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        return response()->json([
            'success' => true,
            'data'    => $nursingOrder->load(['patient:id,name,blood_type,age', 'doctorUser:id,name', 'nurse:id,name', 'auditLogs.nurse:id,name'])
        ]);
    }

    // ── Update: Nurses can only update status + result. Doctors can update everything ──
    public function update(Request $request, NursingOrder $nursingOrder)
    {
        $user = $request->user();

        if ($user->role === 'nurse') {
            // STRICT: Nurse can only execute (update status + record result)
            if ($nursingOrder->nurse_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
            }

            $request->validate([
                'status' => 'required|in:Pending,Ongoing,Completed',
                'result' => 'nullable|string|max:1000',
            ]);

            $old = $nursingOrder->status;
            $nursingOrder->update([
                'status'          => $request->status,
                'result'          => $request->result ?? $nursingOrder->result,
                'completed_at'    => $request->status === 'Completed' ? now() : $nursingOrder->completed_at,
                'last_executed_at'=> now(),
            ]);

            // Abnormal result alert check
            if ($nursingOrder->type === 'Monitoring' && $request->result) {
                $this->checkAbnormalResult($nursingOrder, $request->result);
            }

            // Audit log
            NurseAuditLog::log("task_{$request->status}", [
                'patient_id' => $nursingOrder->patient_id,
                'order_id'   => $nursingOrder->id,
                'details'    => ['from' => $old, 'to' => $request->status, 'result' => $request->result],
            ]);

            return response()->json([
                'success' => true,
                'message' => "Task marked as {$request->status}.",
                'data'    => $nursingOrder,
            ]);
        }

        // Doctor can update full order
        if (in_array($user->role, ['doctor', 'admin'])) {
            $nursingOrder->update($request->only([
                'type','dosage_method','schedule','instructions',
                'scheduled_time','start_date','end_date','interval_hours','notes','status'
            ]));
            return response()->json(['success' => true, 'message' => 'Order updated.', 'data' => $nursingOrder]);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    public function destroy(Request $request, NursingOrder $nursingOrder)
    {
        if (!in_array($request->user()->role, ['doctor', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Only doctors can delete tasks.'], 403);
        }
        $nursingOrder->delete();
        return response()->json(['success' => true, 'message' => 'Task deleted.']);
    }

    // ── Get nurse's assigned patients (strictly filtered) ──
    public function myPatients(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'nurse') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $patientIds = NursingOrder::where('nurse_id', $user->id)
            ->active()
            ->pluck('patient_id')
            ->unique();

        // Return only minimal patient info for nurses
        $patients = Patient::whereIn('id', $patientIds)
            ->get(['id', 'name', 'age', 'gender', 'blood_type', 'allergies', 'phone']);

        return response()->json(['success' => true, 'data' => $patients]);
    }

    // ── Doctor: Get nurses under this doctor ──
    public function myNurses(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'doctor') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $nurses = \App\Models\Nurse::where('doctor_id', $user->id)
            ->with('user:id,name,email')
            ->get();

        return response()->json(['success' => true, 'data' => $nurses]);
    }

    // ── Audit log for a nurse (doctor can view) ──
    public function auditLog(Request $request)
    {
        $user = $request->user();
        if (!in_array($user->role, ['doctor', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $logs = NurseAuditLog::with(['nurse:id,name', 'patient:id,name'])
            ->when($request->nurse_id, fn($q) => $q->where('nurse_id', $request->nurse_id))
            ->latest()
            ->limit(100)
            ->get();

        return response()->json(['success' => true, 'data' => $logs]);
    }

    // ── Check abnormal monitoring results ──
    private function checkAbnormalResult(NursingOrder $order, string $result): void
    {
        $lower = strtolower($result);
        $abnormal = false;
        $msg = '';

        if (preg_match('/bp[:\s]+(\d+)\/(\d+)/i', $result, $m)) {
            if ((int)$m[1] > 140 || (int)$m[1] < 90 || (int)$m[2] > 90 || (int)$m[2] < 60) {
                $abnormal = true; $msg = "Abnormal BP: {$m[1]}/{$m[2]}";
            }
        }
        if (preg_match('/glucose[:\s]+(\d+)/i', $result, $m)) {
            if ((int)$m[1] > 180 || (int)$m[1] < 70) {
                $abnormal = true; $msg .= " Abnormal glucose: {$m[1]}";
            }
        }
        if (preg_match('/spo2[:\s]+(\d+)/i', $result, $m) && (int)$m[1] < 95) {
            $abnormal = true; $msg .= " Low SpO2: {$m[1]}%";
        }

        if ($abnormal && $order->doctor_id) {
            \App\Models\Alert::create([
                'user_id'    => $order->doctor_id,
                'patient_id' => $order->patient_id,
                'title'      => '⚠ Abnormal Result — ' . ($order->patient?->name ?? 'Patient'),
                'message'    => trim($msg) . ' (Recorded by nurse via order #' . $order->id . ')',
                'type'       => 'critical',
                'is_read'    => false,
            ]);
        }
    }
}
