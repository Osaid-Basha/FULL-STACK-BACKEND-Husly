<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class property_type extends Model
{
    //
    protected $fillable = [
        'type',
    ];
    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
