<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIngridientRequest;
use App\Http\Requests\UpdateIngridientRequest;
use App\Models\Ingredient;

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingridient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingridient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIngridientRequest $request, Ingredient $ingridient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingridient)
    {
        //
    }
}
