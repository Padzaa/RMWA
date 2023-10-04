<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    public function recipes(){
        return $this->belongsToMany(Recipe::class,'collection_recipes');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }

    use HasFactory;
    protected $fillable = ['name','user_id'];
}