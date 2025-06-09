<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\property;

class PropertyBuyerController extends Controller
{

  public function getAllProperties()
{
    $properties = Property::with([
        'images',
        'amenities',

        'listing_type',
        'property_type',

    ])->get();

    return response()->json($properties);
}



 public function search(Request $request)
{
    $query = Property::with([
        'images',
        'user',
        'property_type',
        'listing_type',
        'amenities'
    ]);

    if ($request->location) {
        $query->where('city', 'LIKE', "%{$request->location}%");
    }

    if ($request->type) {
        $query->whereHas('property_type', function ($q) use ($request) {
            $q->where('type', 'LIKE', "%{$request->type}%");
        });
    }

    if ($request->min_price) {
        $query->where('price', '>=', $request->min_price);
    }

    if ($request->max_price) {
        $query->where('price', '<=', $request->max_price);
    }

    if ($request->listing_type_id) {
        $query->where('property_listing_id', $request->listing_type_id);
    }

    if ($request->keyword) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'LIKE', "%{$request->keyword}%")
              ->orWhere('shortDescreption', 'LIKE', "%{$request->keyword}%");
        });
    }

    $results = $query->get(); // أو paginate(10)

    return response()->json([
        'success' => true,
        'data' => $results
    ]);
}



 public function show($id)
{
    $property = Property::with([
        'property_type',
        'listing_type',
        
        'images',
        'user.profile',
        'amenities',
        'reviews.user.profile',
        'reviews.replies'
    ])->find($id);

    if (!$property) {
        return response()->json(['message' => 'Property not found'], 404);
    }

    return response()->json($property);
}


}
