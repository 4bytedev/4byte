<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get the count of unread notifications for the authenticated user.
     */
    public function count(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * List the latest notifications for the authenticated user.
     */
    public function list(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get(['id', 'data', 'read_at', 'created_at']);

        return response()->json($notifications);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request): void
    {
        $request->validate([
            'id' => 'required|uuid',
        ]);

        $notification = auth()->user()->notifications()->where('id', $request->id)->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead(); /* @phpstan-ignore-line */
    }
}
