<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIngridientRequest;
use App\Http\Requests\UpdateIngridientRequest;
use App\Models\Ingredient;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreIngridientRequest $request)
    {
        try {
            $this->authorize('create', Ingredient::class);
            $ingredient = Ingredient::create([
                'name' => $request->name
            ]);
            $this->flashSuccessMessage("Ingredient {$ingredient->name} created successfully");
        } catch (\Exception $e) {
            $this->flashErrorMessage($e->getMessage());
        }
        return Inertia::location(URL::previous());
    }

    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingredient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIngridientRequest $request, Ingredient $ingredient)
    {
        try {
            $this->authorize('update', $ingredient);
            $ingredient->update([
                'name' => $request->name
            ]);
            $this->flashSuccessMessage("Ingredient updated successfully");
        } catch (\Exception $e) {
            $this->flashErrorMessage($e->getMessage());
        }
        return Inertia::location(URL::previous());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {

        try {
            $this->authorize('delete', $ingredient);
            $ingredient->delete();
            $this->flashSuccessMessage("Ingredient deleted successfully");
        } catch (\Exception $e) {
            $this->flashErrorMessage($e->getMessage());
        }
        return Inertia::location(URL::previous());
    }
}
