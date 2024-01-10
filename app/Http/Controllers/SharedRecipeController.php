<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSharedRecipeRequest;
use App\Http\Requests\UpdateSharedRecipeRequest;
use App\Models\SharedRecipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SharedRecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Retrieve the recipes shared with the authenticated user.
     */
    public function sharedWithMe(Request $request)
    {
        return Inertia::render('User/Shared_Recipes', [
            'title' => "Recipes shared with me",
            'recipes' => $this->orderAndPaginate(Auth::user()->sharedWithMe(), $request),
        ]);
    }

    /**
     * Retrieves the recipes belonging to the authenticated user that he shared.
     */
    public function myShared(Request $request)
    {
        return Inertia::render('User/Shared_Recipes', [
            'title' => "My Shared Recipes",
            'recipes' => $this->orderAndPaginate(Auth::user()->sharedRecipes(), $request),
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
