<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\TrustedDevice;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    //

public function register(Request $request)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|same:confirm_password',
        'confirm_password' => 'required|string|min:8',
        'role_id' => 'required|exists:roles,id',
    ]);

    $status = in_array($validated['role_id'], [2, 3]) ? 0 : 1; // 0 = Pending, 1 = Active

    $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role_id' => $validated['role_id'],
        'status' => $status,
    ]);


    $user->profile()->create([
        'phone' => null,
        'location' => null,
        'current_position' => null,
        'facebook_url' => null,
        'twitter_url' => null,
        'linkedin_url' => null,
        'instagram_url' => null,
        'imag_path' => 'profiles/profile.png',
    ]);

    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user->load('role', 'profile')
    ], 201);
}


public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        
        'remember_me' => 'sometimes|boolean'
    ]);

    $user = User::where('email', $request->email)->with('role')->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    if ($user->status != 1) {
        return response()->json(['message' => 'Your account is pending approval'], 403);
    }



   $trustedToken = $request->header('X-Trusted-Device');
Log::info('X-Trusted-Device Received:', [$trustedToken]);

if ($trustedToken) {
    $trusted = TrustedDevice::where('user_id', $user->id)
        ->where('device_token', $trustedToken)
        ->where('expires_at', '>', now())
        ->first();

    if ($trusted) {
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login via trusted device',
            'user' => $user,
            'token' => $token
        ], 200);
    }
}



    if ($request->boolean('remember_me')) {
        $trusted_token = Str::random(64);

       TrustedDevice::create([
            'user_id' => $user->id,
            'device_token' => $trusted_token,
            'device_name' => $request->header('User-Agent'),
            'ip_address' => $request->ip(),
            'expires_at' => now()->addDays(30)
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful without 2FA',
            'user' => $user,
            'token' => $token,
            'trusted_token' => $trusted_token
        ], 200);
    }

    $user->generateTwoFactorCode();
    $user->sendTwoFactorCodeEmail();
    return response()->json([
        'message' => 'Please verify your 2FA code',
        'user_id' => $user->id,
    ], 200);
}









    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logout successfully']);
    }






public function sendResetLink(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    $status = Password::sendResetLink($request->only('email'));


    return $status === Password::RESET_LINK_SENT
        ? response()->json(['message' => 'Reset link sent to your email.'])
        : response()->json(['message' => 'Unable to send reset link.'], 500);
}




public function verify2FA(Request $request)
{
    $request->validate([
        'user_id' => 'required|integer',
        'code' => 'required|string',
        'remember_me' => 'sometimes|boolean'
    ]);

    $user = User::with('role')->findOrFail($request->user_id);

    if (!Hash::check($request->code, $user->two_factor_code))
 {
        return response()->json(['message' => 'Invalid verification code'], 401);
    }

    if (Carbon::now()->gt($user->two_factor_expires_at)) {
        return response()->json(['message' => 'Verification code expired'], 401);
    }

    $user->resetTwoFactorCode();

    $token = $user->createToken('authToken')->plainTextToken;

    $response = [
        'message' => '2FA Verified Successfully',
        'user' => $user,
        'token' => $token,
    ];


    if ($request->boolean('remember_me')) {
        $trusted_token = Str::random(64);

        TrustedDevice::create([
            'user_id' => $user->id,
            'device_token' => $trusted_token,
            'device_name' => $request->header('User-Agent'),
            'ip_address' => $request->ip(),
            'expires_at' => now()->addDays(30)
        ]);

        $response['trusted_token'] = $trusted_token;
    }

    return response()->json($response, 200);
}









    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8|same:password',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );


        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset.'])
            : response()->json(['message' => 'Reset failed.'], 500);
    }


public function resend2FA(Request $request)
{
    $user = User::findOrFail($request->user_id);

    $user->generateTwoFactorCode();
    $user->sendTwoFactorCodeEmail();

    return response()->json(['message' => 'A new code has been sent.']);
}





}
