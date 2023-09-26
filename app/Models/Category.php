<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function recipes(){
        return $this->belongsToMany(Recipe::class,'recipe_categories');
    }
    use HasFactory;
}
