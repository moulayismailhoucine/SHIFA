<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleAIController extends Controller
{
    public function index()
    {
        return view('medical-chat');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $apiKey = env('GEMINI_API_KEY', env('GOOGLE_API_KEY'));

        // Debug: Check if API key is available
        if (!$apiKey) {
            return response()->json([
                'error' => 'API key not configured. Please add GOOGLE_API_KEY or GEMINI_API_KEY to your .env file.',
                'reply' => 'Chat bot is not configured. Please contact administrator.'
            ], 500);
        }

        $systemPrompt = "You are a medical assistant. Provide general medical advice only based on trusted sources. Do not provide diagnosis. Always recommend consulting a doctor.";

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;
            
            $payload = [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $systemPrompt . "\n\nUser Question:\n" . $request->message]
                        ]
                    ]
                ]
            ];

            $response = Http::timeout(30)->post($url, $payload);

            if ($response->failed()) {
                $errorDetails = [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ];
                
                return response()->json([
                    'error' => 'API request failed: ' . json_encode($errorDetails),
                    'reply' => 'Sorry, I am currently unavailable. Please try again later.'
                ], 500);
            }

            $data = $response->json();

            if (isset($data['error'])) {
                return response()->json([
                    'error' => 'API Error: ' . json_encode($data['error']),
                    'reply' => 'Sorry, I am currently unavailable. Please try again later.'
                ], 500);
            }

            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return response()->json([
                    'error' => 'Invalid API response format: ' . json_encode($data),
                    'reply' => 'Sorry, I received an invalid response. Please try again later.'
                ], 500);
            }

            $reply = $data['candidates'][0]['content']['parts'][0]['text'];

            return response()->json([
                'reply' => $reply
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
                'reply' => 'Sorry, I am currently unavailable. Please try again later.'
            ], 500);
        }
    }
}
