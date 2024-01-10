<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
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
        $averageRating = round($recipe->reviews()->avg("rating"), 2);

        return Inertia::render('Recipe/Show', [
            "recipe" => $recipe->load('user'),
            "ingredients" => $recipe->ingredients()->get(),
            "average" => $averageRating ?: "No Ratings Yet",
            "comments" => $recipe->comments()->with('user')->orderBy('created_at', 'desc')->get(),
        ]);
    }

    /**
     * Public page of the site, so mainly guest can access
     */
    public function public(Request $request)
    {
        $recipes = Recipe
            ::public()
            ->filterRecipes($request);

        return Inertia::render('Recipe/All', [
            "title" => "Public Recipes",
            "recipes" => $this->orderAndPaginate($recipes, $request),
            'categories' => Category::all(),
            'ingredients' => Ingredient::orderBy('name')->get(),
            'filtersData' => $request->query->all(),
            'collections' => Auth::user() ? Auth::user()->collections()->orderBy('name')->get() : null
        ]);
    }
}
