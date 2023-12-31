<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    protected $fillable = [
        'recipe_id', 'user_id', 'comment'
    ];
    use HasFactory;

    /*
     Retrieve a recipe that has certain comment
     */
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    /*
     Retrieve a user that owns this comment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
