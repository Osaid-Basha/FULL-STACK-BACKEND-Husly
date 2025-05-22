<?php

namespace App\Http\Controllers;

use App\Models\PropertyImage;
use Illuminate\Http\Request;

class PropertyImageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/property-images",
     *     summary="Get all property images",
     *     tags={"Property Images"},
     *     @OA\Response(response=200, description="List of all property images")
     * )
     */
    public function index()
    {
        return response()->json(PropertyImage::all(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/property-images",
     *     summary="Upload a new property image",
     *     tags={"Property Images"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"property_id", "image_url"},
     *             @OA\Property(property="property_id", type="integer", example=1),
     *             @OA\Property(property="image_url", type="string", format="url", example="https://example.com/image.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Image uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=10),
     *             @OA\Property(property="property_id", type="integer", example=1),
     *             @OA\Property(property="image_url", type="string", example="https://example.com/image.jpg"),
     *             @OA\Property(property="created_at", type="string", example="2025-05-21T12:34:56Z"),
     *             @OA\Property(property="updated_at", type="string", example="2025-05-21T12:34:56Z")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */

    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
            'image_url' => 'required|url',
        ]);


        $image = PropertyImage::create($data);
        return response()->json($image, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/property-images/{id}",
     *     summary="Get a specific property image",
     *     tags={"Property Images"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Property Image ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Image details"),
     *     @OA\Response(response=404, description="Image not found")
     * )
     */
    public function show($id)
    {
        $image = PropertyImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }
        return response()->json($image, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/property-images/{id}",
     *     summary="Update a property image",
     *     tags={"Property Images"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Property Image ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="image_url", type="string", format="url", example="https://example.com/new-image.jpg")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Image updated successfully"),
     *     @OA\Response(response=404, description="Image not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $image = PropertyImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $data = $request->validate([
            'image_url' => 'required|url',
        ]);

        $image->update($data);
        return response()->json($image, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/property-images/{id}",
     *     summary="Delete a property image",
     *     tags={"Property Images"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Property Image ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Image deleted successfully"),
     *     @OA\Response(response=404, description="Image not found")
     * )
     */
    public function destroy($id)
    {
        $image = PropertyImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $image->delete();
        return response()->json(['message' => 'Image deleted successfully'], 200);
    }
}
