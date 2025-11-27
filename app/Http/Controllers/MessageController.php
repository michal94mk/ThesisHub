<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Notification;
use App\Models\Thesis;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get messages for a thesis.
     */
    public function index(Request $request, Thesis $thesis)
    {
        // Check if user can view this thesis
        $this->authorize('view', $thesis);

        $messages = $thesis->messages()
            ->with('user:id,name,email,role')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read (messages sent by other users)
        $thesis->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages,
            'unread_count' => 0, // All marked as read now
        ]);
    }

    /**
     * Store a new message.
     */
    public function store(Request $request, Thesis $thesis)
    {
        // Check if user can view this thesis (can chat)
        $this->authorize('view', $thesis);

        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'thesis_id' => $thesis->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        // Load user relationship
        $message->load('user:id,name,email,role');

        // Notify the other party (student or supervisor)
        $recipientId = $thesis->student_id === auth()->id() 
            ? $thesis->supervisor_id 
            : $thesis->student_id;

        Notification::create([
            'user_id' => $recipientId,
            'type' => 'new_message',
            'title' => 'New Message',
            'message' => auth()->user()->name . ' sent you a message about "' . $thesis->title . '"',
            'icon' => '💬',
            'color' => 'blue',
            'action_url' => route('theses.show', $thesis->id) . '#chat',
            'related_thesis_id' => $thesis->id,
            'related_message_id' => $message->id,
        ]);

        return response()->json([
            'message' => $message,
            'success' => true,
        ], 201);
    }

    /**
     * Get unread message count for a thesis.
     */
    public function unreadCount(Thesis $thesis)
    {
        $this->authorize('view', $thesis);

        $count = $thesis->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'count' => $count,
        ]);
    }
}
