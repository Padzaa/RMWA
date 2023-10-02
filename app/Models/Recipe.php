<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Recipe extends Model
{
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class,'recipe_ingredients');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class,'recipe_categories');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function shared(){
        return $this->belongsToMany(User::class,"shared_recipes",'recipe_id','user_shared_to');
    }

    use HasFactory;



protected $fillable = ['title', 'description','instructions', 'user_id','is_favorite'];
}
