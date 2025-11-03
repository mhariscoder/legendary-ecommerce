<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductVariationAttribute extends Pivot
{
    protected $table = 'product_variation_attributes';

    protected $fillable = ['product_variation_id', 'attribute_variation_id'];
}
