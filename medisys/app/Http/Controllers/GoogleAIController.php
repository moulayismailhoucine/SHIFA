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
            'message' => 'required|string|max:2000'
        ]);

        $apiKey  = env('GEMINI_API_KEY') ?: env('GOOGLE_API_KEY');
        $isValid = $apiKey
                && str_starts_with($apiKey, 'AIza')
                && $apiKey !== 'your_gemini_api_key_here'
                && strlen($apiKey) > 20;

        // ── Demo / fallback mode ────────────────────────────────
        if (!$isValid) {
            return response()->json([
                'reply' => $this->demoReply($request->message)
            ]);
        }

        // Try models in order of preference
        $models = [
            'gemini-1.5-flash',
            'gemini-1.5-pro',
            'gemini-pro',
            'gemini-2.0-flash',
        ];

        $systemPrompt = "You are a helpful medical assistant for SHIFA Hospital Management System. "
            . "Provide clear, accurate general medical information based on trusted sources. "
            . "Never diagnose — always advise consulting a qualified healthcare professional. "
            . "Keep responses concise, friendly, and structured (use bullet points when helpful).";

        $payload = [
            'contents' => [[
                'parts' => [[
                    'text' => $systemPrompt . "\n\nUser question: " . $request->message
                ]]
            ]]
        ];

        foreach ($models as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                $response = Http::timeout(25)->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($reply) {
                        return response()->json(['reply' => $reply]);
                    }
                }

                // 429 = quota exceeded — no point trying other models
                if ($response->status() === 429) {
                    return response()->json([
                        'reply' => "⚠️ I've reached the API rate limit. Please wait a moment and try again."
                    ]);
                }

            } catch (\Exception $e) {
                // Try next model
                continue;
            }
        }

        // All models failed — return demo reply
        return response()->json([
            'reply' => $this->demoReply($request->message)
        ]);
    }

    private function demoReply(string $question): string
    {
        $q = strtolower($question);

        if (str_contains($q, 'headache') || str_contains($q, 'head')) {
            return "**Common causes of headaches include:**\n\n- Tension (stress, poor posture)\n- Dehydration — drink more water\n- Eye strain from screens\n- Lack of sleep\n- Sinus congestion\n\nFor frequent or severe headaches, please consult a doctor.";
        }
        if (str_contains($q, 'fever') || str_contains($q, 'temperature')) {
            return "**Managing a fever:**\n\n- Stay hydrated — drink plenty of fluids\n- Rest as much as possible\n- Take paracetamol (acetaminophen) for relief\n- Use a cool compress\n\n⚠️ Seek medical care if fever exceeds 39.5°C or lasts more than 3 days.";
        }
        if (str_contains($q, 'blood pressure') || str_contains($q, 'hypertension')) {
            return "**High blood pressure warning signs:**\n\n- Severe headaches\n- Shortness of breath\n- Nosebleeds\n- Visual changes\n\nLifestyle tips: reduce salt, exercise regularly, manage stress. Regular monitoring is essential. Consult your doctor for proper management.";
        }
        if (str_contains($q, 'sleep') || str_contains($q, 'insomnia')) {
            return "**Tips for better sleep:**\n\n- Maintain a consistent sleep schedule\n- Avoid screens 1 hour before bed\n- Keep your room cool and dark\n- Limit caffeine after 2PM\n- Try relaxation techniques (deep breathing, meditation)\n\nIf problems persist, consult a healthcare professional.";
        }
        if (str_contains($q, 'diet') || str_contains($q, 'nutrition') || str_contains($q, 'eat')) {
            return "**Healthy diet basics:**\n\n- Eat plenty of fruits and vegetables\n- Choose whole grains over refined carbs\n- Limit processed foods and added sugar\n- Stay hydrated (8 glasses of water/day)\n- Include lean proteins\n\nFor a personalized diet plan, consult a nutritionist.";
        }

        return "Thank you for your question! I'm currently running without an active AI connection, so I can only provide general information.\n\n**General health advice:**\n- Stay hydrated and get adequate sleep\n- Exercise regularly\n- Maintain a balanced diet\n- Schedule regular check-ups with your doctor\n\nFor specific medical concerns, please consult a qualified healthcare professional at SHIFA Hospital. I cannot provide diagnosis or specific medical advice.";
    }
}
