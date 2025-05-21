<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorites;



class FavoriteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/favorites",
     *     summary="Get all favorites",
     *     tags={"Favorites"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of users"
     *     )
     * )
     */
    public function getAllFavorites()
    {
        $favorites = Favorites::with('user', 'property')->get();
        return response()->json($favorites);
    }

    /**
     * @OA\Get(
     *     path="/api/favorites/{id}",
     *     summary="Get a favorite by ID",
     *     tags={"Favorites"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Favorite not found")
     * )
     */
    public function getFavoriteById($id)
    {
        $favorite = Favorites::with('user', 'property')->find($id);

        if ($favorite) {
            return response()->json($favorite);
        } else {
            return response()->json(['message' => 'Favorite not found'], 404);
        }
    }



    /**
     * @OA\Delete(
     *     path="/api/favorites/{id}",
     *     summary="Delete a favorite",
     *     tags={"Favorites"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Favorite not found")
     * )
     */
    public function deleteFavoriteById($id)
    {
        $favorite = Favorites::find($id);

        if (!$favorite) {
            return response()->json(['message' => 'Favorite not found'], 404);
        }

        $favorite->delete();

        return response()->json(['message' => 'Favorite deleted successfully']);
    }

}
