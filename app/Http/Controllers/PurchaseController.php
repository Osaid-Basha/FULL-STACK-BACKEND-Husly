<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\purchase;
use App\Models\BuyingRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{

    public function getAllPurchases()
{
    $userId = Auth::id();

    $requests = BuyingRequest::with(['property', 'property.listing_type'])
        ->where('user_id', $userId)
        ->where('status', true)
        ->get();

    return response()->json([
        'status' => 200,
        'purchases' => $requests,
    ]);
}



   public function searchPurchase($keyword)
{
    $userId = Auth::id();

    $purchases = Purchase::with('user', 'properties')
        ->where('user_id', $userId)
        ->where(function ($query) use ($keyword) {
            $query->where('description', 'like', "%$keyword%")
                ->orWhereHas('property', function ($query) use ($keyword) {
                    $query->where('title', 'like', "%$keyword%");
                });
        })
        ->get();

    return response()->json([
        'status' => 200,
        'purchases' => $purchases
    ]);
}





}

