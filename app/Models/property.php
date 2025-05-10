<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class property extends Model
{
    protected $fillable = [
        'address',
        'city',
        'title',
        'landArea',
        'price',
        'bedroom',
        'bathroom',
        'parking',
        'longDescreption',
        'shortDescreption',
        'constructionArea',
        'livingArea',
    ];
}
