<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function recipe(){
    return $this->belongsTo(Recipe::class);
}
    public function user(){
        return $this->belongsTo(User::class);
    }
    protected $fillable = [
        'recipe_id','user_id', 'comment'
    ];
    use HasFactory;
}
