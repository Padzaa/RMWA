<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;
    /**
     * Retrieve a user who followed.
     */
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * Retrieve a followed user.
     */
    public function followed(){
        return $this->belongsTo(User::class,'followed_id');
    }
}
