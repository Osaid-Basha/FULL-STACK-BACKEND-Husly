<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ManageReviewController extends Controller
{
    //
 public function searchReviews($keyword)
{
    $reviews = Review::with('user')
        ->where(function ($query) use ($keyword) {
            $query->where('title', 'like', "%$keyword%")
                ->orWhere('content', 'like', "%$keyword%")
                ->orWhereHas('user', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%$keyword%")
                    ->orWhere('last_name', 'like', "%$keyword%")
                    ->orWhere('email', 'like', "%$keyword%");
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

public function getAllReviews()
{
    $reviews = Review::with('user')->get();

    if ($reviews->isEmpty()) {
        return response()->json(['message' => 'No reviews found'], 404);
    }

    return response()->json([
        'status' => 200,
        'reviews' => $reviews
    ]);

}

public function deleteReview($id)
{
    $review = Review::find($id);

    if (!$review) {
        return response()->json(['message' => 'Review not found'], 404);
    }

    $review->delete();

    return response()->json(['message' => 'Review deleted successfully']);
}

}

