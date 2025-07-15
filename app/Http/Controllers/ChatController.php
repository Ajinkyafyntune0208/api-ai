<?php

// app/Http/Controllers/ChatController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http; // For HTTP requests

class ChatController extends Controller
{
    public function sendMessage(Request $request)
{
    $message = $request->input('message');
    $apiKey = env('GEMINI_API_KEY'); // Store your Gemini API key in .env file

    // The data to send to Gemini
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $message]
                ]
            ]
        ]
    ];

    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", $data);

        if ($response->successful()) {
            $responseBody = $response->json();

            // Extract generated text from Gemini's response
            $generatedText = $responseBody['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated.';

            return response()->json(['reply' => $generatedText]);
        } else {
            return response()->json(['error' => 'Error from Gemini API: ' . $response->body()]);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Request failed: ' . $e->getMessage()]);
    }
}

}
