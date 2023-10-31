<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Recipe;
use App\Models\SharedRecipe;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;
class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        if(Auth::user()){
            $penultimateLogin = Auth::user()->penultimateLogin();
            $lastLogin= $penultimateLogin->format('Y-m-d H:i:s');
            $recipes = Recipe::where('created_at', ">=", $lastLogin)->where('is_public', true)->with("user")->get();
            $sharedRecipes = SharedRecipe::where('created_at', ">=", $lastLogin)->where('user_shared_to', Auth::user()->id)->with("recipe", "recipe.user")->get();
            $newFollowers = Auth::user()->followed()->where('follows.created_at', ">=", $lastLogin)->get();
            $likes = Like::join("recipes", "likes.recipe_id", "=", "recipes.id")
                ->where('recipes.user_id',Auth::user()->id)
                ->where("likes.created_at", ">=", $lastLogin)
                ->select("recipes.title","recipes.id","recipes.user_id","likes.*","users.*")
                ->join("users", "likes.user_id", "=", "users.id")->get();

            foreach ($recipes as $recipe) {
                $recipe->type = "Creation";
            }
            foreach ($sharedRecipes as $sharedRecipe) {
                $sharedRecipe->type = "Shared";
            }
            foreach ($newFollowers as $newFollower) {
                $newFollower->type = "Follow";
            }
            foreach ($likes as $like) {
                $like->type = "Like";
            }
            $notifications = collect($recipes->merge($sharedRecipes)->merge($newFollowers)->merge($likes))->sortByDesc('created_at');
        }



        return array_merge(parent::share($request), [


            "auth" => Auth::user() ? [
                'user' =>[
                    "id" => Auth::user()->id,
                    "firstname" => Auth::user()->firstname,
                    "lastname" => Auth::user()->lastname,
                    "email" => Auth::user()->email,
                    "picture" => Auth::user()->picture ? asset(Auth::user()->picture) : null,
                ]

            ] : null,

            'alertFlash' => $request->session()->get('alert'),

            'notifications' => $notifications ?? null,
        ]);
    }
}
