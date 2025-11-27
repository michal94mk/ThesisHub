<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get notifications for authenticated user.
     */
    public function index(Request $request)
    {
        $notifications = Notification::forUser(auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    /**
     * Get unread notification count.
     */
    public function unreadCount()
    {
        $count = Notification::forUser(auth()->id())
            ->unread()
            ->count();

        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        // Check if notification belongs to user
        if ($notification->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
        ]);
    }
}
