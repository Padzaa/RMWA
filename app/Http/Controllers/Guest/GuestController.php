<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RecipeController;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class GuestController extends Controller
{
    /**
     * Displays a recipe for the guest.
     */
    public function show(Recipe $recipe)
    {
        try {
            $this->authorize("view", $recipe);
            $recipe->load('user');
            $average_rating = round($recipe->reviews()->avg("rating"), 2);

            return Inertia::render('Recipe/Show', [
                "recipe" => $recipe,
                "ingredients" => $recipe->ingredients,
                "average" => $average_rating != 0 ? $average_rating : "No Ratings Yet",
                "comments" => $recipe->comments()->with('user')->orderBy('created_at', 'desc')->get(),
            ]);
        } catch (\Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->route('recipe.index');
        }
    }

    /**
        Public page of the site, so mainly guest can access
    */
    public function public(Request $request)
    {
        $recipes = Recipe
            ::Public()
            ->FilterRecipes($request);
        $recipes = $this->OrderAndPaginate($recipes, $request);

        return Inertia::render('Recipe/All', [
            "title" => "Public Recipes",
            "recipes" => $recipes,
            'categories' => Category::all(),
            'ingredients' => Ingredient::orderBy('name')->get(),
            'filtersData' => $request->query->all(),
            'collections' => Auth::user() ? Auth::user()->collections()->orderBy('name')->get() : null

        ]);
    }
}
