<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyingRequest extends Model
{
    /** @use HasFactory<\Database\Factories\BuyingRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'status',
        'type',
        'property_id',
        'user_id',
        'negotiation_id'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function review()
    {
        return $this->hasOne(Review::class);
    }
    public function negotiation()
    {
        return $this->hasOne(Negotiation::class);
    }

}
