<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

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

    /**
     * Retrieve statistics of new collections for each month
     */
    public static function monthlyCollections()
    {
        return self::selectRaw('MONTH(created_at) as Month, COUNT(id) as Count')
            ->whereYear('created_at', now()->year)
            ->groupBy('Month');
    }
}
