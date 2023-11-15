<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', "rating", "message", "recipe_id"];

    /*
     Retrieve a recipe that is associated to certain review
     */
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    /*
     *  Retrieve a user that owns this review
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
