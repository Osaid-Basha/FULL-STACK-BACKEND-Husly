<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class amenity extends Model
{
    protected $fillable = [
        'name',
        'property_id'

    ];
    public function properties(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_amenities');
    }

}
