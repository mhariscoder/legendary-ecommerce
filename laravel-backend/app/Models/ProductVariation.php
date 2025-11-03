<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'price', 'stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeVariations()
    {
        return $this->belongsToMany(AttributeVariation::class, 'product_variation_attributes', 'product_variation_id', 'attribute_variation_id');
    }
}
