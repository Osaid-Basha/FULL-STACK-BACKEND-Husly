<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;

class AdminController extends Controller
{
    public function getAllUsersAdmin(){

        $users=User::all();
        return response()->json([
            'status'=>200,
            'users'=>$users
        ]);
    }
    public function ApproveUserRequest($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'status' => 404,
            'message' => 'User not found',
        ]);
    }

    $user->update(['status' => 1]);

    return response()->json([
        'status' => 200,
        'message' => 'User approved successfully',
        'user' => $user,
    ]);
    }
    public function RejectUserRequest($id)
{
    $user = User::find($id);

    if ($user) {
        $user->delete(); // حذف المستخدم
        return response()->json([
            'status' => 200,
            'message' => 'User request rejected and user deleted successfully'
        ]);
    } else {
        return response()->json([
            'status' => 404,
            'message' => 'User not found'
        ]);
    }
    }
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
    public function AddUserAdmin(Request $request)
    {
        $user = User::create($request->all());
        return response()->json([
            'status' => 200,
            'message' => 'User added successfully',
            'user' => $user

        ]);



    }
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
    public function getAllPropertiesAdmin()
    {
        $properties = Property::all();
        return response()->json([
            'status' => 200,
            'properties' => $properties
        ]);
    }
    public function DeletePropertyAdmin($id)
    {
        $property = Property::find($id);
        if ($property) {
            $property->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Property deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Property not found'
            ]);
        }
    }
    public function SearchPropertyRequest($keyword)
    {
        $properties = Property::where('title', 'LIKE', '%' . $keyword . '%')
            ->orWhere('description', 'LIKE', '%' . $keyword . '%')
            ->get();
        return response()->json([
            'status' => 200,
            'properties' => $properties
        ]);
    }

}
