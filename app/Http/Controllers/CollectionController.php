<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;
use App\Models\Collection;
use App\Models\Recipe;
use Exception;
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
        $collections = Auth::user()->collections()->with('recipes')->get();

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
        try {
            $collection = Collection::create([
                'name' => $request->name,
                'user_id' => Auth::user()->id
            ]);
            $collection->recipes()->sync($request->recipes);
            $this->flashSuccessMessage('Collection created successfully');
            return redirect()->route('collection.index');
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->route('collection.index');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Collection $collection)
    {
        try {
            $this->authorize('view', $collection);
            return Inertia::render('Collection/Collection_Show', [
                "collection" => $collection->load('recipes')
            ]);
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->route('collection.index');
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection)
    {
        try {
            $this->authorize('update', $collection);

            return Inertia::render('Collection/Collection_Edit', [
                "recipes" => Auth::user()->recipes()->get(),
                "collection" => $collection,
                "active" => $collection->load('recipes')->getRelation("recipes")->pluck("id"),

            ]);
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->route('collection.index');
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCollectionRequest $request, Collection $collection)
    {
        try {
            $this->authorize('update', $collection);
            $collection->update([
                'name' => $request->name,
            ]);
            $collection->recipes()->sync($request->recipes);
            $this->flashSuccessMessage('Collection updated successfully');
            return redirect()->route('collection.index');
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->route('collection.index');
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {

        try {
            $this->authorize('delete', $collection);
            $collection->delete();
            return redirect()->route('collection.index');
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->route('collection.index');

        }


    }
}
