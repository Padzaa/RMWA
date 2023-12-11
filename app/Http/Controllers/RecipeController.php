<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Ingredient;
use App\Models\Notification;
use App\Models\Recipe;
use App\Models\Review;
use App\Models\User;
use App\Notifications\PublicRecipeCreated;
use App\Notifications\RecipeCommented;
use App\Notifications\RecipeLiked;
use App\Notifications\RecipeRated;
use App\Notifications\RecipeShared;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $recipes = Recipe
            ::forUser(Auth::user())
            ->filterRecipes($request);//Working with request cause it contains all filter functions at once, if needed they can be use separately with their own arguments

        $recipes = $this->orderAndPaginate($recipes, $request);

        return Inertia::render('Recipe/All', [
            "title" => "Recipes",
            'recipes' => $recipes,
            'categories' => Category::all(),
            'ingredients' => Ingredient::orderBy('name')->get(),
            'filtersData' => $request->query->all(),
            'collections' => Auth::user()->collections()->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Recipe/Recipe_Create', [
            "ingredients" => Ingredient::orderBy('name')->get(),
            "categories" => Category::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecipeRequest $request)
    {

        try {
            $recipe = Recipe::create([
                'title' => $request->title,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'user_id' => Auth::user()->id,
                'is_favorite' => +$request->favorite,
                'is_public' => +$request->public,
            ]);
            $recipe->ingredients()->attach($request->ingredients);
            $recipe->categories()->attach($request->categories);
            if ($recipe->is_public) {
                NotificationFacade::send(User::all()->except(Auth::user()->id), new PublicRecipeCreated($recipe->title, Auth::user()));
            }
            $this->flashSuccessMessage('Recipe created successfully.');
            return redirect()->route('recipe.index');
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->route('recipe.index');
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        try {
            $this->authorize("view", $recipe);
            $review = $reviews = $users = $shared_to = $is_liked = null;
            $recipe->load('user');

            if (Auth::user()) {
                $shared_to = $recipe->shared->pluck("id");
                $review = $recipe->reviewForRecipeByUser(Auth::user());
                $reviews = $recipe->reviews()->where("user_id", "!=", Auth::user()->id)->with('user')->get();
                $is_liked = $recipe->likes()->where('user_id', Auth::user()->id)->exists();
                $users = User::all()->except(Auth::user()->id);
            }

            $average = round($recipe->reviews()->avg("rating", 2), 2);

            return Inertia::render('Recipe/Show',
                [
                    "recipe" => $recipe,
                    "ingredients" => $recipe->ingredients,
                    "review" => $review,
                    "average" => $average ?: "No Rating Yet",
                    "reviews" => $reviews ?? [],
                    "users" => $users,
                    "shared_to" => $shared_to,
                    "comments" => $recipe->comments()->with('user')->orderBy('created_at', 'desc')->get(),
                    "is_liked" => $is_liked ?? false,
                ]);
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->route('recipe.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        try {
            $this->authorize('update', $recipe);
            return Inertia::render('Recipe/Recipe_Edit', [
                "recipe" => $recipe,
                "recipe.ingredients" => $recipe->ingredients->pluck("id"),
                "recipe.categories" => $recipe->categories->pluck("id"),
                "ingredients" => Ingredient::orderBy('name')->get(),
                "categories" => Category::all()
            ]);
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->route('recipe.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecipeRequest $request, Recipe $recipe)
    {
        try {
            $this->authorize('update', $recipe);
            $recipe->title = $request->title;
            $recipe->description = $request->description;
            $recipe->instructions = $request->instructions;
            $recipe->is_favorite = $request->favorite;
            $recipe->is_public = $request->public;
            $recipe->ingredients()->sync($request->ingredients);
            $recipe->categories()->sync($request->categories);
            $recipe->save();
            if ($recipe->is_public) {
                NotificationFacade::send(User::all()->except(Auth::user()->id), new PublicRecipeCreated($recipe->title, Auth::user()));
            }
            $this->flashSuccessMessage('Recipe updated successfully.');
            return redirect()->route('recipe.show', $recipe);
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->route('recipe.show', $recipe);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
        try {
            $this->authorize('delete', $recipe);
            $recipe
                ->collections()
                ->withCount("recipes")
                ->get()
                ->each(function ($collection) {
                    if ($collection->recipes_count == 1) {
                        $collection->delete();
                    }
                });

            $recipe->delete();
            $this->flashSuccessMessage('Recipe deleted successfully.');
            return Inertia::location(URL::previous());
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return Inertia::location(URL::previous());
        }

    }

    /**
     *Makes a recipe favorite to a user
     */
    public function favorite(Recipe $recipe, Request $request)
    {
        try {
            $this->authorize('update', $recipe);
            $recipe->is_favorite = !$recipe->is_favorite;
            $recipe->save();
            return redirect()->back();
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Creates a review(rate the recipe)
     */
    public function rate(Recipe $recipe, Request $request)
    {
        try {
            $this->authorize('view', $recipe);
            $request->request = $request->validate([
                "rating" => ['required', 'integer', 'max:5', 'min:1'],
                "msg" => ['required', 'string', 'max:500', 'min:1']
            ],
                [
                    "rating.required" => "Rating is required!",
                    "msg.required" => "Message is required!",
                ]);
            $rating = Review::updateOrCreate([
                'user_id' => Auth::user()->id,
                'recipe_id' => $recipe->id,
            ], [
                'rating' => $request->rating,
                'message' => $request->msg,
            ]);
            NotificationFacade::send(User::find($recipe->user_id), new RecipeRated($recipe->title, $rating->rating, Auth::user()));
            $this->flashSuccessMessage('Review added successfully.');
            return redirect()->back();
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return Inertia::location('/recipe/' . $recipe->id);
        }


    }

    /**
     * Share a recipe with other users
     */
    public function share(Recipe $recipe, Request $request)
    {
        try {
            $this->authorize('update', $recipe);
            $recipe->shared()->sync($request->users);
            $users_shared_to = User::whereIn("id", $request->users)->get();
            $this->flashSuccessMessage('Recipe shared successfully.');
            NotificationFacade::send($users_shared_to, new RecipeShared(Auth::user(), $recipe->title));
            return Inertia::location('/recipe/' . $recipe->id);
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return Inertia::location('/recipe/' . $recipe->id);
        }

    }

    /**
     * Adds a comment for the recipe
     */
    public function comment(Recipe $recipe, Request $request)
    {
        try {

            $request->request = $request->validate([
                "comment" => ['required', 'string', 'max:500', 'min:1']
            ], [
                "comment.required" => "Comment is required!",
            ]);
            $recipe->comments()->create([
                "comment" => $request->comment,
                "user_id" => Auth::user()->id,
            ]);
            $this->authorize('view', $recipe);
            $this->flashSuccessMessage('Comment added successfully.');
            NotificationFacade::send($recipe->user()->get(), new RecipeCommented($recipe->title, Auth::user()));
            return Inertia::location('/recipe/' . $recipe->id);
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return Inertia::location('/recipe/' . $recipe->id);
        }
    }

    /**
     * Likes the recipe for a user
     */
    public function like(Recipe $recipe)
    {

        try {
            $this->authorize('view', $recipe);
            if ($recipe->likes()->where('user_id', Auth::user()->id)->exists()) {
                $recipe->likes()->detach(Auth::user()->id);
            } else {
                NotificationFacade::send($recipe->user()->get(),new RecipeLiked(Auth::user(), $recipe->title));
                $recipe->likes()->attach(Auth::user()->id);
            }
            return redirect()->back();
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return redirect()->back();
        }

    }

    /**
     * Goes to favorites page
     */
    public function favorites(Request $request)
    {
        $recipes = Auth::user()->favorites();
        $recipes = $this->orderAndPaginate($recipes, $request);
        return Inertia::render('User/Favorites', [
            "recipes" => $recipes,
        ]);
    }


    /**
     * Updates the read_at field of the notifications for the authenticated user.
     */
    public function notifications($id = null)
    {
        if ($id) {
            Notification::where('id', $id)->update(['read_at' => now()]);
            return redirect()->back();
        }
        Notification::where('notifiable_id', Auth::user()->id)->where('read_at', null)->update(['read_at' => now()]);
        return Inertia::location(URL::previous());
    }

}
