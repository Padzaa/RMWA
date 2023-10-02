<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Review;
use App\Models\User;
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
        $user = Auth::user();
        $ingredients = $request->query('ingredients');
        $categories = $request->query('categories');
        $favorite = $request->query('favorites');
        $ratings = $request->query('ratings');
        $order = $request->query('order');

        $filteredRecipes = $user->recipes()
            ->orWhereHas('shared', function ($query) use ($user) {
                $query->where('shared_recipes.user_shared_to', $user->id);
            });

        if ($ratings !== null) {
            $filteredRecipes = $filteredRecipes->select('recipes.*', \DB::raw('ROUND(AVG(reviews.rating),2) as average_rating'), \DB::raw('COUNT(reviews.id) as review_count'))
                ->leftJoin('reviews', 'reviews.recipe_id', '=', 'recipes.id')
                ->groupBy('recipes.id')
                ->havingRaw('FLOOR(AVG(reviews.rating)) IN (' . implode(',', $ratings) . ')')
                ->orderBy('average_rating', $order ? $order : 'desc');
        }
        if ($ratings == null) {
            $filteredRecipes = $filteredRecipes->select('recipes.*', \DB::raw('ROUND(AVG(reviews.rating),2) as average_rating'), \DB::raw('COUNT(reviews.id) as review_count'))
                ->leftJoin('reviews', 'reviews.recipe_id', '=', 'recipes.id')
                ->groupBy('recipes.id')
                ->orderBy('average_rating', $order ? $order : 'desc');

        }

        if ($categories !== null) {
            $filteredRecipes->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('category_id', $categories);
            });
        }

        if ($ingredients !== null) {
            $filteredRecipes->whereHas('ingredients', function ($query) use ($ingredients) {
                $query->whereIn('ingredient_id', $ingredients);
            });

        }
        if ($favorite == "true") {
            $favorite = 1;
            $filteredRecipes->where("is_favorite", true)->where("recipes.user_id", Auth::user()->id);
        }
        if ($favorite == "false") {
            $filteredRecipes->whereIn("is_favorite", [0, 1]);
        }

        $filteredRecipes = $filteredRecipes->paginate(10);



        return Inertia::render('Recipe/All', [
            'recipes' => $filteredRecipes,
            'categories' => Category::all(),
            'ingredients' => Ingredient::all(),
            'filterData' => $request->query->all(),


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
        $request->attributes = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:512'],
            'instructions' => ['required', 'string', 'max:3000'],
            'ingredients' => ['required', 'array', 'min:1'],
            'categories' => ['required', 'array', 'min:1']

        ],
        [
            'title.required' => 'A title is required!',
            'description.required' => 'A description is required!',
            'instructions.required' => 'An instructions is required!',
            'ingredients.required' => 'At least one ingredient is required!',
            'categories.required' => 'At least one category is required!',
        ]);

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

        $recipe = Recipe::findOrFail($id);
        $this->authorize("view",$recipe);
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
                "shared_to" => $shared_to
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

        $request->attributes = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:512'],
            'instructions' => ['required', 'string', 'max:3000'],
            'ingredients' => ['required', 'array', 'min:1'],
            'categories' => ['required', 'array', 'min:1']

        ],
            [
                'title.required' => 'A title is required!',
                'description.required' => 'A description is required!',
                'instructions.required' => 'An instructions is required!',
                'ingredients.required' => 'At least one ingredient is required!',
                'categories.required' => 'At least one category is required!',
            ]);

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


    public function share($id, Request $request){

        $userIDs = [];
        $recipe = Recipe::findOrFail($id);
        $this->authorize('update', $recipe);
        
        foreach($request->users as $user){
            $userIDs[] = $user["id"];
        }

        $recipe->shared()->sync($userIDs);

        return redirect()->back();
    }



}
