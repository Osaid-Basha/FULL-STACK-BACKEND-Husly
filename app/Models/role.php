<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class role extends Model
{
    //
    protected $fillable = [

        'id',
        'type',


    ];
  public function users()
{
    return $this->hasMany(User::class);
}

}
