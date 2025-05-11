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

        'property_type_id',
        'user_id',
        'purchase_id',
        'amenity_id'


    ];
    public function property_type()
    {
        return $this->belongsTo(property_type::class);
    }
    public function listing_type()
    {
        return $this->belongsTo(listing_type::class);
    }
    public function images()
    {
        return $this->hasMany(property_image::class);
    }

    public function purchase()
    {
        return $this->belongsTo(purchase::class);
    }
    public function buying()
    {
        return $this->hasMany(BuyingRequest::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorites::class);
    }
    public function amenity()
    {
        return $this->belongsToMany(amenity::class, 'property_amenity');
    }


}
