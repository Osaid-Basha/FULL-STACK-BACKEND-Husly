<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
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
            // ✅ هذه هي القواعد الأساسية التي يجب أن تكون موجودة
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'landArea' => 'required|numeric',
            'price' => 'required|numeric',
            'bedroom' => 'required|integer',
            'bathroom' => 'required|integer',
            'parking' => 'required|integer',
            'longDescreption' => 'nullable|string', // يمكن أن يكون nullable
            'shortDescreption' => 'nullable|string', // يمكن أن يكون nullable
            'constructionArea' => 'required|numeric',
            'livingArea' => 'required|numeric',
            'property_listing_id' => 'required|exists:listing_types,id',
            'property_type_id' => 'required|exists:property_types,id',
            'purchase_id' => 'sometimes|nullable|integer', // إذا كان purchase_id اختياري، استخدم sometimes|nullable
            // ✅ قواعد التحقق للصور والـ amenities (كما هي لديك)
            'images' => 'array|nullable',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'amenities' => 'array|nullable',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $data['user_id'] = Auth::id();

        DB::beginTransaction();
        try {
            // يتم إنشاء العقار باستخدام جميع البيانات التي تم التحقق منها
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
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        // Validate images separately if new files are being uploaded
        if ($request->hasFile('images')) {
            $request->validate([
                'images' => 'array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        }
        // If images array is sent without files, it means existing image URLs are being passed
        // We don't need a specific validation rule for the array of URLs here, as we'll process them.

        DB::beginTransaction();
        try {
            $property->update($data);

            // --- Image Handling Logic ---
            if ($request->hasFile('images')) {
                // Scenario 1: New image files are uploaded from Angular.
                // This means existing images should be replaced.

                // Delete old image files from storage
                foreach ($property->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage->imageUrl);
                }
                // Delete old image records from the database
                $property->images()->delete();

                // Save new image files
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('property_images', 'public'); // Stores 'property_images/filename.jpg'
                    $property->images()->create([
                        'imageUrl' => $path, // Store the relative path directly
                    ]);
                }
            } elseif ($request->has('images')) {
                // Scenario 2: No new files were uploaded, but Angular sent an 'images' array.
                // This array now contains only *relative paths* of images that should remain.
                // It might be empty if the user deleted all images from the UI.

                $incomingRelativePaths = $request->input('images');

                // Get current relative paths from DB
                $currentDbRelativePaths = $property->images->pluck('imageUrl')->toArray();

                // 1. Delete images that are in DB but NOT in the incoming array (i.e., removed by user)
                $imagesToDelete = array_diff($currentDbRelativePaths, $incomingRelativePaths);
                foreach ($imagesToDelete as $relativePathToDelete) {
                    Storage::disk('public')->delete($relativePathToDelete); // Delete the actual file from storage
                    $property->images()->where('imageUrl', $relativePathToDelete)->delete(); // Delete the record from DB
                }

                // 2. Add images that are in the incoming array but NOT in DB (shouldn't happen for existing images
                //    if Angular's logic for sending existing images is solid. This is mostly for
                //    edge cases or if Angular sends a mix of existing and newly added non-file images.)
                //    However, if Angular always sends ALL images that should be associated (existing + new files),
                //    and `hasFile('images')` is the check for *new uploads*, then this block handles
                //    the case where the *list of existing images* has changed (some removed).
                //    Since Angular sends relative paths of *existing* images, we just need to ensure
                //    they are still linked. The `sync` method for many-to-many is better if images
                //    were related many-to-many, but for one-to-many, we handle additions/deletions.
                //    For one-to-many: no need to re-add existing ones if they are already in the DB.
                //    The primary goal here is to remove those that are no longer in the list.

                // If $incomingRelativePaths is an empty array, it means all existing images were removed.
                if (empty($incomingRelativePaths)) {
                    foreach ($property->images as $oldImage) {
                        Storage::disk('public')->delete($oldImage->imageUrl);
                    }
                    $property->images()->delete();
                }
                // If Angular sent existing image URLs, we don't need to re-create them in the DB
                // because they already exist. The deletion logic above handles removals.
                // We could add a check for new imageUrls that are *not files* but are sent,
                // if that's a possible scenario (e.g., predefined image library).
                // For now, assuming image addition is via file upload only for new ones.
            }


            // --- Amenities Handling ---
            if (isset($data['amenities'])) {
                $property->amenities()->sync($data['amenities']);
            } else {
                $property->amenities()->detach(); // Detach all amenities if none are sent
            }

            // --- Notifications ---
            $recipients = User::whereIn('role_id', [1, 3])->pluck('id')->toArray();
            Notification::sendToMultipleUsers(
                $recipients,
                'property_updated',
                "The property '{$property->title}' has been updated."
            );

            DB::commit();
            // Load images and amenities before returning the response
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
