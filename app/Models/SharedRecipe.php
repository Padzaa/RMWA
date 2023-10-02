<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedRecipe extends Model
{

    use HasFactory;

    protected $fillable = ['user_who_shared', 'user_shared_to'];

}
