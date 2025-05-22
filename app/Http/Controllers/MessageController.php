<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/messages",
     *     summary="Get all messages",
     *     tags={"Messages"},
     *     @OA\Response(response=200, description="List of all messages")
     * )
     */
    public function index()
    {
        return response()->json(Message::all(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/messages",
     *     summary="Send a new message",
     *     tags={"Messages"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"sender_id", "receiver_id", "content"},
     *             @OA\Property(property="sender_id", type="integer", example=1),
     *             @OA\Property(property="receiver_id", type="integer", example=2),
     *             @OA\Property(property="content", type="string", example="Hello, how are you?")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Message sent successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'sender_id' => 'required|integer|exists:users,id',
            'receiver_id' => 'required|integer|exists:users,id',
            'content' => 'required|string|max:500',
        ]);

        $message = Message::create($data);
        return response()->json($message, 201);
    }



}
