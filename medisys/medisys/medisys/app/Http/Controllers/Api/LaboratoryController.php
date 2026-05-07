<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laboratory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LaboratoryController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => Laboratory::with('user')->get()]);
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
            'specialization' => 'nullable|string|max:255',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end'   => 'nullable|date_format:H:i',
        ]);

        // Create user for lab
        $code = 'LAB' . strtoupper(Str::random(6));
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'lab',
            'code'     => $code,
        ]);

        $labData = $validated;
        unset($labData['password']);
        $labData['user_id'] = $user->id;
        
        $lab = Laboratory::create($labData);

        return response()->json(['success' => true, 'data' => $lab, 'code' => $code], 201);
    }

    public function show(Laboratory $laboratory)
    {
        return response()->json(['success' => true, 'data' => $laboratory->load('user')]);
    }

    public function update(Request $request, Laboratory $laboratory)
    {
        $validated = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'address'        => 'sometimes|string',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email',
            'password'       => 'nullable|string|min:6',
            'license_number' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:255',
            'is_active'      => 'boolean',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end'   => 'nullable|date_format:H:i',
        ]);

        $laboratory->update($validated);

        if ($laboratory->user) {
            $userUpdate = [];
            if ($request->filled('name')) $userUpdate['name'] = $validated['name'];
            if ($request->filled('email')) $userUpdate['email'] = $validated['email'];
            if ($request->filled('password')) $userUpdate['password'] = Hash::make($validated['password']);
            
            if (!empty($userUpdate)) {
                $laboratory->user->update($userUpdate);
            }
        }

        return response()->json(['success' => true, 'data' => $laboratory->load('user')]);
    }

    public function destroy(Laboratory $laboratory)
    {
        $laboratory->delete();
        return response()->json(['success' => true, 'message' => 'Laboratory deleted.']);
    }
}
