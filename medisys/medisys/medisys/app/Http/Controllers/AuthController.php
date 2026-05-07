<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Patient;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->login;

        // Check if login is email or code
        $user = User::where('email', $login)->orWhere('code', $login)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        if (!Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function nfcLogin(Request $request)
    {
        $request->validate([
            'nfc_uid' => 'required|string',
        ]);

        $patient = Patient::where('nfc_uid', $request->nfc_uid)->first();

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        // For patients, perhaps no password, or use nfc as auth
        // Assuming patients login via NFC without password

        $token = $patient->createToken('auth_token')->plainTextToken;

        return response()->json([
            'patient' => $patient,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
