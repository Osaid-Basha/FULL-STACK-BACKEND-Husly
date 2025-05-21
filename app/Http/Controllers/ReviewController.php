<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Replay;
use Illuminate\Support\Facades\Auth;

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


    $alreadyReviewed = Review::where('buying_id', $request->buying_id)->exists();
    if ($alreadyReviewed) {
        return response()->json(['message' => 'Review already submitted'], 400);
    }

    $review = Review::create([
        'title' => $request->title,
        'content' => $request->content,
        'buying_id' => $request->buying_id,
        'user_id' => Auth::id(),
        'rating' => $request->rating,
    ]);

    return response()->json([
        'status' => 201,
        'message' => 'Review created successfully',
        'review' => $review,
    ]);
}

public function storeReplay(Request $request)
{
    $request->validate([
        'message_content' => 'required|string',
        'review_id' => 'required|exists:reviews,id',
    ]);

    $replay = Replay::create([
        'message_content' => $request->message_content,
        'user_id' => Auth::id(),
        'review_id' => $request->review_id,
    ]);

    return response()->json([
        'status' => 201,
        'message' => 'Reply created successfully',
        'replay' => $replay,
    ]);
}
public function myReviews()
    {
        $agent = Auth::user();

        $reviews = Review::with(['user', 'reply', 'buyingRequest.property'])
            ->whereHas('buyingRequest.property', function ($query) use ($agent) {
                $query->where('user_id', $agent->id);
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

