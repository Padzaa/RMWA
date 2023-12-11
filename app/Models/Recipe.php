<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'instructions', 'user_id', 'is_favorite', 'is_public'];

    /**
     * Retrieve every ingredient that is associated to certain recipe
     */
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients');
    }

    /**
     * Retrieve every category that is associated to certain recipe
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'recipe_categories');
    }

    /**
     * Retrieve a user that owns this recipe
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retrieve every review that belongs to certain recipe
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Retrieves the user's review for a given recipe.
     */
    public function reviewForRecipeByUser($user)
    {
        return $this->reviews()->where('user_id', $user->id)->with('user')->first();
    }

    /**
     * Retrieve every record where a certain recipe appears to be shared
     */
    public function shared()
    {
        return $this->belongsToMany(User::class, "shared_recipes", 'recipe_id', 'user_shared_to')->withTimestamps();
    }

    /**
     * Retrieve every collection that is associated to certain recipe
     */
    public function collections()
    {
        return $this->belongsToMany(Collection::class, "collection_recipes");
    }

    /**
     * Retrieve every comment that belongs to certain recipe
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Retrieve every like that belongs to certain recipe
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, "likes", 'recipe_id', 'user_id');
    }

    /**
     * Retrieve every recipe that a certain user can access(His own Recipes and Recipes shared with him)
     */
    public function scopeForUser($query, $user)
    {
        return $query->whereHas('user', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->orWhereHas('shared', function ($query) use ($user) {
            $query->where('user_shared_to', $user->id);
        });
    }

    /**
     * Filtering through recipes
     */
    public function scopeFilterRecipes($query, $params)
    {

        $query
            ->search($params['search'])
            ->filterIngredients($params['ingredients'])
            ->filterCollections($params['collections'])
            ->filterCategories($params['categories'])
            ->filterByRating($params['r_from'], $params['r_to'])
            ->filterFavorites($params['favorites']);
    }

    /**
     * Filter recipes by search
     */
    public function scopeSearch($query, $requested_search)
    {
        $query->when($requested_search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%')
                    ->orWhere('instructions', 'like', '%' . $search . '%');
            });
        });
    }

    /**
     * Filter recipes by categories
     */
    public function scopeFilterCategories($query, $requested_categories)
    {
        $query->when($requested_categories, function ($query, $categories) {
            $query->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('category_id', $categories);
            });
        });
    }

    /**
     * Filter recipes by ingredients
     */
    public function scopeFilterIngredients($query, $requested_ingredients)
    {
        $query->when($requested_ingredients, function ($query, $ingredients) {
            $query->whereHas('ingredients', function ($query) use ($ingredients) {
                $query->whereIn('ingredient_id', $ingredients);
            });
        });
    }

    /**
     * Filter recipes by collections
     */
    public function scopeFilterCollections($query, $requested_collections)
    {
        if (Auth::user()) {
            $query->when($requested_collections, function ($query, $collections) {
                $query->whereHas('collections', function ($query) use ($collections) {
                    $query->whereIn('collection_id', $collections);
                });
            });
        }
    }

    /**
     * Filter recipes by rating
     */
    public function scopeFilterByRating($query, $rating_from, $rating_to)
    {
        $query->select('recipes.*', \DB::raw('COALESCE(ROUND(AVG(reviews.rating),2),0) as average_rating'), \DB::raw('COUNT(reviews.id) as review_count'))
            ->leftJoin('reviews', 'reviews.recipe_id', '=', 'recipes.id')
            ->groupBy('recipes.id');
        $from = ($rating_from > 0) && ($rating_from <= 5) ? $rating_from : "0.00";
        $to = ($rating_to <= 5) && ($rating_to > 0) ? $rating_to : "5.00";
        $query->havingBetween("average_rating", [$from, $to]);
    }

    /**
     * Check if a recipe is public
     */
    public function scopePublic($query)
    {
        $query->where("is_public", 1);
    }

    /**
     * Filter recipes if they are favorite to a user
     */
    public function scopeFilterFavorites($query, $is_favorite)
    {
        if (Auth::user() && $is_favorite == "true") {
            $query->where("recipes.is_favorite", 1)->where('recipes.user_id', Auth::user()->id);
        }
    }

    /**
     * Retrieve statistics of new recipes for each month
     */
    public static function monthlyRecipes()
    {
        return self::selectRaw('MONTH(created_at) as Month, COUNT(id) as Count')
            ->whereYear('created_at', now()->year)
            ->groupBy('Month');
    }

}
