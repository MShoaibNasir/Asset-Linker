<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
      
     protected $fillable = [
        'user_id',
        'property_type',
        'size',
        'rent_sale',
        'price',
        'yards',
        'Location',
        'location',
        'category',
        'corner',
        'address',
        'main_features',
        'details',
        'open',
        'rooms',
        'area_unit',
        'furnished',
		'bedrooms',
		'bathrooms',
		'phase',
		'images',
		'created_at',
		'updated_at'
        
    ];
}
