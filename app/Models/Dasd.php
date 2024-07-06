<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Traits\DasdRelationTrait;

class Dasd extends Model
{
    use HasFactory,DasdRelationTrait;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $guarded = [];
}
