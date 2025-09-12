<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{



    public function sendToAllBuyers(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'message_content' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:100',
            'action_url' => 'nullable|url',
        ]);

        $notification = Notification::create([
            'title' => $request->title ?? Notification::defaultTitleFor($request->type),
            'type' => $request->type,
            'message_content' => Notification::refineMessage($request->type, $request->message_content ?? ''),
            'action_url' => $request->action_url,
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
        $user = User::find(Auth::id());
        $notifications = $user->notifications()->withPivot('is_read', 'read_at', 'status')->get();

        return response()->json(['notifications' => $notifications]);
    }

    public function markAsRead($notificationId)
    {
        $user = User::find(Auth::id());

        $user->notifications()->updateExistingPivot($notificationId, [
            'is_read' => true,
            'read_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Notification marked as read']);
    }


    public function deleteNotification($notificationId)
    {
        $user = User::find(Auth::id());
        $user->notifications()->detach($notificationId);

        return response()->json(['message' => 'Notification removed from user']);
    }


}
