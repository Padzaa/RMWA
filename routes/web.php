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
Route::middleware('auth')->group(function () {

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
    Route::put('/recipe/{id}/rate', [RecipeController::class, 'rate'])->name('rate');
    Route::put('/recipe/{id}/rate', [RecipeController::class, 'rate'])->name('rate');
    Route::get('/recipe/{id}/rate', function () {
        return Inertia::location("/recipe/");
    });

//Share routes
    Route::put('/recipe/{id}/share', [RecipeController::class, 'share'])->name('share');
    Route::get('/sharedtome', [SharedRecipeController::class, 'sharedToMe'])->name('sharedtome');

//Favorites routes
    Route::get('/favorites', function () {
        $favorites = Auth::user()->favorites();
        return Inertia::render('User/Favorites', [
            "recipes" => $favorites
        ]);
    });
    Route::put('/recipe/{id}/favorite', [RecipeController::class, 'favorite'])->name('favorite');
    Route::get('/recipe/{id}/favorite', function () {
        return Inertia::location("/recipe/");
    });
    //Comment route
    Route::put('/recipe/{id}/comment', [RecipeController::class, 'comment'])->name('comment');
    //Follow route
    Route::put('/user/{id}/follow', [UserController::class, 'follow'])->name('follow');
    //Like route
    Route::put('/recipe/{id}/like', [RecipeController::class, 'like'])->name('like');

});

Auth::routes();


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
