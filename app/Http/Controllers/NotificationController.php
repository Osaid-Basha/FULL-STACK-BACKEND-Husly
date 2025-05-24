<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
   
    public function sendToUser(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'message_content' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $notification = Notification::create([
            'type' => $request->type,
            'message_content' => $request->message_content,
        ]);

        $notification->users()->attach($request->user_id, [
            'is_read' => false,
            'read_at' => null,
            'status' => 'sent',
        ]);

        return response()->json(['message' => 'Notification sent', 'notification' => $notification], 201);
    }

    public function sendToAllBuyers(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'message_content' => 'required|string|max:255',
        ]);

        $notification = Notification::create([
            'type' => $request->type,
            'message_content' => $request->message_content,
        ]);

        $buyers = User::where('role_id', 3)->get();

        foreach ($buyers as $buyer) {
            $notification->users()->attach($buyer->id, [
                'is_read' => false,
                'read_at' => null,
                'status' => 'broadcast',
            ]);
        }

        return response()->json(['message' => 'Notification sent to all buyers'], 201);
    }

    public function myNotifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->withPivot('is_read', 'read_at', 'status')->get();

        return response()->json(['notifications' => $notifications]);
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();

        $user->notifications()->updateExistingPivot($notificationId, [
            'is_read' => true,
            'read_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    // حذف إشعار من المستخدم فقط (detach)
    public function deleteNotification($notificationId)
    {
        $user = Auth::user();
        $user->notifications()->detach($notificationId);

        return response()->json(['message' => 'Notification removed from user']);
    }
}
