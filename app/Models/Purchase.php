<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['description','user_id'];
    public function property()
    {
        return $this->hasMany(Property::class);
    }
    public function user()
    {
//        return $this->hasOne(User::class);
        return $this->belongsTo(User::class, 'user_id');
    }
}

