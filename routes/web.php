<?php

use App\Http\Controllers\RecipeController;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
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
Route::middleware('auth')->group(function (){
    Route::get('/', function () {
        return Inertia::render('Welcome');
    })->name('welcome');

    Route::get('/rec', function () {
        return Inertia::render('Recipes');
    })->name('welcome1');
    Route::resource('/recipe', RecipeController::class);
    Route::put('/recipe/{id}/favorite', [RecipeController::class, 'favorite'])->name('favorite');
    Route::get('/recipe/{id}/favorite', function(){

        return Inertia::location("/recipe/");
    });
});

Auth::routes();



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
