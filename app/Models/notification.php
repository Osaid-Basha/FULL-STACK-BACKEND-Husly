<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    protected $fillable = ['message', 'user_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
