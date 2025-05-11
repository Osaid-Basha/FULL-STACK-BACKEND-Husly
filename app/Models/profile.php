<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class profile extends Model
{
     protected $fillable = [
        'name',
        'imag_path',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'linkedin_url',
        'current_position',
        'phone',
        'location',
        'user_id'
    ];
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
