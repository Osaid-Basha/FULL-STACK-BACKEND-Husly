<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class favorites extends Model
{
    //
    protected $fillable = [
        'available',
        'property_id',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function property()
    {
        return $this->belongsTo(property::class);
    }
}
