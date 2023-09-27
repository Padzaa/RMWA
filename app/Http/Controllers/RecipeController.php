<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Category;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $ingredients = $request->query('ingredients');
        $categories = $request->query('categories');
        $favorite = $request->query('favorite');

        $filteredRecipes = Recipe::query();


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


        if ($favorite !== null) {
            if ($favorite == "true") {
                $favorite = 1;
                $filteredRecipes->where("is_favorite", true);
            }
            if ($favorite == "false") {

                $filteredRecipes->whereIn("is_favorite", [0, 1]);
            }


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
            "ingredients" => Ingredient::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecipeRequest $request)
    {

        $request->attributes = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:512'],
            'instructions' => ['required', 'string', 'max:3000'],
            'ingredients' => ['required', 'array', 'min:1']

        ]);

        $recipe = Recipe::create([
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'user_id' => Auth::user()->id,
            'is_favorite' => +$request->favorite
        ]);

        foreach ($request->ingredients as $ingredient) {
            if (explode("|", $ingredient)[1]) {
                $ingredients[] = explode("|", $ingredient)[1];
            }
        }
        $recipe->ingredients()->attach($ingredients);
        return Inertia::location('/recipe');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $recipe = Recipe::findOrFail($id);

        return Inertia::render('Recipe/Show',
            [
                "recipe" => $recipe,
                "ingredients" => $recipe->ingredients
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecipeRequest $request, Recipe $recipe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
        //
    }

    public function favorite($id, Request $request)
    {

        $recipe = Recipe::findOrFail($id);
        $recipe->is_favorite = !$recipe->is_favorite;
        $recipe->save();
        return $this->index();


    }
//    public function filter(Request $request){
//
//
//        $ingredients = $request->ingredients;
//        $categories = $request->categories;
//        $favorite = $request->favorite ? 1:0;
//        $filteredRecipes = Recipe::query();
//
//        if($request->favorite){
//            $filteredRecipes = Recipe::where("is_favorite", $favorite);
//        }
//
//        if ($categories) {
//            $filteredRecipes->whereHas('categories', function ($query) use ($categories) {
//                $query->whereIn('category_id', $categories);
//            });
//        }
//
//        if ($ingredients) {
//            $filteredRecipes->whereHas('ingredients', function ($query) use ($ingredients) {
//                $query->whereIn('ingredient_id', $ingredients);
//            });
//        }
//
//        $filteredRecipes = $filteredRecipes->paginate(10);
//
//
//        return Inertia::render('Recipe/All',[
//            'recipes' => $filteredRecipes,
//            'categories' => Category::all(),
//            'ingredients' => Ingredient::all(),
//        ]);
//    }


}
