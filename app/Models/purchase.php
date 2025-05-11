<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class purchase extends Model
{
    protected $fillable = ['description', 'time','user_id'];
    public function property()
    {
        return $this->hasMany(property::class);
    }
    public function user()
    {
        return $this->hasOne(User::class);
    }
}

