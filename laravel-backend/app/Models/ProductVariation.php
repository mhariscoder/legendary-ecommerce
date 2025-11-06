<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $table = 'product_variations';

    protected $fillable = [
        'product_id',
        'price',
        'stock',
    ];

    /**
     * Each variation belongs to a product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * A variation can have many attribute values (via product_variation_attributes).
     */
    public function attributes()
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_variation_attributes',
            'product_variation_id',
            'product_attribute_value_id'
        )->withTimestamps();
    }
}
