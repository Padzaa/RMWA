<?php

namespace App\Models;

use App\Traits\Chart;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
<<<<<<< HEAD
    use HasFactory, Chart;
=======
    use HasFactory,Chart;
>>>>>>> a309665 (Fixed validating file and msg content issue,refactoring functions)

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
