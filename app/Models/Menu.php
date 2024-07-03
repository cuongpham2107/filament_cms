<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;
    // use \Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;
     /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function casts(): array
    {
        return [
            'menu_items' => 'array',
        ];
    }
}
