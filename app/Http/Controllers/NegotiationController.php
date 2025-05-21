<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negotiation;
use App\Models\BuyingRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NegotiationController extends Controller
{
    //
    public function propose(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'proposed_price' => 'required|numeric|min:1',
        ]);

        $negotiation = Negotiation::create([
            'user_id' => Auth::id(),
            'property_id' => $request->property_id,
            'proposed_price' => $request->proposed_price,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Negotiation submitted successfully.',
            'negotiation' => $negotiation
        ]);
    }
    public function acceptNegotiation($id)
    {
    $negotiation = Negotiation::find($id);

    if (!$negotiation) {
        return response()->json([
            'status' => 404,
            'message' => 'Negotiation not found',
        ]);
    }


    if ($negotiation->status === 'accepted') {
        return response()->json([
            'status' => 400,
            'message' => 'Negotiation already accepted',
        ]);
    }


    $negotiation->status = 'accepted';
    $negotiation->save();

    $buyingRequest = BuyingRequest::create([
        'user_id' => $negotiation->user_id,
        'property_id' => $negotiation->property_id,
        'status' => true,
        'type' => 'confirmed',
        'negotiation_id' => $negotiation->id,
    ]);

    return response()->json([
        'status' => 201,
        'message' => 'Negotiation accepted and converted to Buying Request.',
        'buying_request' => $buyingRequest
    ]);
    }
    public function rejectNegotiation($id){
    $negotiation = Negotiation::find($id);

    if (!$negotiation) {
        return response()->json([
            'status' => 404,
            'message' => 'Negotiation not found',
        ]);
    }

    if ($negotiation->status === 'rejected') {
        return response()->json([
            'status' => 400,
            'message' => 'Negotiation is already rejected',
        ]);
    }

    $negotiation->status = 'rejected';
    $negotiation->save();

    return response()->json([
        'status' => 200,
        'message' => 'Negotiation has been rejected successfully.',
        'negotiation' => $negotiation
    ]);
    }
    public function received()
{
    $agent = Auth::user();
    $negotiations = $agent->receivedNegotiations->load(['user', 'property']);

    return response()->json([
        'status' => 200,
        'negotiations' => $negotiations
    ]);
    }
}
