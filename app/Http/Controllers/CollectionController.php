<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;
use App\Models\Collection;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $collections = Collection::where("user_id", Auth::user()->id)->with('recipes')->get();


        return Inertia::render('Collection/Collections', [
            "collections" => $collections
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Collection/Collection_Create', [
            "recipes" => Recipe::where("user_id", Auth::user()->id)->get(),

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCollectionRequest $request)
    {

        $recipeIDs = [];
        foreach ($request->recipes as $recipe) {
            $recipeIDs[] = $recipe["id"];
        }


        $collection = Collection::create([
            'name' => $request->name,
            'user_id' => Auth::user()->id
        ]);
        $collection->recipes()->sync($recipeIDs);

        return redirect()->route('collection.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Collection $collection)
    {
        $this->authorize('view', $collection);
        $collection = Collection::where("id", $collection->id)->with('recipes')->first();
        return Inertia::render('Collection/Collection_Show', [
            "collection" => $collection
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection)
    {

        $this->authorize('update', $collection);
        $collection = Collection::where("id", $collection->id)->with('recipes')->first();


        return Inertia::render('Collection/Collection_Edit', [
            "recipes" => Recipe::where("user_id", Auth::user()->id)->get(),
            "collection" => $collection
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCollectionRequest $request, Collection $collection)
    {
        $this->authorize('update', $collection);
        $recipeIDs = [];

        foreach ($request->recipes as $recipe) {
            $recipeIDs[] = $recipe["id"];
        }
        $collection->update([
            'name' => $request->name,
        ]);
        $collection->recipes()->sync($recipeIDs);

        return redirect()->route('collection.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
        $collection = Collection::findOrFail($collection->id);
        $this->authorize('delete', $collection);
        $collection->delete();

        return redirect()->route('collection.index');
    }
}
