<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{


    /**
     * Display a listing of all properties (for buyers/public).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getAllProperties(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = Auth::id(); ;

        $query = Property::query()->where('user_id', $userId);

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('title', 'LIKE', '%' . $searchTerm . '%');
        }

        $properties = $query->with('images', 'amenities')->get();

        return response()->json($properties, 200);
    }

    /**
     * Store a newly created property in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function newProperty(Request $request): \Illuminate\Http\JsonResponse
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
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
        'purchase_id' => 'required|exists:purchases,id',
        'images' => 'array|nullable',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        'amenities' => 'array|nullable',
        'amenities.*' => 'exists:amenities,id',
    ]);

    $data['user_id'] = Auth::id();

    DB::beginTransaction();
    try {
        $property = Property::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('property_images', 'public');
                $property->images()->create(['imageUrl' => $path]);
            }
        }

        if (isset($data['amenities'])) {
            $property->amenities()->sync($data['amenities']);
        }

        DB::commit();
        return response()->json($property->load('images', 'amenities'), 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error creating property: ' . $e->getMessage()], 500);
    }
}


    /**
     * Display the specified property.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewProperty($id): \Illuminate\Http\JsonResponse
    {
        $property = Property::with('images', 'amenities')->find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        if (Auth::id() !== $property->user_id) {
            return response()->json(['message' => 'Unauthorized. You do not own this property.'], 403);
        }

        return response()->json($property, 200);
    }


    /**
     * Update the specified property in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProperty(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        if (Auth::id() !== $property->user_id) {
            return response()->json(['message' => 'Unauthorized. You do not own this property.'], 403);
        }

        $data = $request->validate([
            'address' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|nullable|string|max:255',
            'title' => 'sometimes|nullable|string|max:255',
            'landArea' => 'sometimes|nullable|numeric',
            'price' => 'sometimes|nullable|numeric',
            'bedroom' => 'sometimes|nullable|integer',
            'bathroom' => 'sometimes|nullable|integer',
            'parking' => 'sometimes|nullable|integer',
            'longDescreption' => 'sometimes|nullable|string',
            'shortDescreption' => 'sometimes|nullable|string',
            'constructionArea' => 'sometimes|nullable|numeric',
            'livingArea' => 'sometimes|nullable|numeric',
            'property_listing_id' => 'sometimes|nullable|exists:listing_types,id',
            'property_type_id' => 'sometimes|nullable|exists:property_types,id',
            'purchase_id' => 'sometimes|nullable|exists:purchases,id',
            'images' => 'nullable|array',
            'images.*.image_url' => 'url',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        DB::beginTransaction();
        try {
            $property->update($data);

            if (isset($data['images'])) {
                $newImageUrls = collect($data['images'])->pluck('image_url')->toArray();
                $property->images()->whereNotIn('image_url', $newImageUrls)->delete();
                foreach ($data['images'] as $image) {
                    $property->images()->updateOrCreate(
                        ['image_url' => $image['image_url'], 'property_id' => $property->id],
                        ['image_url' => $image['image_url']]
                    );
                }
            } else {
                $property->images()->delete();
            }

            if (isset($data['amenities'])) {
                $property->amenities()->sync($data['amenities']);
            } else {
                $property->amenities()->detach();
            }

            DB::commit();
            return response()->json($property->load('images', 'amenities'), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error updating property: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified property from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProperty($id): \Illuminate\Http\JsonResponse
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        if (Auth::id() !== $property->user_id) {
            return response()->json(['message' => 'Unauthorized. You do not own this property.'], 403);
        }

        DB::beginTransaction();
        try {
            $property->images()->delete();
            $property->amenities()->detach();
            $property->delete();

            DB::commit();
            return response()->json(['message' => 'Property deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error deleting property: ' . $e->getMessage()], 500);
        }
    }
}
