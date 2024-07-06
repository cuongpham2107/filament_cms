<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Traits\SadasdRelationTrait;

class Sadasd extends Model
{
    use HasFactory,SadasdRelationTrait;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $guarded = [];
}
