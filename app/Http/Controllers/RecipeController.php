<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Collection;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Review;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filteredRecipes = Recipe::forUser()
            ->Filter($request)
            ->paginate(10);
//dd($filteredRecipes->toSql());
        return Inertia::render('Recipe/All', [
            "title" => "Recipes",
            'recipes' => $filteredRecipes,
            'categories' => Category::all(),
            'ingredients' => Ingredient::all(),
            'filterData' => $request->query->all(),
            'collections' => Auth::user()->collections()->get()

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return Inertia::render('Recipe/Recipe_Create', [
            "ingredients" => Ingredient::all(),
            "categories" => Category::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecipeRequest $request)
    {

        $ingredients = collect($request->ingredients)->pluck("id");
        $categories = collect($request->categories)->pluck("id");

        $recipe = Recipe::create([
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'user_id' => Auth::user()->id,
            'is_favorite' => +$request->favorite,
            'is_public' => +$request->public
        ]);
        $recipe->ingredients()->attach($ingredients);
        $recipe->categories()->attach($categories);
        return Inertia::location('/recipe');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        $review=$reviews=$users=$shared_to=$is_liked = null;
        $recipe = $recipe->load('user');
        if(Auth::user()){
            $shared_to =  $recipe->shared()->get();
            $review = $recipe->reviews()->where('user_id', Auth::user()->id)->first();
            $reviews = $recipe->reviews()->where("user_id", "!=", Auth::user()->id)->get();
            $is_liked = $recipe->likes()->where('user_id', Auth::user()->id)->count();
            $users = User::all()->except(Auth::user()->id);
        }
        $this->authorize("view", $recipe);
//        $shared_to = Auth::user() ? $recipe->shared()->get() : null;
//
//        $review = Auth::user() ? $recipe->reviews()->where('user_id', Auth::user()->id)->first() : null;
        $average = round($recipe->reviews()->avg("rating", 2), 2);
//        $reviews = Auth::user() ? $recipe->reviews()->where("user_id", "!=", Auth::user()->id)->get() : $recipe->reviews()->get();

        return Inertia::render('Recipe/Show',
            [
                "recipe" => $recipe,
                "ingredients" => $recipe->ingredients,
                "review" => $review ? $review : null,
                "average" => $average ? $average : "No Rating Yet",
                "reviews" => $reviews ? $reviews : [],
                "users" => $users ? $users : User::all(),
                "shared_to" => $shared_to ? $shared_to : null,
                "comments" => $recipe->comments()->with('user')->get(),
                "is_liked" => $is_liked ? $is_liked : false,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        $this->authorize('update', $recipe);

        return Inertia::render('Recipe/Recipe_Edit', [
            "recipe" => $recipe,
            "recipe_ingredients" => $recipe->ingredients,
            "recipe_categories" => $recipe->categories,
            "ingredients" => Ingredient::all(),
            "categories" => Category::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecipeRequest $request, Recipe $recipe)
    {

        $this->authorize('update', $recipe);

        $ingredients = collect($request->ingredients)->pluck("id");
        $categories = collect($request->categories)->pluck("id");

        $recipe->title = $request->title;
        $recipe->description = $request->description;
        $recipe->instructions = $request->instructions;
        $recipe->is_favorite = $request->favorite;
        $recipe->is_public = $request->public;
        $recipe->ingredients()->sync($ingredients);
        $recipe->categories()->sync($categories);
        $recipe->save();

        return Inertia::location('/recipe');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {

        $this->authorize('delete', $recipe);
        $recipe->ingredients()->detach();
        $recipe->categories()->detach();

        $recipe->delete();
        return redirect()->back();
    }

    /*
    Makes a recipe favorite to a user
   */
    public function favorite(Recipe $recipe, Request $request)
    {


        $this->authorize('update', $recipe);
        $recipe->is_favorite = !$recipe->is_favorite;
        $recipe->save();
        return redirect()->back();


    }
    /*
        Creates a review(rate the recipe)
       */
    public function rate(Recipe $recipe, Request $request)
    {

        $this->authorize('view', $recipe);
        $rating = $recipe->reviews()->where("user_id", Auth::user()->id)->first();

        $request->request = $request->validate([
            "rating" => ['required', 'integer', 'max:5', 'min:1'],
            "comment" => ['required', 'string', 'max:500', 'min:1']
        ],
            [
                "rating.required" => "Rating is required!",
                "comment.required" => "Comment is required!",
            ]);
        if ($rating) {
            $rating->rating = $request->rating;
            $rating->message = $request->comment;
            $rating->save();
        }
        if ($rating === null) {
            Review::create([
                "rating" => $request->rating,
                "message" => $request->comment,
                "user_id" => Auth::user()->id,
                "recipe_id" => $recipe->id
            ]);
        }

        return redirect()->back();


    }

    /*
       Share a recipe with other users
       */
    public function share(Recipe $recipe, Request $request)
    {
        $this->authorize('update', $recipe);

        $userIDs = collect($request->users)->pluck("id");

        $recipe->shared()->sync($userIDs);

        return redirect()->back();
    }

    /*
     Adds a comment for the recipe
    */
    public function comment(Recipe $recipe, Request $request)
    {

        $this->authorize('view', $recipe);

        $request->request = $request->validate([
            "ccomment" => ['required', 'string', 'max:500', 'min:1']
        ],
            [
                "ccomment.required" => "Comment is required!",
            ]);

        $recipe->comments()->create([
            "comment" => $request->ccomment,
            "user_id" => Auth::user()->id,
        ]);

        return redirect()->back();
    }

    /*
     Likes the recipe for a user
    */
    public function like(Recipe $recipe)
    {

        $this->authorize('view', $recipe);

        $recipe->likes()->where('user_id', Auth::user()->id)->count() ?
            $recipe->likes()->detach(Auth::user()->id)
            :
            $recipe->likes()->attach(Auth::user()->id);


        return redirect()->back();
    }

    /*
     Goes to favorites page
     */
    public function favorites()
    {
        $favorites = Auth::user()->favorites();
        return Inertia::render('User/Favorites', [
            "recipes" => $favorites
        ]);
    }

    /*
     //Public page of the site, so mainly guest can access
     */
    public function public(Request $request){
        $filteredRecipes = Recipe::query()->Public()->Filter($request)->paginate(10);

        return Inertia::render('Recipe/All', [
            "title" => "Public Recipes",
            "recipes" => $filteredRecipes,
            'categories' => Category::all(),
            'ingredients' => Ingredient::all(),
            'filterData' => $request->query->all(),
            'collections' => Auth::user() ? Auth::user()->collections()->get() : null

        ]);
    }

}
