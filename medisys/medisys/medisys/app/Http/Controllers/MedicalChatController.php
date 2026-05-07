<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class MedicalChatController extends Controller
{
    public function index()
    {
        $chats = Chat::where('user_id', Auth::id())->latest()->get();
        return view('medical-chat', compact('chats'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->message;

        $systemPrompt = "You are a medical assistant. Provide general medical advice only based on trusted sources. Do not provide diagnosis. Always recommend consulting a doctor.";

        try {
            $apiKey = env('GEMINI_API_KEY', 'AIzaSyCTJdL_lhpwc3F0D2EBvbm0GDVdpBJnKxw');
            $response = Http::post(
                "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $apiKey,
                [
                    "contents" => [
                        [
                            "parts" => [
                                ["text" => $systemPrompt . "\n\nUser Question:\n" . $userMessage]
                            ]
                        ]
                    ]
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $botResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not process that request.';
            } else {
                $botResponse = 'Error: Unable to reach the medical assistant service at the moment.';
            }
        } catch (\Exception $e) {
            $botResponse = 'Error: ' . $e->getMessage();
        }

        Chat::create([
            'user_id' => Auth::id(),
            'message' => $userMessage,
            'response' => $botResponse,
        ]);

        return redirect()->route('medical-chat.index');
    }
}
