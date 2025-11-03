<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeVariation extends Model
{
    use HasFactory;

    protected $fillable = ['product_attribute_id', 'value'];

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function productVariations()
    {
        return $this->belongsToMany(ProductVariation::class, 'product_variation_attributes', 'attribute_variation_id', 'product_variation_id');
    }
}
