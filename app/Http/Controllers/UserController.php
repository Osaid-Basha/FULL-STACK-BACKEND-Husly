<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"firstname", "lastname", "email", "password", "confirm_password", "user_type"},
     *             @OA\Property(property="firstname", type="string"),
     *             @OA\Property(property="lastname", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="confirm_password", type="string"),
     *             @OA\Property(property="user_type", type="string", enum={"buyer", "seller", "admin"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="User registered successfully")
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|same:confirm_password',
            'confirm_password' => 'required|string|min:8|same:password',
            'user_type' => 'required|string|in:buyer,seller,admin',
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type
        ]);

        return response()->json([
            'message' => 'User Register successfully',
            'user' => $user
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
     *             required={"email", "password", "user_type"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="user_type", type="string", enum={"Buyer", "Agent", "Admin"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Login successfully"),
     *     @OA\Response(response=403, description="User type mismatch"),
     *     @OA\Response(response=4001, description="Incorrect email or password")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'user_type' => 'required|string|in:Buyer,Agent,Admin',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'incorrect email or password'], 4001);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('authToken')->plainTextToken;

        if ($user->user_type !== $request->user_type) {
            return response()->json(['message' => 'User type mismatch'], 403);
        }

        return response()->json([
            'message' => 'Login successfully',
            'user' => $user,
            'token' => $token,
            'user_type' => $user->user_type
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the authenticated user",
     *     tags={"Authentication"},
     *     @OA\Response(response=200, description="Logout successfully")
     * )
     */
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

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json([
                'message' => 'Reset link sent to your email.'
            ])
            : response()->json(['message' => 'Unable to send reset link.'], 500);
    }

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

        // تحقق ناجح
        $user->resetTwoFactorCode();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => '2FA Verified Successfully',
            'user' => $user,
            'token' => $token,
        ]);
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












    //
    /**
 * @OA\Get(
 *     path="/api/users",
 *     summary="Get all users",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="A list of users"
 *     )
 * )
 */
   public function getallUsers()
   {
       // Your logic to get all users
        $users = User::all();
        return response()->json($users);

   }
/**
 * @OA\Get(
 *    path="/api/users/{id}",
 *   summary="Get a user by ID",
 *   tags={"Users"},
 *  @OA\Parameter(
 *     name="id",
 *    in="path",
 *   required=true,
 *   description="ID of the user to get",
 *  @OA\Schema(
 *        type="integer"
 *   )
 *
 * ),
 * @OA\Response(
 *    response=200,
 *   description="User details",
 *  @OA\JsonContent(
 *        type="object",
 *       @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *    @OA\Property(property="created_at", type="string", format="date-time"),
 *
 * @OA\Property(property="updated_at", type="string", format="date-time")
 *   )
 *  )
 * )
 */
   public function getUserById($id)
   {
       // Your logic to get a user by ID
       $user = User::find($id);
       if ($user) {
           return response()->json($user);
       } else {
           return response()->json(['message' => 'User not found'], 404);
       }

   }
    /**
     *
     *  @OA\Post(
     *    path="/api/users",
     *
     *  summary="Create a new user",
     * tags={"Users"},
     * @OA\RequestBody(
     *    required=true,
     *   @OA\JsonContent(
     *       required={"name", "email"},
     *      @OA\Property(property="first_name", type="string"),
     *     @OA\Property(property="last_name", type="string"),
     *    @OA\Property(property="email", type="string"),
     *   @OA\Property(property="password", type="string"),
     *
     *   )
     * ),
     * @OA\Response(
     *   response=201,
     *  description="User created successfully",
     * @OA\JsonContent(
     *       type="object",
     *      @OA\Property(property="first_name", type="string"),
     *     @OA\Property(property="last_name", type="string"),
     *     @OA\Property(property="email", type="string"),
     *    @OA\Property(property="password", type="string"),
     *
     *   )
     * )
     * )
     */
   public function createUser(Request $request)
   {
       // Your logic to create a new user
       $user = User::create($request->all());
       return response()->json($user, 201);
   }


/**
 *  @OA\Put(
 *   path="/api/users/{id}",
 *  summary="Update a user by ID",
 * tags={"Users"},
 * @OA\Parameter(
 *    name="id",
 *   in="path",
 *  required=true,
 * description="ID of the user to update",
 * @OA\Schema(
 *       type="integer"
 *  )
 * ),
 * @OA\RequestBody(
 *  required=true,
 * @OA\JsonContent(
 *      required={"name", "email"},
 *     @OA\Property(property="name", type="string"),
 *
 *
 *  @OA\Property(property="email", type="string"),
 * @OA\Property(property="password", type="string"),
 *
 * )
 * ),
 * @OA\Response(
 *  response=200,
 * description="User updated successfully",
 * @OA\JsonContent(
 *      type="object",
 *
 *  @OA\Property(property="id", type="integer"),
 * @OA\Property(property="name", type="string"),
 *
 *  @OA\Property(property="email", type="string"),
 * @OA\Property(property="created_at", type="string", format="date-time"),
 * @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * )
 *
 *
 *
 *  )
 */
   public function updateUser(Request $request, $id)
   {
       // Your logic to update a user by ID
       $user = User::find($id);
       if ($user) {
           $user->update($request->all());
           return response()->json($user);
       } else {
           return response()->json(['message' => 'User not found'], 404);
       }
   }
   /**
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     summary="Delete a user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user to delete",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully"
 *     )
 * )
 */
   public function deleteUser($id)
   {
       // Your logic
         $user = User::find($id);
         if ($user) {
              $user->delete();
              return response()->json(['message' => 'User deleted successfully']);
            } else {
              return response()->json(['message' => 'User not found'], 404);
            }
   }
      /**
 * @OA\Get(
 *     path="api/users/{keyword}",
 *     summary="Get all users",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="A list of users"
 *     )
 * )
 */
    public function searchUsers($keyword)
    {
        // Your logic to search users by keyword
        $users = User::where('name', 'like', '%' . $keyword . '%')
            ->orWhere('email', 'like', '%' . $keyword . '%')
            ->get();
        return response()->json($users);
    }

}


