<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $table = 'product_attribute_values';

    protected $fillable = [
        'product_attribute_id',
        'value',
    ];

    /**
     * Each value belongs to an attribute (e.g., Red belongs to Color).
     */
    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    /**
     * Each value can belong to many product variations.
     */
    public function variations()
    {
        return $this->belongsToMany(
            ProductVariation::class,
            'product_variation_attributes',
            'product_attribute_value_id',
            'product_variation_id'
        )->withTimestamps();
    }
}
