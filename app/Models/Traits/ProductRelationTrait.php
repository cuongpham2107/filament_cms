<?php

namespace App\Models\Traits;

trait ProductRelationTrait
{
    public function categories()
    {
        return $this->belongsToMany(\App\Models\PivotCategoryProduct::class, 'pivot_category_product', 'category_id', 'product_id');
    }
}
