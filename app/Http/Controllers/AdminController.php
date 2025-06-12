<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;

class AdminController extends Controller
{
   public function getAllUsersAdmin()
{
    $users = User::with('profile')->get();

    return response()->json([
        'status' => 200,
        'users' => $users
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
        $user->delete();
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
    public function getAllPropertiesAdmin(Request $request)
    {


        $query = Property::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('title', 'LIKE', '%' . $searchTerm . '%');
        }

        $properties = $query->with('images', 'amenities')->get();

        return response()->json($properties, 200);

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
    public function getPendingUsers(){
        $pendingUsers = User::where('status', 0)->get();
        return response()->json([
            'status' => 200,
            'pending_users' => $pendingUsers
        ]);
    }
    public function getPropertyById($id)
    {
       $property = Property::with('images', 'amenities')->find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }


        return response()->json($property, 200);
    }
    public function getStatisticsAdmin(){

        $totalProperties = Property::count();
        $totalAgents = User::where('role_id', 2)->count(); // Assuming role_id 2 is for agents
        $totalUsers = User::count();
        $totalReviews = Review::count();
        $avgRating = Review::avg('rating');




        return response()->json([
            'status' => 200,
            'total_properties' => $totalProperties,
            'total_agents' => $totalAgents,
            'total_users' => $totalUsers,
            'total_reviews' => $totalReviews,
            'average_rating' => $avgRating

        ]);

    }

}
