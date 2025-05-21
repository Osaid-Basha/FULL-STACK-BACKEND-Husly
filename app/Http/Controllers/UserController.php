<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Models\User;
class UserController extends Controller
{
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
 * * @OA\Post(
 *    path="/api/users",
 *
 *  summary="Create a new user",
 * tags={"Users"},
 * @OA\RequestBody(
 *    required=true,
 *   @OA\JsonContent(
 *       required={"name", "email"},
 *      @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *    @OA\Property(property="password", type="string"),
 *
 *   )
 * ),
 * @OA\Response(
 *   response=201,
 *  description="User created successfully",
 * @OA\JsonContent(
 *       type="object",
 *      @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *    @OA\Property(property="email", type="string"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *  @OA\Property(property="updated_at", type="string", format="date-time")
 *  )
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
 * * @OA\Put(
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

