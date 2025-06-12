<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negotiation extends Model
{
    //
   protected $fillable = [
    'user_id',
    'property_id',
    'proposed_price',
    'status',
    'phone',
    'email',
        'message'


];

   public function user()
{
    return $this->belongsTo(User::class);
}

public function property()
{
    return $this->belongsTo(Property::class);
}
public function buyingRequest()
{
    return $this->hasOne(BuyingRequest::class, 'negotiation_id');
}


}
