<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{

    public function getAllPurchases()
    {
        $userid=Auth::user();
        $purchases = purchase::with('user', 'properties')->where('user_id', $userid->id)->get();
        return response()->json($purchases);
    }


   public function searchPurchase($keyword)
{
    $userId = Auth::id();

    $purchases = purchase::with('user', 'properties')
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

