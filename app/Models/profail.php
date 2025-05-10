<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class profail extends Model
{
    //
    protected $fillable = [
        'name',
        'imag_path',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'linkedin_url',
        'current_position',
        'phone',
        'location'
    ];
}
