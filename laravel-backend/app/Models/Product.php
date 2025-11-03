<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'stock', 'sku'];

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'product_variation_attributes', 'product_variation_id', 'attribute_variation_id');
    }
}
