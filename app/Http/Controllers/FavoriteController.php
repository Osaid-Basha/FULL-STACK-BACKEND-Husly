<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorites;
use Illuminate\Support\Facades\Auth;



class FavoriteController extends Controller
{

    public function getAllFavorites()
    {
        $userId=Auth::id();
        $favorites = Favorites::with('user', 'property')->where('user_id', $userId)->get();
        return response()->json($favorites);
    }



public function addFavorite(Request $request)
{
    $request->validate([
        'property_id' => 'required|exists:properties,id',
    ]);

    $userId = Auth::id();


    $alreadyExists = Favorites::where('user_id', $userId)
                            ->where('property_id', $request->property_id)
                            ->exists();

    if ($alreadyExists) {
        return response()->json(['message' => 'Already in favorites'], 409);
    }

    Favorites::create([
        'user_id' => $userId,
        'property_id' => $request->property_id,
    ]);

    return response()->json(['message' => 'Added to favorites'], 201);
}

public function deleteFavorite(Request $request)
{
    $request->validate([
        'property_id' => 'required|exists:properties,id',
    ]);

    $userId = Auth::id();

    $favorite = Favorites::where('user_id', $userId)
                        ->where('property_id', $request->property_id)
                        ->first();

    if (!$favorite) {
        return response()->json(['message' => 'Not found'], 404);
    }

    $favorite->delete();

    return response()->json(['message' => 'Removed from favorites'], 200);
}


}
