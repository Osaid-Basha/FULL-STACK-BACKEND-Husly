<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negotiation;
use App\Models\BuyingRequest;
use App\Models\User;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
class NegotiationController extends Controller
{
    //
   public function propose(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'proposed_price' => 'required|numeric|min:1',
        ]);

        $property = Property::find($request->property_id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        $negotiation = Negotiation::create([
            'user_id' => Auth::id(),
            'agent_id' => $property->user_id,
            'property_id' => $request->property_id,
            'proposed_price' => $request->proposed_price,
            'status' => 'pending',
        ]);
        Notification::sendToUser(
            $property->user_id, // the agent (property owner)
            'negotiation_created',
            "You received a new negotiation request for your property '{$property->title}'."
        );


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

    // تحديث حالة التفاوض
    $negotiation->status = 'accepted';
    $negotiation->save();

    // حذف باقي التفاوضات لنفس العقار
    Negotiation::where('property_id', $negotiation->property_id)
        ->where('id', '!=', $negotiation->id)
        ->delete();

    $buyingRequest = BuyingRequest::create([
        'user_id' => $negotiation->user_id,
        'property_id' => $negotiation->property_id,
        'status' => false, // لما يؤكد الشراء بيصير true
        'type' => 'pending',
        'negotiation_id' => $negotiation->id,
    ]);
    Notification::sendToUser(
        $negotiation->user_id, // the buyer
        'negotiation_accepted',
        "Your negotiation for the property '{$negotiation->property->title}' has been accepted."
    );

    Notification::sendToUser(
        $negotiation->agent_id, // the agent
        'negotiation_confirmed',
        "You accepted a negotiation and created a pending buying request for '{$negotiation->property->title}'."
    );


    return response()->json([
        'status' => 201,
        'message' => 'Negotiation accepted and buying request created.',
        'buying_request' => $buyingRequest,
    ]);
}


    public function rejectNegotiation($id)
{
    $negotiation = Negotiation::find($id);

    if (!$negotiation) {
        return response()->json([
            'status' => 404,
            'message' => 'Negotiation not found',
        ]);
    }

    $negotiation->delete();
    Notification::sendToUser(
        $negotiation->user_id, // the buyer
        'negotiation_rejected',
        "Your negotiation for the property '{$negotiation->property->title}' was rejected."
    );



    return response()->json([
        'status' => 200,
        'message' => 'Negotiation has been rejected and deleted successfully.'
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
    public function deleteReceivedNegotiations()
{
    $agentId = Auth::id();

    $deleted = Negotiation::where('user_id', $agentId)->delete();

    return response()->json([
        'status' => 200,
        'message' => "$deleted negotiations deleted successfully."
    ]);
}

}
