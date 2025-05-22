<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/properties",
     * summary="Get all properties, with optional search by name",
     * tags={"Properties"},
     * @OA\Parameter(
     * name="search",
     * in="query",
     * description="Search term for property name (e.g., 'Villa')",
     * required=false,
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(response=200, description="List of all properties or matching properties")
     * )
     */

    public function index(Request $request)
    {

        $query = Property::query();


        if ($request->has('search')) {
            $searchTerm = $request->input('search');

            $query->where('title', 'LIKE', '%' . $searchTerm . '%');
        }

        $properties = $query->get();

        return response()->json($properties, 200);
    }

    /**
     * @OA\Post(
     * path="/api/properties",
     * summary="Add a new property",
     * tags={"Properties"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "description", "price", "location"},
     * @OA\Property(property="name", type="string", example="Villa Sunset"),
     * @OA\Property(property="description", type="string", example="A luxurious villa with a sea view."),
     * @OA\Property(property="price", type="number", example=350000),
     * @OA\Property(property="location", type="string", example="123 Beach St, Miami, FL")
     * )
     * ),
     * @OA\Response(response=201, description="Property created successfully"),
     * @OA\Response(response=422, description="Invalid data"),
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'landArea' => 'required|numeric',
            'price' => 'required|numeric',
            'bedroom' => 'required|integer',
            'bathroom' => 'required|integer',
            'parking' => 'required|integer',
            'longDescreption' => 'required|string',
            'shortDescreption' => 'required|string',
            'constructionArea' => 'required|numeric',
            'livingArea' => 'required|numeric',
            'property_listing_id' => 'required|exists:listing_types,id',
            'property_type_id' => 'required|exists:property_types,id',
            'user_id' => 'required|exists:users,id',
            'purchase_id' => 'required|exists:purchases,id',
        ]);

        $property = Property::create($data);
        return response()->json($property, 201);
    }

    /**
     * @OA\Get(
     * path="/api/properties/{id}",
     * summary="Get a specific property",
     * tags={"Properties"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Property ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=200, description="Property details"),
     * @OA\Response(response=404, description="Property not found"),
     * )
     */
    // هذه الوظيفة تعرض property واحدة فقط عن طريق الـ ID
    public function show($id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }
        return response()->json($property, 200);
    }

    /**
     * @OA\Put(
     * path="/api/properties/{id}",
     * summary="Update a property",
     * tags={"Properties"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Property ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string", example="Villa Sunset Updated"),
     * @OA\Property(property="description", type="string", example="An updated luxurious villa."),
     * @OA\Property(property="price", type="number", example=400000),
     * @OA\Property(property="location", type="string", example="456 Palm St, Miami, FL")
     * )
     * ),
     * @OA\Response(response=200, description="Property updated successfully"),
     * @OA\Response(response=404, description="Property not found"),
     * )
     */
    public function update(Request $request, $id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $data = $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'landArea' => 'required|numeric',
            'price' => 'required|numeric',
            'bedroom' => 'required|integer',
            'bathroom' => 'required|integer',
            'parking' => 'required|integer',
            'longDescreption' => 'required|string',
            'shortDescreption' => 'required|string',
            'constructionArea' => 'required|numeric',
            'livingArea' => 'required|numeric',
            'property_listing_id' => 'required|exists:listing_types,id',
            'property_type_id' => 'required|exists:property_types,id',
            'user_id' => 'required|exists:users,id',
            'purchase_id' => 'required|exists:purchases,id',
        ]);

        $property->update($data);
        return response()->json($property, 200);
    }


    /**
     * @OA\Delete(
     * path="/api/properties/{id}",
     * summary="Delete a property",
     * tags={"Properties"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Property ID",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(response=200, description="Property deleted successfully"),
     * @OA\Response(response=404, description="Property not found"),
     * )
     */
    public function destroy($id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $property->delete();
        return response()->json(['message' => 'Property deleted successfully'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/properties/{propertyId}/amenities",
     *     summary="Get a list of amenities for a specific property",
     *     tags={"Properties"},
     *     @OA\Parameter(
     *         name="propertyId",
     *         in="path",
     *         description="ID of the property",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of amenities",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string", example="garden")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Property not found"
     *     )
     * )
     */
    public function getAmenities($propertyId)
    {
        $property = Property::with('amenity')->find($propertyId);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return response()->json($property->amenity->pluck('name'), 200);
    }


}
