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
        $user = User::with('profile')->find(Auth::id());

        if (!$user || !$user->profile) {
            return response()->json([
                'error' => 'Profile not found.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ],
            'profile' => $user->profile,
            'imag_url' => $user->profile->imag_path
                ? asset('storage/' . $user->profile->imag_path)
                : null
        ]);
    }

    public function updateProfileInfo(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            // users table
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',

            // profile table
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'current_position' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|string|max:255',
            'twitter_url' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($userId);
        $user->first_name = $validated['first_name'] ?? $user->first_name;
        $user->last_name = $validated['last_name'] ?? $user->last_name;
        $user->email = $validated['email'] ?? $user->email;
        $user->save();

        $profile = Profile::where('user_id', $userId)->firstOrFail();
        $profile->phone = $validated['phone'] ?? $profile->phone;
        $profile->location = $validated['location'] ?? $profile->location;
        $profile->current_position = $validated['current_position'] ?? $profile->current_position;
        $profile->facebook_url = $validated['facebook_url'] ?? $profile->facebook_url;
        $profile->twitter_url = $validated['twitter_url'] ?? $profile->twitter_url;
        $profile->linkedin_url = $validated['linkedin_url'] ?? $profile->linkedin_url;
        $profile->instagram_url = $validated['instagram_url'] ?? $profile->instagram_url;
        $profile->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user,
            'profile' => $profile
        ], 200);
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json(['error' => 'Profile not found.'], 404);
        }

        // حذف الصورة القديمة
        if ($profile->imag_path && Storage::disk('public')->exists($profile->imag_path)) {
            Storage::disk('public')->delete($profile->imag_path);
        }

        // رفع الصورة الجديدة
        $path = $request->file('image')->store('profiles', 'public');
        $profile->imag_path = $path;
        $profile->save();

        return response()->json([
            'message' => 'Profile picture updated.',
            'imag_url' => asset('storage/' . $path)
        ], 200);
    }




    public function removeProfilePicture()
{
    $userId = Auth::id();
    $user = User::with('profile')->find($userId);

    if (!$user || !$user->profile) {
        return response()->json(['error' => 'Profile not found'], 404);
    }

    $profile = $user->profile;

    if ($profile->imag_path && Storage::disk('public')->exists($profile->imag_path)) {
        Storage::disk('public')->delete($profile->imag_path);
    }

    $profile->imag_path = null;
    $profile->save();

    return response()->json(['message' => 'Profile picture removed.'], 200);
}

}

