<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PharmacyController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => Pharmacy::with('user')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'address'        => 'required|string',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:6',
            'license_number' => 'nullable|string|max:100',
            'manager_name'   => 'nullable|string|max:255',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end'   => 'nullable|date_format:H:i',
        ]);

        // Create user for pharmacy
        $code = 'PHARM' . strtoupper(Str::random(6));
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'pharmacy',
            'code'     => $code,
        ]);

        $pharmacyData = $validated;
        unset($pharmacyData['password']);
        $pharmacyData['user_id'] = $user->id;

        $pharmacy = Pharmacy::create($pharmacyData);

        return response()->json(['success' => true, 'data' => $pharmacy, 'code' => $code], 201);
    }

    public function show(Pharmacy $pharmacy)
    {
        return response()->json(['success' => true, 'data' => $pharmacy->load('user')]);
    }

    public function update(Request $request, Pharmacy $pharmacy)
    {
        $validated = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'address'        => 'sometimes|string',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email',
            'password'       => 'nullable|string|min:6',
            'license_number' => 'nullable|string|max:100',
            'manager_name'   => 'nullable|string|max:255',
            'is_active'      => 'boolean',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end'   => 'nullable|date_format:H:i',
        ]);

        $pharmacy->update($validated);

        if ($pharmacy->user) {
            $userUpdate = [];
            if ($request->filled('name')) $userUpdate['name'] = $validated['name'];
            if ($request->filled('email')) $userUpdate['email'] = $validated['email'];
            if ($request->filled('password')) $userUpdate['password'] = Hash::make($validated['password']);
            
            if (!empty($userUpdate)) {
                $pharmacy->user->update($userUpdate);
            }
        }

        return response()->json(['success' => true, 'data' => $pharmacy->load('user')]);
    }

    public function destroy(Pharmacy $pharmacy)
    {
        $pharmacy->delete();
        return response()->json(['success' => true, 'message' => 'Pharmacy deleted.']);
    }
}
