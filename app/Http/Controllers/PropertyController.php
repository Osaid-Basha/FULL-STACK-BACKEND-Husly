<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage; // Make sure this is imported
use App\Models\Amenity;     // Make sure this is imported
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification; // Make sure this is imported
use App\Models\User;         // Make sure this is imported
use Illuminate\Support\Facades\Storage; // Import Storage facade

class PropertyController extends Controller
{
    /**
     * Display a listing of all properties for the authenticated agent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllProperties(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

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

            $recipients = User::whereIn('role_id', [1, 3])->pluck('id')->toArray();
            Notification::sendToMultipleUsers(
                $recipients,
                'property_created',
                "A new property '{$property->title}' has been added to the system."
            );

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

        // Validate incoming data
        // For array inputs (like amenities, images), Laravel expects numeric keys for validation,
        // and when used with FormData from Angular, they arrive as 'amenities[]'.
        // So 'amenities.*' and 'images.*' are correct for validation.
        $data = $request->validate([
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'landArea' => 'sometimes|numeric',
            'price' => 'sometimes|numeric',
            'bedroom' => 'sometimes|integer',
            'bathroom' => 'sometimes|integer',
            'parking' => 'sometimes|integer',
            'longDescreption' => 'sometimes|string',
            'shortDescreption' => 'sometimes|string',
            'constructionArea' => 'sometimes|numeric',
            'livingArea' => 'sometimes|numeric',
            'property_listing_id' => 'sometimes|exists:listing_types,id',
            'property_type_id' => 'sometimes|exists:property_types,id',
            'purchase_id' => 'sometimes|exists:purchases,id',
            'images' => 'array|nullable',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validate new image uploads
            'amenities' => 'array|nullable',
            'amenities.*' => 'exists:amenities,id',
        ]);

        DB::beginTransaction();
        try {
            // Update property details, excluding images and amenities for now
            // Use $request->input() to get data that might not be validated or present
            // Or use $data array for validated inputs
            $property->update($data); // This updates all simple fields passed in $data

            // Handle images:
            // If new image files are uploaded, clear old images and add new ones.
            if ($request->hasFile('images')) {
                // Delete old images from storage and database
                foreach ($property->images as $image) {
                    Storage::disk('public')->delete($image->imageUrl);
                    $image->delete();
                }

                // Upload and save new images
                foreach ($request->file('images') as $image) {
                    $path = $image->store('property_images', 'public');
                    $property->images()->create(['imageUrl' => $path]);
                }
            }


            // Handle amenities
            if (isset($data['amenities'])) {
                // sync will detach amenities not in the new list and attach new ones
                $property->amenities()->sync($data['amenities']);
            } else {

                $property->amenities()->detach();
            }

            $recipients = User::whereIn('role_id', [1, 3])->pluck('id')->toArray();
            Notification::sendToMultipleUsers(
                $recipients,
                'property_updated',
                "The property '{$property->title}' has been updated."
            );

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
            // Delete associated images from storage
            foreach ($property->images as $image) {
                Storage::disk('public')->delete($image->imageUrl);
            }
            $property->images()->delete(); // Delete image records from DB
            $property->amenities()->detach(); // Detach amenities
            $property->delete(); // Delete the property record

            DB::commit();
            $recipients = User::whereIn('role_id', [1, 3])->pluck('id')->toArray();
            Notification::sendToMultipleUsers(
                $recipients,
                'property_deleted',
                "The property '{$property->title}' has been deleted."
            );

            return response()->json(['message' => 'Property deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error deleting property: ' . $e->getMessage()], 500);
        }
    }
}
