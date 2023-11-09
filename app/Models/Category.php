<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /*
    Retrieve every recipe that has certain category
    */
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_categories');
    }


}
