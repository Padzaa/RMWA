<?php

use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SharedRecipeController;
use App\Http\Controllers\UserController;
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
    Route::get('/', function () {
        return Inertia::render('Welcome');
    })->name('welcome');
//Resource routes
    Route::resource('/recipe', RecipeController::class);

    Route::resource('/user', UserController::class);
    Route::resource('/review', ReviewController::class);
    Route::resource('/collection', CollectionController::class);
    Route::resource('/follow', FollowController::class);
    Route::resource('/like', LikeController::class);
    Route::resource('/shared', SharedRecipeController::class);
//Rate routes
    Route::put('/recipe/{recipe}/rate', [RecipeController::class, 'rate'])->name('rate');
    Route::put('/recipe/{recipe}/rate', [RecipeController::class, 'rate'])->name('rate');
    Route::get('/recipe/{recipe}/rate', function () {
        return Inertia::location("/recipe/");
    });
//Share routes
    Route::put('/recipe/{recipe}/share', [RecipeController::class, 'share'])->name('share');
    Route::get('/sharedwithme', [SharedRecipeController::class, 'sharedWithMe'])->name('sharedwithme');
//Favorites routes
    Route::get('/favorites', [RecipeController::class, 'favorites'])->name('favorites');
    Route::put('/recipe/{recipe}/favorite', [RecipeController::class, 'favorite'])->name('favorite');
    Route::get('/recipe/{recipe}/favorite', function () {
        return Inertia::location("/recipe/");
    });
    //Comment route
    Route::put('/recipe/{recipe}/comment', [RecipeController::class, 'comment'])->name('comment');
    //Follow route
    Route::put('/user/{user}/follow', [UserController::class, 'follow'])->name('follow');
    //Like route
    Route::put('/recipe/{recipe}/like', [RecipeController::class, 'like'])->name('like');

});
/*---------------------------------------------------------------------------------------*/
/*------------END OF (ONLY ACCESSABLE WHEN USER LOGGED IN) ROUTES------------------------*/
/*---------------------------------------------------------------------------------------*/

Auth::routes();
//Guest can access show route of this resource
Route::resource('/recipe', RecipeController::class)->only("show");
//Public page of the site, so mainly guest can access
Route::get("/public", [RecipeController::class,"public"])->name("public");

