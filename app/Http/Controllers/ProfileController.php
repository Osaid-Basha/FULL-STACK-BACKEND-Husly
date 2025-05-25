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

    if (!$user->profile) {
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
        'imag_url' => $user->profile->image_path
            ? asset('storage/' . $user->profile->image_path)
            : null
    ]);
}





    public function updateProfileInfo(Request $request)
{
    $userId = Auth::id();

    $validated = $request->validate([
        'phone' => 'nullable|string|max:20',
        'location' => 'nullable|string|max:255',
        'current_position' => 'nullable|string|max:255',
        'facebook_url' => 'nullable|url',
        'twitter_url' => 'nullable|url',
        'linkedin_url' => 'nullable|url',
        'instagram_url' => 'nullable|url',
        'imag_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $profile = Profile::where('user_id', $userId)->firstOrFail();

    $profile->fill([
        'phone' => $validated['phone'] ?? $profile->phone,
        'location' => $validated['location'] ?? $profile->location,
        'current_position' => $validated['current_position'] ?? $profile->current_position,
        'facebook_url' => $validated['facebook_url'] ?? $profile->facebook_url,
        'twitter_url' => $validated['twitter_url'] ?? $profile->twitter_url,
        'linkedin_url' => $validated['linkedin_url'] ?? $profile->linkedin_url,
        'instagram_url' => $validated['instagram_url'] ?? $profile->instagram_url,
    ]);

    if ($request->hasFile('imag_path')) {
        if ($profile->imag_path && Storage::disk('public')->exists($profile->imag_path)) {
            Storage::disk('public')->delete($profile->imag_path);
        }

        $path = $request->file('imag_path')->store('profiles', 'public');
        $profile->imag_path = $path;
    }

    $profile->save();

    return response()->json([
        'message' => 'Profile updated.',
        'profile' => $profile,
        'imag_url' => $profile->imag_path ? asset('storage/' . $profile->imag_path) : null
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

