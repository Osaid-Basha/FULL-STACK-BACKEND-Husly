<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class review extends Model
{
    protected $fillable = ['title', 'content','buying_id'];
    public function buy()
    {
        return $this->hasOne(BuyingRequest::class);
    }
    public function reply()
    {
        return $this->hasOne(Replay::class);
    }


}
