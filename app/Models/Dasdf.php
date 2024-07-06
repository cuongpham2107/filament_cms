<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Traits\DasdfRelationTrait;

class Dasdf extends Model
{
    use HasFactory,DasdfRelationTrait;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $guarded = [];
}
