<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\property;

class PropertyBuyerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/properties",
     *     summary="Get all properties",
     *     tags={"Properties"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of properties"
     *     )
     * )
     */
    public function getAllProperties()
    {
        $properties = Property::all();
        return response()->json($properties);
    }

    /**
     * @OA\Get(
     *     path="/api/properties/search",
     *     summary="Search for properties using filters",
     *     tags={"Properties"},
     *     @OA\Parameter(
     *         name="location",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *            type="string",
     *             enum={"Property","Offices","Apartments","Houses","Villa","Duplex","Penthouse","Townhouse"}
     *        )
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="listing_type_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Filtered list of properties"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No results found"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = Property::query();

        if ($request->location) {
            $query->where('city', 'LIKE', "%{$request->location}%");
        }
        if ($request->type) {
            $query->whereHas('property_type', function ($q) use ($request) {
                $q->where('type','LIKE',"%{$request->type}%");
            });
        }
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->listing_type_id) {
            $query->where('property_listing_id', $request->listing_type_id);
        }
        if ($request->keyword) {
            $query->where('title', 'LIKE', "%{$request->keyword}%");
        }

        $results = $query->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No results found'], 404);
        }

        return response()->json($results);
    }
    /**
     * @OA\Get(
     *     path="/api/properties/{id}",
     *     summary="Get single property details",
     *     tags={"Properties"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the property to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Property details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Property not found"
     *     )
     * )
     */
    public function show($id)
    {
        $property = Property::with([
            'property_type',
            'listing_type',
            'purchase',
            'images',
            'user',
            'amenity'
        ])->find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return response()->json($property);
    }
}
