<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class postFavourite extends Model
{
    use HasFactory;
    protected $table = 'post_favourite';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id','post_id'];

}