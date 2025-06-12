<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuyingRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
class BuyingRequestController extends Controller
{
    //
   public function confirm($negotiationId)
{
    $buyingRequest = BuyingRequest::where('negotiation_id', $negotiationId)
        ->where('status', 0)
        ->first();

    if (!$buyingRequest) {
        return response()->json([
            'status' => 404,
            'message' => 'Buying request not found or already confirmed.'
        ]);
    }

    $buyingRequest->status = 1;
    $buyingRequest->type = 'confirmed';
    $buyingRequest->save();

    $buyerId = $buyingRequest->user_id;

    Notification::sendToUser(
        $buyerId,
        'purchase_confirmed',
        "Your purchase request for property '{$buyingRequest->property->title}' has been confirmed."
    );

    return response()->json([
        'status' => 200,
        'message' => 'Buying request confirmed successfully.',
        'buying_request' => $buyingRequest
    ]);
}


}
