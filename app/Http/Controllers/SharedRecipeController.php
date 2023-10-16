<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSharedRecipeRequest;
use App\Http\Requests\UpdateSharedRecipeRequest;
use App\Models\Recipe;
use App\Models\SharedRecipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SharedRecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recipes = Recipe::where('user_id', Auth::user()->id)->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('shared_recipes')
                ->whereRaw('shared_recipes.recipe_id = recipes.id');
        })->paginate(10);

        return Inertia::render('User/Shared_Recipes', [
            'recipes' => $recipes,
            'title' => "My Shared Recipes"
        ]);
    }

    public function sharedWithMe()
    {
        $recipes = Recipe::whereHas('shared', function ($query) {
            $query->where('user_shared_to', Auth::user()->id);
        })->paginate(10);
        return Inertia::render('User/Shared_Recipes', [
            'recipes' => $recipes,
            'title' => "Recipes shared with me"
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSharedRecipeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SharedRecipe $sharedRecipe)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SharedRecipe $sharedRecipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSharedRecipeRequest $request, SharedRecipe $sharedRecipe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SharedRecipe $sharedRecipe)
    {
        //
    }
}
