<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class review extends Model
{
    protected $fillable = ['title', 'content','buying_id','user_id','replay_id','rating'];
   public function buyingRequest()
{
    return $this->belongsTo(BuyingRequest::class, 'buying_id');
}

    public function replay()
{
    return $this->hasOne(Replay::class);
}

    public function user()
{
    return $this->belongsTo(User::class);
}



}
