<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request, $parentId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $specialistId = $user->id;

        $messages = Message::conversation($specialistId, 'specialist', $parentId, 'parent')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // Store a new message sent by the specialist
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required',
            'content' => 'required|string',
            'child_id' => 'nullable'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'sender_type' => 'specialist',
            'recipient_id' => $request->recipient_id,
            'recipient_type' => 'parent',
            'parent_id' => $request->recipient_id,
            'child_id' => $request->child_id,
            'content' => $request->content,
        ]);

        return response()->json($message, 201);
    }
}
