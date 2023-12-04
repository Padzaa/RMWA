<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class CategoryController extends Controller
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
    public function store(StoreCategoryRequest $request)
    {
        try {
            $this->authorize('create', Category::class);
            $category = Category::create([
                'name' => $request->name
            ]);
            $this->flashSuccessMessage("Category {$category->name} created successfully");
        } catch (\Exception $e) {
            $this->flashErrorMessage($e->getMessage());
        }
        return Inertia::location(URL::previous());
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $this->authorize('update', $category);
            $category->update([
                'name' => $request->name
            ]);
            $this->flashSuccessMessage("Category updated successfully");
        } catch (\Exception $e) {
            $this->flashErrorMessage($e->getMessage());
        }
        return Inertia::location(URL::previous());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $this->authorize('delete', $category);
            $category->delete();
            $this->flashSuccessMessage("Category deleted successfully");
        } catch (\Exception $e) {
            $this->flashErrorMessage($e->getMessage());
        }
        return Inertia::location(URL::previous());
    }
}
