<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    /**
     * Display a user's profile.
     *
     * @OA\Get(
     *     path="/api/profile/{userId}",
     *     summary="Get a user's profile",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of the user whose profile is to be retrieved",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile successfully retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="image_path", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="current_position", type="string"),
     *             @OA\Property(property="facebook_url", type="string", format="uri"),
     *             @OA\Property(property="twitter_url", type="string", format="uri"),
     *             @OA\Property(property="linkedin_url", type="string", format="uri"),
     *             @OA\Property(property="instagram_url", type="string", format="uri")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized access",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="You are not authorized to view this profile.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="No query results for model [App\\Models\\Profile].")
     *         )
     *     )
     * )
     */
    public function getProfileByUid($userId)
    {
        $user = User::with('profile')->find($userId);

        if (!$user) {
            return response()->json([
                'error' => 'User not found.'
            ], 404);
        }

        if (auth()->id() !== $userId) {
            return response()->json([
                'error' => 'Unauthorized.'
            ], 403);
        }

        if (!$user->profile) {
            return response()->json([
                'error' => 'Profile not found.'
            ], 404);
        }

        return response()->json([
            'hello word'
        ]);
    }
    /**
     * Update the authenticated user's profile.
     *
     * @OA\Put(
     *     path="/api/profile",
     *     summary="Update user profile",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="current_position", type="string"),
     *             @OA\Property(property="facebook_url", type="string", format="uri"),
     *             @OA\Property(property="twitter_url", type="string", format="uri"),
     *             @OA\Property(property="linkedin_url", type="string", format="uri"),
     *             @OA\Property(property="instagram_url", type="string", format="uri"),
     *             @OA\Property(property="image_path", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="profile", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="image_path", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="location", type="string"),
     *                 @OA\Property(property="current_position", type="string"),
     *                 @OA\Property(property="facebook_url", type="string", format="uri"),
     *                 @OA\Property(property="twitter_url", type="string", format="uri"),
     *                 @OA\Property(property="linkedin_url", type="string", format="uri"),
     *                 @OA\Property(property="instagram_url", type="string", format="uri")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     )
     * )
     */
    public function updateProfileInfo(Request $request, $userId)
    {
        if (Auth::id() !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'current_position' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profile = Profile::where('user_id', $userId)->firstOrFail();

        // Update fields
        $profile->fill([
            'phone' => $validated['phone'] ?? $profile->phone,
            'location' => $validated['location'] ?? $profile->location,
            'current_position' => $validated['current_position'] ?? $profile->current_position,
            'facebook_url' => $validated['facebook_url'] ?? $profile->facebook_url,
            'twitter_url' => $validated['twitter_url'] ?? $profile->twitter_url,
            'linkedin_url' => $validated['linkedin_url'] ?? $profile->linkedin_url,
            'instagram_url' => $validated['instagram_url'] ?? $profile->instagram_url,
        ]);

        // Handle image upload
        if ($request->hasFile('image_path')) {
            // Delete old image if exists
            if ($profile->image_path && Storage::exists($profile->image_path)) {
                Storage::delete($profile->image_path);
            }

            $path = $request->file('image_path')->store('profiles');
            $profile->image_path = $path;
        }
        $profile->save();

        return response()->json(['message' => 'Profile updated.', 'profile' => $profile], 200);
    }
    /**
     * @OA\Delete(
     *     path="/api/profile/picture",
     *     summary="Remove user's profile picture",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile picture removed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized action",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Profile not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function removeProfilePicture($userId)
    {
        // التحقق من أن المستخدم هو نفسه
        if (auth()->id() !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // جلب المستخدم مع ملفه الشخصي
        $user = User::with('profile')->find($userId);

        // التحقق من وجود المستخدم وملفه الشخصي
        if (!$user || !$user->profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $profile = $user->profile;

        // حذف الصورة إذا كانت موجودة
        if ($profile->image_path && Storage::exists($profile->image_path)) {
            Storage::delete($profile->image_path);
        }

        // تعيين الصورة إلى null
        $profile->image_path = null;
        $profile->save();

        return response()->json(['message' => 'Profile picture removed.'], 200);
    }
}

