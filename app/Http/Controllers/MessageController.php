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

        $senderUser = Auth::user();

        $messageContent = $request->textContent;

        // Determine message content based on sender's role
        // Assuming role_id 2 is for Agent. Adjust as per your database.
        if ($senderUser && $senderUser->role_id === 2) {
            $messageContent = "Hi there! I'm a real estate agent and I'd like to connect with you about properties. How can I assist you?";
        } else {
            $messageContent = $request->textContent; // Use the text provided in the request for non-agents
        }

        $message = Message::create([
            'user_sender_id' => Auth::id(),
            'user_receiver_id' => $request->receiver_id,
            'textContent' => $messageContent,
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

        // Mark unread messages from this user as read
        Message::where('user_sender_id', $userId)
            ->where('user_receiver_id', $authId)
            ->where('status', 'unread')
            ->update(['status' => 'read']);

        // Fetch all messages between the two users
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

        // Get IDs of all contacts the current user has messaged with
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

        // If no contacts, return an empty array
        if ($contactIds->isEmpty()) {
            return response()->json([]);
        }

        // Fetch user details for these contacts
        $usersQuery = User::whereIn('id', $contactIds);

        // Apply search filter if provided
        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $usersQuery->with('profile')->get();

        // Format user data for the frontend
        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'profile_image_path' => $user->profile ? $user->profile->imag_path : null,
            ];
        });

        return response()->json($formattedUsers);
    }

    public function startNewChat(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $currentUser = Auth::user();
        $receiverUser = User::find($request->receiver_id);

        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!$receiverUser) {
            return response()->json(['message' => 'Receiver user not found.'], 404);
        }

        // Check if chat already exists
        $existingChat = Message::where(function ($query) use ($currentUser, $receiverUser) {
            $query->where('user_sender_id', $currentUser->id)
                ->where('user_receiver_id', $receiverUser->id);
        })->orWhere(function ($query) use ($currentUser, $receiverUser) {
            $query->where('user_sender_id', $receiverUser->id)
                ->where('user_receiver_id', $currentUser->id);
        })->exists();

        if ($existingChat) {
            return response()->json([
                'message' => 'Chat already exists.',
                'receiver_id' => $receiverUser->id
            ], 200);
        }

        // Determine welcome message content based on sender's role
        $welcomeMessageContent = "";

        // Assuming role_id 2 is for Agent. Adjust as per your database.
        if ($currentUser->role_id === 2) {
            $welcomeMessageContent = "Hi there! I'm a real estate agent and I'd like to connect with you about properties. How can I help?";
        } else {
            $welcomeMessageContent = "Hello! I would like to inquire about your property.";
        }

        // Create the first message for the new chat
        $message = Message::create([
            'user_sender_id' => $currentUser->id,
            'user_receiver_id' => $receiverUser->id,
            'textContent' => $welcomeMessageContent,
            'status' => 'unread',
        ]);

        // Send a notification to the receiver
        Notification::sendToUser(
            $receiverUser->id,
            'new_message',
            "You have a new message from " . $currentUser->first_name . "."
        );

        // Return success response
        return response()->json([
            'message' => 'Chat started successfully and welcome message sent.',
            'receiver_id' => $receiverUser->id,
            'first_message' => $message
        ]);
}
}
