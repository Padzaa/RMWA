<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\Guest\GuestController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SharedRecipeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CollectionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
/*---------------------------------------------------------------------------------------*/
/*--------------------------ONLY ACCESSABLE WHEN USER LOGGED IN--------------------------*/
/*---------------------------------------------------------------------------------------*/
Route::middleware(['auth'])->group(function () {
    //Home page
    Route::get('/', [WelcomeController::class, 'welcome'])->name('welcome');
    //Resource routes
    Route::resource('/recipe', RecipeController::class);
    Route::resource('/user', UserController::class);
    Route::resource('/review', ReviewController::class);
    Route::resource('/collection', CollectionController::class);
    Route::resource('/follow', FollowController::class);
    Route::resource('/like', LikeController::class);
    Route::resource('/shared', SharedRecipeController::class);
    //RECIPE ROUTES
    Route::group([], function () {
        Route::put('/recipe/{recipe}/comment', [RecipeController::class, 'comment'])->name('comment');//Comment a recipe
        Route::put('/recipe/{recipe}/favorite', [RecipeController::class, 'favorite'])->name('favorite');//Make a recipe favorite
        Route::put('/recipe/{recipe}/like', [RecipeController::class, 'like'])->name('like');//Like a recipe
        Route::put('/recipe/{recipe}/rate', [RecipeController::class, 'rate'])->name('rate');//Rate a recipe
        Route::put('/recipe/{recipe}/share', [RecipeController::class, 'share'])->name('share');//Share a recipe
        Route::put('/notifications', [RecipeController::class, 'notifications'])->name('notifications');//Mark notification as read
        Route::get('/favorites', [RecipeController::class, 'favorites'])->name('favorites');//Show favorite recipes
    });
    //SHARED-RECIPES ROUTES
    Route::group([], function () {
        Route::get('/my-shared', [SharedRecipeController::class, 'myShared'])->name('my-shared');//Show shared recipes
        Route::get('/shared-with-me', [SharedRecipeController::class, 'sharedWithMe'])->name('shared-with-me');//Show shared with me recipes
    });
    //USER ROUTES
    Route::put('/user/{user}/follow', [UserController::class, 'follow'])->name('follow');//Follow a user
});
/*---------------------------------------------------------------------------------------*/
/*------------------END OF (ONLY ACCESSABLE WHEN USER LOGGED IN) ROUTES------------------*/
/*---------------------------------------------------------------------------------------*/

Auth::routes();
Route::prefix('guest')->group(function () {
    Route::get('/recipe/{recipe}', [GuestController::class, 'show'])->name('guest-recipe-show');
        //Public page of the site, so mainly guest can access
    Route::get("/public", [GuestController::class, "public"])->name("public");
});


