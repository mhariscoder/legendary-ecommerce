<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function variations()
    {
        return $this->hasMany(AttributeVariation::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_variation_attributes', 'attribute_variation_id', 'product_variation_id');
    }
}
