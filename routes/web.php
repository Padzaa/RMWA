<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\Guest\GuestController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MessageController;
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
    Route::resource('/ingredient', IngredientController::class)->except('show', 'index', 'edit', 'create');
    Route::resource('/category', CategoryController::class)->except('show', 'index', 'edit', 'create');
    Route::resource('/message', MessageController::class)->except('show', 'edit', 'create');


    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    });

    //RECIPE ROUTES
    Route::group([], function () {
        Route::put('/recipe/{recipe}/comment', [RecipeController::class, 'comment'])->name('comment');//Comment a recipe
        Route::put('/recipe/{recipe}/favorite', [RecipeController::class, 'favorite'])->name('favorite');//Make a recipe favorite
        Route::put('/recipe/{recipe}/like', [RecipeController::class, 'like'])->name('like');//Like a recipe
        Route::put('/recipe/{recipe}/rate', [RecipeController::class, 'rate'])->name('rate');//Rate a recipe
        Route::put('/recipe/{recipe}/share', [RecipeController::class, 'share'])->name('share');//Share a recipe
        Route::get('/favorites', [RecipeController::class, 'favorites'])->name('favorites');//Show favorite recipes
    });
    //SHARED-RECIPES ROUTES
    Route::group([], function () {
        Route::get('/myshared', [SharedRecipeController::class, 'myShared'])->name('my-shared');//Show shared recipes
        Route::get('/sharedwithme', [SharedRecipeController::class, 'sharedWithMe'])->name('shared-with-me');//Show shared with me recipes
    });
    //USER ROUTESđ
    Route::group([], function () {
        Route::put('/user/{user}/follow', [UserController::class, 'follow'])->name('follow');//Follow a user
        Route::put('/notifications/{id?}', [UserController::class, 'notifications'])->name('notifications');//Mark notification as read
    });
    //COMMENT ROUTES
    Route::group([], function () {
        Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->name('delete-comment');//Like a comment
    });

});
/*---------------------------------------------------------------------------------------*/
/*------------------END OF (ONLY ACCESSABLE WHEN USER LOGGED IN) ROUTES------------------*/
/*---------------------------------------------------------------------------------------*/

Auth::routes([]);
Route::prefix('guest')->group(function () {
    Route::get('/recipe/{recipe}', [GuestController::class, 'show'])->name('guest-recipe-show');//Show public recipe;
    Route::get("/public", [GuestController::class, "public"])->name("public");//All public recipes
});


