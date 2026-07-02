<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    public function chat(Request $request)
    {
        // 1. Validate the user input
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            // 2. Forward the request to your FastAPI service
            // Make sure the URL matches where your FastAPI is running
            $response = Http::timeout(30)->post('http://127.0.0.1:8000/ask', [
                'message' => $request->input('message')
            ]);

            // 3. Check if FastAPI responded successfully
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'AI Service unavailable'], 503);
            }
        } catch (\Exception $e) {
            // 4. Log errors for debugging
            Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json(['error' => 'Could not connect to AI service'], 500);
        }
    }
}
