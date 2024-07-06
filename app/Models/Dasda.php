<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Traits\DasdaRelationTrait;

class Dasda extends Model
{
    use HasFactory,DasdaRelationTrait;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $guarded = [];
}
