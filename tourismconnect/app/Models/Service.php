<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'city_id',
        'name',
        'description',
        'website',
        'contact',
        'address',
        'latitude',
        'longitude',
        'subscription_type',
        'subscription_end_date',
        'is_featured'
    ];

    protected $casts = [
        'subscription_end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function mainImage()
    {
        return $this->hasOne(ServiceImage::class)->where('is_main', true);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function views()
    {
        return $this->hasMany(ServiceView::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function isSubscriptionValid()
    {
        return $this->subscription_end_date === null || $this->subscription_end_date->isFuture();
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }
}