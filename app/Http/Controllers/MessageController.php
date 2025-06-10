<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class MessageController extends Controller
{

    public function send(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'textContent' => 'required|string'
        ]);

        $message = Message::create([
            'user_sender_id' => Auth::id(),
            'user_receiver_id' => $request->receiver_id,
            'textContent' => $request->textContent,
            'status' => 'unread',
        ]);
        Notification::sendToUser(
            $request->receiver_id,
            'new_message',
            "You have a new message from " . Auth::user()->first_name . "."
        );

        return response()->json($message, 201);
    }


    public function conversation($userId): \Illuminate\Http\JsonResponse
    {
        $authId = Auth::id();


        Message::where('user_sender_id', $userId)
            ->where('user_receiver_id', $authId)
            ->where('status', 'unread')
            ->update(['status' => 'read']);

        $messages = Message::where(function ($query) use ($authId, $userId) {
            $query->where('user_sender_id', $authId)->where('user_receiver_id', $userId);
        })->orWhere(function ($query) use ($authId, $userId) {
            $query->where('user_sender_id', $userId)->where('user_receiver_id', $authId);
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }

    public function chatList(Request $request): \Illuminate\Http\JsonResponse
    {
        $authId = Auth::id();
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

        // هنا تحتاج لتحميل علاقة الـprofile
        $users = $usersQuery->with('profile')->get();

        // قم بتحويل الـcollection لإضافة حقل profile_image_path
        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name, // أو حقول الاسم الأخرى
                'email' => $user->email,
                // أضف السطر المطلوب هنا
                'profile_image_path' => $user->profile ? $user->profile->imag_path : null,
                // يمكنك إضافة حقول أخرى تحتاجها هنا
            ];
        });

        return response()->json($formattedUsers);
    }

    public function startNewChat(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            // Consistent with 'send' method, expecting 'receiver_id'
            'receiver_id' => 'required|exists:users,id',
        ]);

        $currentUser = Auth::user(); // The authenticated user (the buyer)
        $receiverUser = User::find($request->receiver_id); // The user being contacted (the property owner)

        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!$receiverUser) { // Changed from $targetUser to $receiverUser
            return response()->json(['message' => 'Receiver user not found.'], 404);
        }

        // Prevent creating a new chat if one already exists between these two users
        $existingChat = Message::where(function($query) use ($currentUser, $receiverUser) { // Changed $targetUser to $receiverUser
            $query->where('user_sender_id', $currentUser->id)
                ->where('user_receiver_id', $receiverUser->id);
        })->orWhere(function($query) use ($currentUser, $receiverUser) { // Changed $targetUser to $receiverUser
            $query->where('user_sender_id', $receiverUser->id) // Changed $targetUser to $receiverUser
            ->where('user_receiver_id', $currentUser->id);
        })->exists();

        if ($existingChat) {
            return response()->json([
                'message' => 'Chat already exists.',
                'receiver_id' => $receiverUser->id // Consistent return
            ], 200);
        }

        $welcomeMessageContent = "Hello! I would like to inquire about your property.";

        $message = Message::create([
            'user_sender_id' => $currentUser->id,
            'user_receiver_id' => $receiverUser->id, // Consistent with $receiverUser
            'textContent' => $welcomeMessageContent,
            'status' => 'unread',
        ]);

        // Send notification to the receiver user
        Notification::sendToUser(
            $receiverUser->id, // Consistent with $receiverUser
            'new_message',
            "You have a new message from " . $currentUser->first_name . "."
        );

        return response()->json([
            'message' => 'Chat started successfully and welcome message sent.',
            'receiver_id' => $receiverUser->id, // Consistent with $receiverUser
            'first_message' => $message
        ], 201);
    }
}
