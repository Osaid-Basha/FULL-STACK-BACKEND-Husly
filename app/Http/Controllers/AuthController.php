<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    //
     /**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Register a new user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name", "last_name", "email", "password", "confirm_password", "role_id"},
 *             @OA\Property(property="first_name", type="string", example="Mohammad"),
 *             @OA\Property(property="last_name", type="string", example="Salem"),
 *             @OA\Property(property="email", type="string", format="email", example="mohammad@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="12345678"),
 *             @OA\Property(property="confirm_password", type="string", format="password", example="12345678"),
 *             @OA\Property(property="role_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(response=201, description="User registered successfully")
 * )
 */
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

    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user->load('role')
    ], 201);
}
   /**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Login user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password", "role"},
 *             @OA\Property(property="email", type="string", format="email", example="mohammad@example.com"),
 *             @OA\Property(property="password", type="string", example="12345678"),
 *             @OA\Property(property="role", type="string", example="admin")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Login successfully"),
 *     @OA\Response(response=403, description="Role mismatch"),
 *     @OA\Response(response=401, description="Incorrect email or password")
 * )
 */
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string'
    ]);


    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = User::where('email', $request->email)->first();
if ($user->status != 1) {
    return response()->json(['message' => 'Your account is pending approval'], 403);
}


    $user->generateTwoFactorCode();
    $user->sendTwoFactorCodeEmail();

    return response()->json([
        'message' => 'Verification code sent to your email.',
        'user_id' => $user->id
    ]);
}

    /**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Logout the authenticated user",
 *     tags={"Authentication"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logged out successfully"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logout successfully']);
    }


    /**
 * @OA\Post(
 *     path="/api/forgot-password",
 *     summary="Send password reset link to email",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="albashaosayd@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reset link sent to your email."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Unable to send reset link."
 *     )
 * )
 */

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json([
                'message' => 'Reset link sent to your email.'
            ])
            : response()->json(['message' => 'Unable to send reset link.'], 500);
    }



    /**
     * @OA\Post(
     *    path="/api/verify-2fa",
     *   summary="Verify 2FA code",
     *   tags={"Authentication"},
     *  @OA\RequestBody(
     *        required=true,
     *       @OA\JsonContent(
     *           required={"user_id", "code"},
     *          @OA\Property(property="user_id", type="integer", example=1),
     *         @OA\Property(property="code", type="string", example="123456")
     *       )
     *   ),
     *  @OA\Response(
     *       response=200,
     *      description="2FA Verified Successfully"
     *  )
     * )
  */

    public function verify2FA(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'code' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->two_factor_code !== $request->code) {
            return response()->json(['message' => 'Invalid verification code'], 401);
        }

        if (Carbon::now()->gt($user->two_factor_expires_at)) {
            return response()->json(['message' => 'Verification code expired'], 401);
        }


        $user->resetTwoFactorCode();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => '2FA Verified Successfully',
            'user' => $user,
            'token' => $token,
        ]);
    }




    /**
 * @OA\Post(
 *     path="/api/resetPassword",
 *     summary="Reset user password using token",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"token", "email", "password", "password_confirmation"},
 *             @OA\Property(property="token", type="string", example="abcdef123456"),
 *             @OA\Property(property="email", type="string", format="email", example="osayd@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Password has been reset"),
 *     @OA\Response(response=500, description="Reset failed")
 * )
 */

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







}
