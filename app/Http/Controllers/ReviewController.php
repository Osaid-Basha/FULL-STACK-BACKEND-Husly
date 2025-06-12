<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Replay;
use App\Models\BuyingRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
class ReviewController extends Controller
{
    //
public function storeReview(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'buying_id' => 'required|exists:buying_requests,id',
        'rating' => 'required|integer|between:1,5',
    ]);

    $buying = BuyingRequest::where('id', $request->buying_id)
        ->where('user_id', Auth::id())
        ->where('status', 1)

        ->whereNotNull('negotiation_id')
        ->first();

    if (!$buying) {
        return response()->json([
            'status' => 403,
            'message' => 'You are not authorized to review this buying request or it is not confirmed yet.'
        ]);
    }


    $alreadyReviewed = Review::where('buying_id', $request->buying_id)->exists();
    if ($alreadyReviewed) {
        return response()->json([
            'status' => 400,
            'message' => 'Review already submitted for this purchase.'
        ]);
    }


    $review = Review::create([
        'title' => $request->title,
        'content' => $request->content,
        'buying_id' => $request->buying_id,
        'user_id' => Auth::id(),
        'rating' => $request->rating,
    ]);
    $propertyOwnerId = $buying->property->user_id;

Notification::sendToUser(
    $propertyOwnerId,
    'new_review',
    "You received a new review for your property '{$buying->property->title}'."

);

    return response()->json([
        'status' => 201,
        'message' => 'Review created successfully.',
        'review' => $review,
    ]);
}


public function storeReplay(Request $request)
{
    $request->validate([
        'message_content' => 'required|string',
        'review_id' => 'required|exists:reviews,id',
    ]);

    $review = Review::with('buyingRequest.property')->find($request->review_id);
    if (!$review || $review->buyingRequest->property->user_id !== Auth::id()) {
        return response()->json([
            'status' => 403,
            'message' => 'You are not authorized to reply to this review.',
        ]);
    }

    $replay = Replay::create([
        'message_content' => $request->message_content,
        'user_id' => Auth::id(),
        'review_id' => $request->review_id,
    ]);
    $buyerId = $review->user_id;
    Notification::sendToUser(
        $buyerId,
        'reply_review',
        "Your review on '{$review->buyingRequest->property->title}' has been replied to."

    );

    return response()->json([
        'status' => 201,
        'message' => 'Reply created successfully',
        'replay' => $replay,
    ]);
}

public function myReviews()
{
    $agentId = Auth::id();

    $reviews = Review::with([
        'user.profile',     // ✅ جلب صورة المستخدم
        'replies.user',
        'buyingRequest.property'
    ])
    ->whereHas('buyingRequest.property', function ($query) use ($agentId) {
        $query->where('user_id', $agentId);
    })
    ->get();

    return response()->json([
        'status' => 200,
        'reviews' => $reviews
    ]);
}



public function searchReviews($keyword)
{
    $reviews = Review::with('user')
        ->where(function ($query) use ($keyword) {
            $query->where('title', 'like', "%$keyword%")
                ->orWhere('content', 'like', "%$keyword%")
                ->orWhereHas('user', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%$keyword%")
                    ->orWhere('last_name', 'like', "%$keyword%");
                });
        })
        ->get();

    if ($reviews->isEmpty()) {
        return response()->json(['message' => 'No reviews found'], 404);
    }

    return response()->json([
        'status' => 200,
        'reviews' => $reviews
    ]);
}

}

