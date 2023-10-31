<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Collection;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Review;
use App\Models\User;
use App\Models\UserLogin;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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

        return Inertia::render('Recipe/All', [
            "title" => "Recipes",
            'recipes' => $filteredRecipes,
            'categories' => Category::all(),
            'ingredients' => Ingredient::orderBy('name')->get(),
            'filtersData' => $request->query->all(),
            'collections' => Auth::user()->collections()->orderBy('name')->get()

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Recipe/Recipe_Create', [
            "ingredients" => Ingredient::orderBy('name')->get(),
            "categories" => Category::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecipeRequest $request)
    {

        try {
            $recipe = Recipe::create([
                'title' => $request->title,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'user_id' => Auth::user()->id,
                'is_favorite' => +$request->favorite,
                'is_public' => +$request->public,
            ]);
            $recipe->ingredients()->attach($request->ingredients);
            $recipe->categories()->attach($request->categories);

            session()->flash('alert', [
                'title' => 'Success!',
                'message' => 'Recipe created successfully.',
                'type' => 'success'
            ]);
            return redirect()->route('recipe.index');

        } catch (Exception $e) {
            session()->flash('alert', [
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return redirect()->route('recipe.index');
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        try {
            $this->authorize("view", $recipe);
            $review = $reviews = $users = $shared_to = $is_liked = null;
            $recipe->load('user');

            if (Auth::user()) {
                $shared_to = $recipe->shared()->get()->pluck("id");
                $review = $recipe->reviews()->where('user_id', Auth::user()->id)->first();
                $reviews = $recipe->reviews()->where("user_id", "!=", Auth::user()->id)->get();
                $is_liked = $recipe->likes()->where('user_id', Auth::user()->id)->count();
                $users = User::all()->except(Auth::user()->id);
            }
            $average = round($recipe->reviews()->avg("rating", 2), 2);

            return Inertia::render('Recipe/Show',
                [
                    "recipe" => $recipe,
                    "ingredients" => $recipe->ingredients,
                    "review" => $review ? $review : null,
                    "average" => $average ? $average : "No Rating Yet",
                    "reviews" => $reviews ? $reviews : [],
                    "users" => $users ? $users : User::all(),
                    "shared_to" => $shared_to ? $shared_to : null,
                    "comments" => $recipe->comments()->with('user')->orderBy('created_at', 'desc')->get(),
                    "is_liked" => $is_liked ? $is_liked : false,
                ]);
        } catch (Exception $e) {
            session()->flash('alert', [
                'title' => 'Recipe Error',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return redirect()->route('recipe.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        try {
            $this->authorize('update', $recipe);
            return Inertia::render('Recipe/Recipe_Edit', [
                "recipe" => $recipe,
                "recipe.ingredients" => $recipe->ingredients->pluck("id"),
                "recipe.categories" => $recipe->categories->pluck("id"),
                "ingredients" => Ingredient::orderBy('name')->get(),
                "categories" => Category::all()
            ]);
        } catch (Exception $e) {
            session()->flash('alert', [
                'title' => 'Recipe Error',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return redirect()->route('recipe.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecipeRequest $request, Recipe $recipe)
    {


        try {
            $this->authorize('update', $recipe);
            $recipe->title = $request->title;
            $recipe->description = $request->description;
            $recipe->instructions = $request->instructions;
            $recipe->is_favorite = $request->favorite;
            $recipe->is_public = $request->public;
            $recipe->ingredients()->sync($request->ingredients);
            $recipe->categories()->sync($request->categories);
            $recipe->save();

            session()->flash('alert', [
                'title' => 'Success!',
                'message' => 'Recipe updated successfully.',
                'type' => 'success'
            ]);
            return redirect()->route('recipe.show', $recipe);

        } catch (Exception $e) {
            session()->flash('alert', [
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return redirect()->route('recipe.show', $recipe);
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {

        try {
            $this->authorize('delete', $recipe);
            $collections = $recipe->collections()->withCount("recipes")->get();
            foreach ($collections as $collection) {
                if($collection->recipes_count == 1){
                    $collection->delete();
                }
            }
            $recipe->delete();

            return redirect()->back();
        } catch (Exception $e) {
            session()->flash('alert', [
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return redirect()->back();
        }

    }

    /*
    Makes a recipe favorite to a user
   */
    public function favorite(Recipe $recipe, Request $request)
    {
        try {
            $this->authorize('update', $recipe);
            $recipe->is_favorite = !$recipe->is_favorite;
            $recipe->save();
            return redirect()->back();
        } catch (Exception $e) {
            session()->flash('alert', [
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return redirect()->back();
        }
    }

    /*
        Creates a review(rate the recipe)
       */
    public function rate(Recipe $recipe, Request $request)
    {

        try {
            $this->authorize('view', $recipe);

            $rating = $recipe->reviews()->where("user_id", Auth::user()->id)->first();

            $request->request = $request->validate([
                "rating" => ['required', 'integer', 'max:5', 'min:1'],
                "msg" => ['required', 'string', 'max:500', 'min:1']
            ],
                [
                    "rating.required" => "Rating is required!",
                    "msg.required" => "Message is required!",
                ]);
            if ($rating) {
                $rating->rating = $request->rating;
                $rating->message = $request->msg;
                $rating->save();
            }
            if ($rating === null) {
                Review::create([
                    "rating" => $request->rating,
                    "message" => $request->msg,
                    "user_id" => Auth::user()->id,
                    "recipe_id" => $recipe->id
                ]);
            }
            session()->flash('alert', [
                'title' => 'Success!',
                'message' => 'Review added successfully.',
                'type' => 'success'
            ]);
            return Inertia::location('/recipe/' . $recipe->id);
        } catch (Exception $e) {
            session()->flash('alert', [
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return Inertia::location('/recipe/' . $recipe->id);
        }


    }

    /*
       Share a recipe with other users
     */
    public function share(Recipe $recipe, Request $request)
    {

        try {
            $this->authorize('update', $recipe);
            $recipe->shared()->sync($request->users);
            session()->flash('alert', [
                'title' => 'Success!',
                'message' => 'Recipe shared list adjusted successfully.',
                'type' => 'success'
            ]);
            return Inertia::location('/recipe/' . $recipe->id);
        } catch (Exception $e) {
            session()->flash('alert', [
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return Inertia::location('/recipe/' . $recipe->id);
        }

    }

    /*
     Adds a comment for the recipe
    */
    public function comment(Recipe $recipe, Request $request)
    {

        $request->request = $request->validate([
            "comment" => ['required', 'string', 'max:500', 'min:1']
        ], [
            "comment.required" => "Comment is required!",
        ]);
        $recipe->comments()->create([
            "comment" => $request->comment,
            "user_id" => Auth::user()->id,
        ]);
        try {
            $this->authorize('view', $recipe);


            session()->flash('alert', [
                'title' => 'Success!',
                'message' => 'Comment added successfully.',
                'type' => 'success'
            ]);
            return Inertia::location('/recipe/' . $recipe->id);
        } catch (Exception $e) {
            session()->flash('alert', [
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return Inertia::location('/recipe/' . $recipe->id);
        }
    }

    /*
     Likes the recipe for a user
    */
    public function like(Recipe $recipe)
    {
        try {
            $this->authorize('view', $recipe);

            $recipe->likes()->where('user_id', Auth::user()->id)->count() ?
                $recipe->likes()->detach(Auth::user()->id)
                :
                $recipe->likes()->attach(Auth::user()->id);
            return redirect()->back();
        } catch (Exception $e) {
            session()->flash('alert', [
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return redirect()->back();
        }

    }

    /*
     Goes to favorites page
     */
    public function favorites()
    {
        //Function favorites() that is called here accepts one argument $perPage which has a default value of 10
        //$perPage is the number of recipes per page
        return Inertia::render('User/Favorites', [
            "recipes" => Auth::user()->favorites(),
        ]);
    }

    /*
     Public page of the site, so mainly guest can access
     */
    public function public(Request $request)
    {
        $filteredRecipes = Recipe::query()->Public()->Filter($request)->paginate(10);

        return Inertia::render('Recipe/All', [
            "title" => "Public Recipes",
            "recipes" => $filteredRecipes,
            'categories' => Category::all(),
            'ingredients' => Ingredient::orderBy('name')->get(),
            'filtersData' => $request->query->all(),
            'collections' => Auth::user() ? Auth::user()->collections()->orderBy('name')->get() : null

        ]);
    }

    public function notifications(){
        $last = Auth::user()->logins()->orderBy('created_at',"desc")->limit(2)->get()->last();
        $last = UserLogin::find($last->id);
        $last->updated_at = Carbon::now();
        $last->save();
        return Inertia::location(URL::previous());
    }
}
