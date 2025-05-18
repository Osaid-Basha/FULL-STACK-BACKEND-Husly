<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
/**
 * @OA\Get(
 *     path="/api/admin/users",
 *     summary="Get all users for admin",
 *     tags={"Admin"},
 *     @OA\Response(
 *         response=200,
 *         description="A list of all users",
 *     )
 * )
 */
    public function getAllUsersAdmin(){

        $users=User::all();
        return response()->json([
            'status'=>200,
            'users'=>$users
        ]);
    }
/**
 * @OA\Put(
 *    path="/api/admin/users/approve/{id}",
 *   summary="Approve a user request",
 *  tags={"Admin"},
 * @OA\Response(
 *         response=200,
 *         description="A list of all users",
 *     )
 * )
 */
    public function ApproveUserRequest($id){
        $user=User::find($id);
        if($user){
            $user->update(['status'=> 1]);
            $user->save();
            return response()->json([
                'status'=>200,
                'message'=>'User request approved successfully'
            ]);
        }else{
            return response()->json([
                'status'=>404,
                'message'=>'User not found'
            ]);
        }
    }
/**
 * @OA\Put(
 *    path="/api/admin/users/reject/{id}",
 *   summary="Reject a user request",
 *  tags={"Admin"},
 * @OA\Response(
 *         response=200,
 *         description="A list of all users",
 *     )
 * )
 */
    public function RejectUserRequest($id){
        $user=User::find($id);
        if($user){
            $user->update(['status'=>0]);
            $user->save();
            return response()->json([
                'status'=>200,
                'message'=>'User request rejected successfully'
            ]);
        }else{
            return response()->json([
                'status'=>404,
                'message'=>'User not found'
            ]);
        }
    }
/**
 * @OA\Get(
 *     path="/api/admin/users/search/{keyword}",
 *     summary="Search users by keyword",
 *     tags={"Admin"},
 *
 *   @OA\Response(
 *         response=200,
 *         description="A list of all users",
 *     )
 * )
 */
    public function SearchUserRequest($keyword){
        $users=User::where('first_name','LIKE','%'.$keyword.'%')
        ->orWhere('last_name','LIKE','%'.$keyword.'%')
        ->orWhere('email','LIKE','%'.$keyword.'%')
        ->get();
        return response()->json([
            'status'=>200,
            'users'=>$users
        ]);
    }

     /**
     *
     *  @OA\Post(
     *    path="/api/admin/users",
     *
     *  summary="Add a new user",
     *  tags={"Admin"},

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

    public function AddUserAdmin(Request $request)
    {
        $user = User::create($request->all());
        return response()->json([
            'status' => 200,
            'message' => 'User added successfully',
            'user' => $user

        ]);



    }
    /**
     * @OA\Delete(
     *     path="/api/admin/users/{id}",
     *     summary="Delete a user by ID",
     *     tags={"Admin"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully"
     *     )
     * )
     */
    public function DeleteUserAdmin($id){
        $user=User::find($id);
        if($user){
            $user->delete();
            return response()->json([
                'status'=>200,
                'message'=>'User deleted successfully'
            ]);
        }else{
            return response()->json([
                'status'=>404,
                'message'=>'User not found'
            ]);
        }
    }

}
