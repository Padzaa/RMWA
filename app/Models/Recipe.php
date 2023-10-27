<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'instructions', 'user_id', 'is_favorite', 'is_public'];

    /*
     Retrieve every ingredient that is associated to certain recipe
     */
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients');
    }

    /*
         Retrieve every category that is associated to certain recipe
         */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'recipe_categories');
    }

    /*
         Retrieve a user that owns this recipe
         */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
     Retrieve every review that belongs to certain recipe
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /*
     Retrieve every record where a certain recipe appears to be shared
     */
    public function shared()
    {
        return $this->belongsToMany(User::class, "shared_recipes", 'recipe_id', 'user_shared_to');
    }

    /*
     Retrieve every collection that is associated to certain recipe
     */
    public function collections()
    {
        return $this->belongsToMany(Collection::class, "collection_recipes");
    }

    /*
     Retrieve every comment that belongs to certain recipe
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /*
     Retrieve every like that belongs to certain recipe
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, "likes", 'recipe_id', 'user_id');
    }

    /*
    Retrieve every recipe that a certain user can access(His own Recipes and Recipes shared with him)
     */
    public function scopeForUser($query)
    {
        $user = Auth::user();
        if ($user) {
            return $query->whereHas('user', function ($query) use ($user) {
                $query->where('id', $user->id);
            })->orWhereHas('shared', function ($query) use ($user) {
                $query->where('user_shared_to', $user->id);
            });
        } else {
            return $query->get();
        }

    }

    /*
        Filtering through recipes
         */
    public function scopeFilter($query, $request)
    {
        $filters = $request->query();
        $order = $request->order !== null ? explode('-',$request->order) : ['created_at', 'desc'];
        $orderColumn = $order[0];
        $orderDirection = $order[1];
        $query->when($request->search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
                    ->orWhere('instructions', 'like', '%' . $search . '%');
            });
        })->when($request->ratings, function ($query, $ratings) use ($orderColumn, $orderDirection) {
            $query->select('recipes.*', \DB::raw('ROUND(AVG(reviews.rating),2) as average_rating'), \DB::raw('COUNT(reviews.id) as review_count'))
                ->leftJoin('reviews', 'reviews.recipe_id', '=', 'recipes.id')
                ->groupBy('recipes.id')
                ->havingRaw('FLOOR(AVG(reviews.rating)) IN (' . implode(',', $ratings) . ')')
                ->orderBy($orderColumn, $orderDirection);

        })->when($request->ratings == null, function ($query) use ($orderColumn, $orderDirection) {


            $query->select('recipes.*', \DB::raw('ROUND(AVG(reviews.rating),2) as average_rating'), \DB::raw('COUNT(reviews.id) as review_count'))
                ->leftJoin('reviews', 'reviews.recipe_id', '=', 'recipes.id')
                ->groupBy('recipes.id')
                ->orderBy($orderColumn, $orderDirection);

        })->when($request->ingredients, function ($query, $ingredients) {
            $query->whereHas('ingredients', function ($query) use ($ingredients) {
                $query->whereIn('ingredient_id', $ingredients);
            });
        })->when($request->collections, function ($query, $collections) {
            if (Auth::user()) {
                $query->whereHas('collections', function ($query) use ($collections) {
                    $query->whereIn('collection_id', $collections);
                });
            }
        })->when($request->categories, function ($query, $categories) {
            $query->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('category_id', $categories);
            });
        })->when($request->favorites, function ($query, $favorites) {
            if (Auth::user()) {
                $favorites === "true" ? $query->where("is_favorite", 1)->where("recipes.user_id", Auth::user()->id) : null;
            }
        });
    }

    /*
     * Check if a recipe is public
     * */
    public function scopePublic($query)
    {
        return $query->where("is_public", 1);
    }

}
