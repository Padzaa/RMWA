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
            ->Filter($request);
        $filteredRecipes = $filteredRecipes->paginate(10);

        return Inertia::render('Recipe/All', [
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
        $ingredients = [];
        $categories = [];

        foreach ($request->ingredients as $ingredient) {
            if (isset($ingredient["id"])) {
                $ingredients[] = $ingredient["id"];
            }
        }
        foreach ($request->categories as $category) {
            if (isset($category["id"])) {
                $categories[] = $category["id"];
            }
        }

        $recipe = Recipe::create([
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'user_id' => Auth::user()->id,
            'is_favorite' => +$request->favorite
        ]);
        $recipe->ingredients()->attach($ingredients);
        $recipe->categories()->attach($categories);
        return Inertia::location('/recipe');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $recipe = Recipe::with('user')->findOrFail($id);
        $this->authorize("view", $recipe);
        $shared_to = $recipe->shared()->get();

        $review = $recipe->reviews()->where('user_id', Auth::user()->id)->first();
        $average = round($recipe->reviews()->avg("rating", 2), 2);
        $reviews = $recipe->reviews()->where("user_id", "!=", Auth::user()->id)->get();

        return Inertia::render('Recipe/Show',
            [
                "recipe" => $recipe,
                "ingredients" => $recipe->ingredients,
                "review" => $review,
                "average" => $average ? $average : "No Rating Yet",
                "reviews" => $reviews,
                "users" => User::all()->except(Auth::user()->id),
                "shared_to" => $shared_to,
                "comments" => $recipe->comments()->with('user')->get(),
                "is_liked" => $recipe->likes()->where('user_id', Auth::user()->id)->count() > 0,
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $recipe = Recipe::findOrFail($id);
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
    public function update(UpdateRecipeRequest $request, $id)
    {
        $recipe = Recipe::findOrFail($id);
        $this->authorize('update', $recipe);
        $ingredients = [];
        $categories = [];

        foreach ($request->ingredients as $ingredient) {
            if (isset($ingredient["id"])) {
                $ingredients[] = $ingredient["id"];
            }
        }
        foreach ($request->categories as $category) {
            if (isset($category["id"])) {
                $categories[] = $category["id"];
            }
        }

        $recipe->title = $request->title;
        $recipe->description = $request->description;
        $recipe->instructions = $request->instructions;
        $recipe->is_favorite = $request->favorite;
        $recipe->ingredients()->sync($ingredients);
        $recipe->categories()->sync($categories);
        $recipe->save();

        return Inertia::location('/recipe');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);
        $this->authorize('delete', $recipe);
        $recipe->ingredients()->detach();
        $recipe->categories()->detach();

        $recipe->delete();
        return redirect()->back();
    }

    public function favorite($id, Request $request)
    {

        $recipe = Recipe::findOrFail($id);
        $this->authorize('update', $recipe);
        $recipe->is_favorite = !$recipe->is_favorite;
        $recipe->save();
        return redirect()->back();


    }

    public function rate($id, Request $request)
    {
        $recipe = Recipe::findOrFail($id);
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


    public function share($id, Request $request)
    {

        $userIDs = [];
        $recipe = Recipe::findOrFail($id);
        $this->authorize('update', $recipe);

        foreach ($request->users as $user) {
            $userIDs[] = $user["id"];
        }

        $recipe->shared()->sync($userIDs);

        return redirect()->back();
    }

    public function comment($id, Request $request)
    {
        $recipe = Recipe::findOrFail($id);
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

    public function like($id)
    {
        $recipe = Recipe::findOrFail($id);
        $this->authorize('view', $recipe);

        if ($recipe->likes()->where('user_id', Auth::user()->id)->count() > 0) {
            $recipe->likes()->detach(Auth::user()->id);
        } else {
            $recipe->likes()->attach(Auth::user()->id);

        }

        return redirect()->back();
    }


}
