<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    // إرسال رسالة
    public function send(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'textContent' => 'required|string'
        ]);

        $message = Message::create([
            'user_sender_id' => auth()->id(),
            'user_receiver_id' => $request->receiver_id,
            'textContent' => $request->textContent,
            'status' => 'unread',
        ]);

        return response()->json($message, 201);
    }

    // جلب المحادثة + تعليم الرسائل كمقروءة
    public function conversation($userId): \Illuminate\Http\JsonResponse
    {
        $authId = auth()->id();

        // تعليم الرسائل كمقروءة
        Message::where('user_sender_id', $userId)
            ->where('user_receiver_id', $authId)
            ->where('status', 'unread')
            ->update(['status' => 'read']);

        // جلب المحادثة بين المستخدمين
        $messages = Message::where(function ($query) use ($authId, $userId) {
            $query->where('user_sender_id', $authId)->where('user_receiver_id', $userId);
        })->orWhere(function ($query) use ($authId, $userId) {
            $query->where('user_sender_id', $userId)->where('user_receiver_id', $authId);
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }

    // جلب المستخدمين الذين تم التحدث معهم
    public function chatList(Request $request): \Illuminate\Http\JsonResponse
    {
        $authId = auth()->id();
        $search = $request->query('search');

        $contactIds = Message::where('user_sender_id', $authId)
            ->orWhere('user_receiver_id', $authId)
            ->get()
            ->map(function ($message) use ($authId) {
                return $message->user_sender_id == $authId
                    ? $message->user_receiver_id
                    : $message->user_sender_id;
            })
            ->unique()
            ->values();

        $usersQuery = User::whereIn('id', $contactIds);

        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $usersQuery->get();

        return response()->json($users);
    }
}
