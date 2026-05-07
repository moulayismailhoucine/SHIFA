<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Doctor / Admin login with email + password
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'login'    => 'required|string',
                'password' => 'required|string|min:6',
            ]);

            $login = $request->login ?? $request->email;

            // Check if login is email, username, or code
            $user = User::where('email', $login)
                        ->orWhere('username', $login)
                        ->orWhere('code', $login)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials.',
                ], 401);
            }

            // Establish web session only when running in web context (has session middleware)
            if ($request->hasSession()) {
                Auth::login($user);
                $request->session()->regenerate();
            }

            // Revoke old tokens
            $user->tokens()->delete();

            $token = $user->createToken('auth_token', [$user->role])->plainTextToken;

            $userData = [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
                'code'  => $user->code,
            ];

            if ($user->isDoctor() && $user->doctor) {
                $userData['doctor'] = [
                    'id'        => $user->doctor->id,
                    'specialty' => $user->doctor->specialty,
                    'phone'     => $user->doctor->phone,
                    'photo'     => $user->doctor->photo ? asset('storage/' . $user->doctor->photo) : null,
                ];
            }

            if ($user->isPharmacy() && $user->pharmacy) {
                $userData['pharmacy'] = [
                    'id'   => $user->pharmacy->id,
                    'name' => $user->pharmacy->name,
                ];
            }

            if ($user->isLab() && $user->laboratory) {
                $userData['laboratory'] = [
                    'id'   => $user->laboratory->id,
                    'name' => $user->laboratory->name,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'token'   => $token,
                'user'    => $userData,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile() . ':' . $e->getLine(),
            ], 500);
        }
    }

    /**
     * Patient NFC login — no password needed, just the UID
     */
    public function nfcLogin(Request $request)
    {
        $request->validate([
            'nfc_uid' => 'required|string',
        ]);

        $patient = Patient::where('nfc_uid', $request->nfc_uid)
                          ->where('is_active', true)
                          ->first();

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'NFC card not recognized. Patient not found.',
                'nfc_uid' => $request->nfc_uid,
            ], 404);
        }

        // Revoke old tokens
        $patient->tokens()->delete();

        $token = $patient->createToken('patient_nfc_token', ['patient'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'NFC login successful.',
            'token'   => $token,
            'patient' => [
                'id'             => $patient->id,
                'name'           => $patient->name,
                'age'            => $patient->age,
                'gender'         => $patient->gender,
                'blood_type'     => $patient->blood_type,
                'photo'          => $patient->photo,
                'allergies'      => $patient->allergies,
                'emergency_contact' => $patient->emergency_contact,
            ],
        ]);
    }

    /**
     * Look up a patient by NFC UID without creating tokens.
     * Safe to call from pharmacy/lab while their own session is active.
     */
    public function nfcLookup(Request $request)
    {
        $request->validate(['nfc_uid' => 'required|string']);

        $patient = Patient::where('nfc_uid', $request->nfc_uid)
                          ->where('is_active', true)
                          ->first();

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'NFC card not recognized. Patient not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'patient' => [
                'id'         => $patient->id,
                'name'       => $patient->name,
                'age'        => $patient->age,
                'gender'     => $patient->gender,
                'blood_type' => $patient->blood_type,
                'photo'      => $patient->photo,
                'allergies'  => $patient->allergies,
                'nfc_uid'    => $patient->nfc_uid,
            ],
        ]);
    }

    /**
     * Logout (works for both users and patients)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Get current authenticated user info
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user'    => $user,
        ]);
    }
}
