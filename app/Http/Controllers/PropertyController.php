<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class PropertyController extends Controller
{
    //
    /**
     * @OA\Get(
     *     path="/api/properties",
     *     summary="Get all properties",
     *     tags={"Properties"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of properties"
     *     )
     * )
     */
    public function getAllProperties(){
        // Your logic to get all properties
        $properties = Property::all();
        return response()->json($properties);
    }
}
