<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class listing_type extends Model
{
    //
    protected $fillable = [
        'type',
        'listing_type_id'
    ];
    public function listings()
    {
        return $this->hasMany(Property::class);
    }
}
