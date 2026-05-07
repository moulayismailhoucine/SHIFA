<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OpenAIController extends Controller
{
    public function testGoogleAI()
    {
        $apiKey = config('services.gemini.key', 'AIzaSyCTJdL_lhpwc3F0D2EBvbm0GDVdpBJnKxw');

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=".$apiKey,
            [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => "Hello, are you working?"]
                        ]
                    ]
                ]
            ]
        );

        return $response->json();
    }
}