<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function count(Request $request)
    {
        $count = $request->user()->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    public function list(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get(['id', 'data', 'read_at', 'created_at']);

        return response()->json($notifications);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'id' => 'required|uuid',
        ]);

        $notification = auth()->user()->notifications()->where('id', $request->id)->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }
}
