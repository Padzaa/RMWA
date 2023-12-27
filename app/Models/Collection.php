<?php

namespace App\Models;

use App\Traits\Chart;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory, Chart;

    protected $fillable = ['name', 'user_id'];

    /**
     * Retrieve every recipe that has certain collection
     */
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'collection_recipes');
    }

    /**
     * Retrieve the user that owns this collection
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
