<?php

namespace App\Http\Controllers;
use App\Models\Favorites;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GeneralStatisticsController extends Controller
{
    public function getGeneralStats(): JsonResponse
    {
        $totalProperties = Property::count();
        $totalAgents = User::where('role_id', 2)->count();
        $totalCities = DB::table('properties')->distinct('city')->count('city');

        return response()->json([
            'total_properties' => $totalProperties,
            'total_agents' => $totalAgents,
            'total_cities' => $totalCities,

        ]);
    }
}
