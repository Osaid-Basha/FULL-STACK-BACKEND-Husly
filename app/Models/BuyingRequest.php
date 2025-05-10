<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyingRequest extends Model
{
    /** @use HasFactory<\Database\Factories\BuyingRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'status',
        'type',
        'date',
    ];
}
