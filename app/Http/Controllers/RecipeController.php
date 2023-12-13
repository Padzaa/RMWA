<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Ingredient;
use App\Models\Notification;
use App\Models\Recipe;
use App\Models\Review;
use App\Models\User;
use App\Notifications\RecipeCreated;
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
            ->filterRecipes($request);
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
            NotificationFacade::send(User::all(), new RecipeCreated($recipe->title, Auth::user(), "Public"));
        } else {
            NotificationFacade::send(User::getAdmins()->get(), new RecipeCreated($recipe->title, Auth::user()));
        }
        $this->flashSuccessMessage('Recipe created successfully.');

        return redirect()->route('recipe.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        $this->authorize("view", $recipe);
        $recipe->load('user');

        return Inertia::render('Recipe/Show',
            [
                "recipe" => $recipe,
                "ingredients" => $recipe->ingredients()->get(),
                "review" => $recipe->reviewForRecipeByUser(Auth::user()),
                "average" => round($recipe->reviews()->avg("rating", 2), 2) ?: "No Rating Yet",
                "reviews" => $recipe->reviews()->where("user_id", "!=", Auth::user()->id)->with('user')->get() ?? [],
                "users" => User::all()->except(Auth::user()->id),
                "shared_to" => $recipe->shared()->get()->pluck("id"),
                "comments" => $recipe->comments()->with('user')->orderBy('created_at', 'desc')->get(),
                "is_liked" => $recipe->isLikedByUser(Auth::user()),
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        $this->authorize('update', $recipe);
        return Inertia::render('Recipe/Recipe_Edit', [
            "recipe" => $recipe,
            "recipe.ingredients" => $recipe->ingredients()->get()->pluck("id"),
            "recipe.categories" => $recipe->categories()->get()->pluck("id"),
            "ingredients" => Ingredient::orderBy('name')->get(),
            "categories" => Category::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecipeRequest $request, Recipe $recipe)
    {
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
            NotificationFacade::send(User::all()->except(Auth::user()->id), new RecipeCreated($recipe->title, Auth::user(), "Public"));
        } else {

            NotificationFacade::send(User::getAdmins()->get(), new RecipeCreated($recipe->title, Auth::user()));
        }

        $this->flashSuccessMessage('Recipe updated successfully.');

        return redirect()->route('recipe.show', $recipe);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
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
    }

    /**
     *Makes a recipe favorite to a user
     */
    public function favorite(Recipe $recipe)
    {
        $this->authorize('update', $recipe);
        $recipe->is_favorite = !$recipe->is_favorite;
        $recipe->save();

        return redirect()->back();
    }

    /**
     * Creates a review(rate the recipe)
     */
    public function rate(Recipe $recipe, Request $request)
    {
        $this->authorize('view', $recipe);
        $request->request = $request->validate([
            "rating" => ['required', 'integer', 'max:5', 'min:1'],
            "msg" => ['required', 'string', 'max:500', 'min:1']
        ],
            [
                "rating.required" => "Rating is required!",
                "msg.required" => "Message is required!",
            ]);
        //updateOrCreate --- If the first [data](param 1) exists it updates the row with second [data](param 2), if not it creates row with both [data](both params)
        $rating = Review::updateOrCreate([
            'user_id' => Auth::user()->id,
            'recipe_id' => $recipe->id,
        ], [
            'rating' => $request->rating,
            'message' => $request->msg,
        ]);
        $recipients = $this->finalRecipients($recipe->user_id);
        NotificationFacade::send($recipients, new RecipeRated($recipe->title, $rating->rating, Auth::user()));
        $this->flashSuccessMessage('Review added successfully.');

        return redirect()->back();

    }

    /**
     * Share a recipe with other users
     */
    public function share(Recipe $recipe, Request $request)
    {
        $this->authorize('update', $recipe);
        $recipe->shared()->sync($request->users);
        $this->flashSuccessMessage('Recipe shared successfully.');

        $recipients = $this->finalRecipients($request->users);
        NotificationFacade::send($recipients, new RecipeShared(Auth::user(), $recipe->title));

        return Inertia::location('/recipe/' . $recipe->id);
    }

    /**
     * Adds a comment for the recipe
     */
    public function comment(Recipe $recipe, Request $request)
    {
        $this->authorize('view', $recipe);
        $request->request = $request->validate([
            "comment" => ['required', 'string', 'max:500', 'min:1']
        ], [
            "comment.required" => "Comment is required!",
        ]);
        $recipe->comments()->create([
            "comment" => $request->comment,
            "user_id" => Auth::user()->id,
        ]);

        $this->flashSuccessMessage('Comment added successfully.');

        $recipients = $this->finalRecipients($recipe->user_id);
        NotificationFacade::send($recipients, new RecipeCommented($recipe->title, Auth::user()));

        return redirect()->back();

    }

    /**
     * Likes the recipe for a user
     */
    public function like(Recipe $recipe)
    {
        $this->authorize('view', $recipe);
        if ($recipe->isLikedByUser(Auth::user())) {
            $recipe->likes()->detach(Auth::user()->id);
        } else {
            $recipe->likes()->attach(Auth::user()->id);
            $recipients = $this->finalRecipients($recipe->user_id);
            NotificationFacade::send($recipients, new RecipeLiked(Auth::user(), $recipe->title));
        }
        return redirect()->back();
    }

    /**
     * Goes to favorites page
     */
    public function favorites(Request $request)
    {
        $recipes = $this->orderAndPaginate(Auth::user()->favorites(), $request);
        return Inertia::render('User/Favorites', [
            "recipes" => $recipes,
        ]);
    }

}
