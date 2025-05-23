<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{


    public function index(Request $request)
    {

        $query = Property::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');

            $query->where('title', 'LIKE', '%' . $searchTerm . '%');
        }

        $properties = $query->get();

        return response()->json($properties, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'landArea' => 'required|numeric',
            'price' => 'required|numeric',
            'bedroom' => 'required|integer',
            'bathroom' => 'required|integer',
            'parking' => 'required|integer',
            'longDescreption' => 'required|string',
            'shortDescreption' => 'required|string',
            'constructionArea' => 'required|numeric',
            'livingArea' => 'required|numeric',
            'property_listing_id' => 'required|exists:listing_types,id',
            'property_type_id' => 'required|exists:property_types,id',
            'user_id' => 'required|exists:users,id',
            'purchase_id' => 'required|exists:purchases,id',
        ]);

        $property = Property::create($data);
        return response()->json($property, 201);
    }


    public function show($id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }
        return response()->json($property, 200);
    }


    public function update(Request $request, $id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $data = $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'landArea' => 'required|numeric',
            'price' => 'required|numeric',
            'bedroom' => 'required|integer',
            'bathroom' => 'required|integer',
            'parking' => 'required|integer',
            'longDescreption' => 'required|string',
            'shortDescreption' => 'required|string',
            'constructionArea' => 'required|numeric',
            'livingArea' => 'required|numeric',
            'property_listing_id' => 'required|exists:listing_types,id',
            'property_type_id' => 'required|exists:property_types,id',
            'user_id' => 'required|exists:users,id',
            'purchase_id' => 'required|exists:purchases,id',
        ]);

        $property->update($data);
        return response()->json($property, 200);
    }



    public function destroy($id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $property->delete();
        return response()->json(['message' => 'Property deleted successfully'], 200);
    }


    public function getAmenities($propertyId)
    {
        $property = Property::with('amenity')->find($propertyId);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return response()->json($property->amenity->pluck('name'), 200);
    }


}
