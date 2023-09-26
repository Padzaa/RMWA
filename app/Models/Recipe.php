<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class,'recipe_ingredients');
    }
    public function categories()
    {
        return $this->belongsToMany(Recipe::class,'recipe_categories');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    use HasFactory;
    protected $fillable = ['title', 'description','instructions', 'user_id','is_favorite'];
}
