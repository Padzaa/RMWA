<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Collection/Collections', [
            "collections" => Auth::user()->collections()->with('recipes')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Collection/Collection_Create', [
            "recipes" => Auth::user()->recipes()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCollectionRequest $request)
    {
        $collection = Collection::create([
            'name' => $request->name,
            'user_id' => Auth::user()->id
        ]);
        $collection->recipes()->sync($request->recipes);
        $this->flashSuccessMessage('Collection created successfully');

        return redirect()->route('collection.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Collection $collection)
    {
        $this->authorize('view', $collection);

        return Inertia::render('Collection/Collection_Show', [
            "collection" => $collection->load('recipes')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection)
    {
        $this->authorize('update', $collection);

        return Inertia::render('Collection/Collection_Edit', [
            "recipes" => Auth::user()->recipes()->get(),
            "collection" => $collection,
            "active" => $collection->recipes()->get()->pluck("id"),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCollectionRequest $request, Collection $collection)
    {
        $this->authorize('update', $collection);
        $collection->update([
            'name' => $request->name,
        ]);
        $collection->recipes()->sync($request->recipes);
        $this->flashSuccessMessage('Collection updated successfully');

        return redirect()->route('collection.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
        $this->authorize('delete', $collection);
        $collection->delete();

        return Inertia::location(URL::previous());
    }
}
