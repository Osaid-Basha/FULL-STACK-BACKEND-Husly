<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuyingRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
class BuyingRequestController extends Controller
{
    //
    public function confirm($id)
{
    $buyingRequest = BuyingRequest::where('id', $id)
        ->where('user_id', Auth::id())
        ->where('status', false)
        ->first();

    if (!$buyingRequest) {
        return response()->json([
            'status' => 404,
            'message' => 'Buying request not found or already confirmed.'
        ]);
    }

    $buyingRequest->status = true;
    $buyingRequest->type = 'confirmed';
    $buyingRequest->save();
    $agentId = $buyingRequest->property->user_id;

Notification::sendToUser(
    $agentId,
    'purchase_confirmed',
    "The buyer " . Auth::user()->first_name . " has confirmed the purchase of your property: '{$buyingRequest->property->title}'."

);


    return response()->json([
        'status' => 200,
        'message' => 'Buying request confirmed successfully.',
        'buying_request' => $buyingRequest
    ]);
}

}
