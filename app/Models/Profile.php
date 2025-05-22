<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Profile extends Model
{
     protected $fillable = [

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
    return $this->belongsTo(User::class, 'user_id');
}

}
