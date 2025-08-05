<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function checkNewNotifications()
    {
        $user = auth()->user();
        if (!$user->shouldReceiveNotification()) {
            return response()->json(['count' => 0]);
        }
        $count = $user->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    public function getNotifications()
    {
        $user = auth()->user();
        if (!$user->shouldReceiveNotification()) {
            return response()->json([]);
        }
        $notifications = $user->unreadNotifications()->latest()->take(5)->get();
        return view('partials.notifications', compact('notifications'))->render();
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}