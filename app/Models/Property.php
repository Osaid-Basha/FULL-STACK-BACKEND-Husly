<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Property extends Model
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
        'available',

        'property_type_id',
        'property_listing_id',
        'user_id',

        'amenity_id'


    ];
    public function property_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(property_type::class);
    }
    public function listing_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(listing_type::class);
    }
    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(propertyImage::class);
    }



    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function favorites(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Favorites::class);
    }
    public function amenities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(amenity::class, 'property_amenities');
    }
    public function negotiations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Negotiation::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(
            Review::class,
            BuyingRequest::class,
            'property_id',   // Foreign key on buying_requests table
            'buying_id',     // Foreign key on reviews table
            'id',            // Local key on properties table
            'id'             // Local key on buying_requests table
        );
    }


}
