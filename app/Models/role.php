<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class role extends Model
{
    //
    protected $fillable = [

        'id',
        'type',
        'user_id'

    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_users');
    }
}
