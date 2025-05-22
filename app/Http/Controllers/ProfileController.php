<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{


    public function getProfileByUid()
    {
        $userId = Auth::id();
        $user = User::with('profile')->find($userId);

        if (!$user) {
            return response()->json([
                'error' => 'User not found.'
            ], 404);
        }

        if (Auth::id() !== $userId) {
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
    'status' => 200,
    'user' => $user,
    'profile' => $user->profile
]);

    }

    public function updateProfileInfo(Request $request)
    {   $userId = Auth::id();

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

    public function removeProfilePicture()
    {
        $userId = Auth::id();

        $user = User::with('profile')->find($userId);

        if (!$user || !$user->profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $profile = $user->profile;

        if ($profile->image_path && Storage::exists($profile->image_path)) {
            Storage::delete($profile->image_path);
        }

        $profile->image_path = null;
        $profile->save();

        return response()->json(['message' => 'Profile picture removed.'], 200);
    }
}

