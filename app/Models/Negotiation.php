<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negotiation extends Model
{
    //
    protected $fillable = [
        'status',
        'type',
        'property_id',
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
    return $this->hasOne(BuyingRequest::class);
}

}
