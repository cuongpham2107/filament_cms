<?php

namespace App\Models\Traits;

trait ProductRelationTrait
{
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id', 'id');
    }
}
