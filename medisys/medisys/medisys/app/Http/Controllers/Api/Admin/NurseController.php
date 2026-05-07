<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class NurseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin']);
    }

    /**
     * Display a listing of nurses for admin dashboard.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'nurse')
            ->select(['id', 'name', 'email', 'username', 'phone', 'department', 'license_number', 'address', 'code', 'created_at']);

        // Apply search filter
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Apply department filter
        if ($request->department) {
            $query->where('department', $request->department);
        }

        // Apply pagination
        $perPage = $request->per_page ?? 10;
        $nurses = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $nurses->items(),
            'pagination' => [
                'current_page' => $nurses->currentPage(),
                'last_page' => $nurses->lastPage(),
                'per_page' => $nurses->perPage(),
                'total' => $nurses->total(),
                'from' => $nurses->firstItem(),
                'to' => $nurses->lastItem()
            ]
        ]);
    }

    /**
     * Get nurse statistics.
     */
    public function statistics()
    {
        $totalNurses = User::where('role', 'nurse')->count();
        $activeNurses = User::where('role', 'nurse')->count(); // All nurses are considered active
        $newThisMonth = User::where('role', 'nurse')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $departments = User::where('role', 'nurse')
            ->whereNotNull('department')
            ->distinct('department')
            ->count();

        return response()->json([
            'success' => true,
            'total' => $totalNurses,
            'active' => $activeNurses,
            'new_this_month' => $newThisMonth,
            'departments' => $departments
        ]);
    }

    /**
     * Get a specific nurse.
     */
    public function show($id)
    {
        $nurse = User::where('role', 'nurse')
            ->where('id', $id)
            ->first();

        if (!$nurse) {
            return response()->json([
                'success' => false,
                'message' => 'Nurse not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $nurse
        ]);
    }

    /**
     * Store a new nurse.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'department' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
        ]);

        $nurse = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => 'nurse',
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'code' => 'NURSE' . strtoupper(uniqid()),
        ]);

        // Store additional nurse metadata
        $nurse->update([
            'department' => $validated['department'] ?? null,
            'license_number' => $validated['license_number'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nurse created successfully',
            'data' => $nurse
        ]);
    }

    /**
     * Update a nurse.
     */
    public function update(Request $request, $id)
    {
        $nurse = User::where('role', 'nurse')->where('id', $id)->first();

        if (!$nurse) {
            return response()->json([
                'success' => false,
                'message' => 'Nurse not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($nurse->id),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($nurse->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'department' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'department' => $validated['department'] ?? null,
            'license_number' => $validated['license_number'] ?? null,
        ];

        if ($validated['password']) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $nurse->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Nurse updated successfully',
            'data' => $nurse
        ]);
    }

    /**
     * Delete a nurse.
     */
    public function destroy($id)
    {
        $nurse = User::where('role', 'nurse')->where('id', $id)->first();

        if (!$nurse) {
            return response()->json([
                'success' => false,
                'message' => 'Nurse not found'
            ], 404);
        }

        $nurse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nurse deleted successfully'
        ]);
    }
}
