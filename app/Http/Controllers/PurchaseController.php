<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/purchases",
     *     summary="Get all purchases",
     *     tags={"Purchases"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all purchases"
     *     )
     * )
     */
    public function getAllPurchases(): JsonResponse
    {
        $purchases = Purchase::with('user', 'property')->get();
        return response()->json($purchases);
    }

    /**
     * Get a purchase by ID.
     *
     * @OA\Get(
     *     path="/api/purchases/{id}",
     *     summary="Get purchase by ID",
     *     tags={"Purchases"},
     *     @OA\Parameter(name="id", in="path", description="ID of the purchase", required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Purchase data retrieved successfully"),
     *     @OA\Response(response=404, description="Purchase not found")
     * )
     */
    public function getPurchaseById(int $id): JsonResponse
    {
        $purchase = Purchase::with('user', 'property')->find($id);

        if ($purchase) {
            return response()->json($purchase);
        }
        else{
            return response()->json(['message' => 'Purchase not found'], 404);
        }
    }



}

