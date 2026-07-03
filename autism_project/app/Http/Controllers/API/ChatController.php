<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Specialist fetches conversation with a specific parent.
     * GET /api/messages/parent/{parentId}
     * Called by the specialist's ChatPage.
     */
    public function index(Request $request, $parentId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $specialistUserId = $user->id;

        // The scope is bidirectional: returns messages where specialist ↔ parent
        $messages = Message::conversation($specialistUserId, 'specialist', $parentId, 'parent')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Parent fetches conversation with a specific specialist.
     * GET /api/messages/specialist/{specialistId}
     * Called by the parent's ParentChatPage.
     */
    public function indexForParent(Request $request, $specialistId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $parentUserId = $user->id;

        // Same bidirectional scope — just flip which ID is "self" vs "other"
        $messages = Message::conversation($specialistId, 'specialist', $parentUserId, 'parent')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Store a new message. Works for both specialists and parents.
     * The sender_type is determined by the authenticated user's role,
     * NOT hardcoded — so parent messages are stored as sender_type='parent'.
     * POST /api/messages
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id'   => 'required',
            'content'        => 'required|string',
            'recipient_type' => 'nullable|string',
            'child_id'       => 'nullable',
        ]);

        $user = Auth::user();

        // Determine sender role from the user's actual role field,
        // falling back to the recipient_type hint sent by the client.
        $senderType = match ($user->role ?? '') {
            'parent'     => 'parent',
            'specialist' => 'specialist',
            default      => $request->input('sender_type', 'specialist'),
        };

        // Determine recipient type — client may pass it explicitly
        $recipientType = $request->input('recipient_type', 'parent');

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id'       => $user->id,
            'sender_type'     => $senderType,
            'recipient_id'    => $request->recipient_id,
            'recipient_type'  => $recipientType,
            'parent_id'       => $senderType === 'parent' ? $user->id : $request->recipient_id,
            'child_id'        => $request->child_id,
            'content'         => $request->content,
        ]);

        return response()->json($message, 201);
    }
}
