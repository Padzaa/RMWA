<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use App\Models\UserLogin;
use App\Traits\Chart;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class AdminController extends Controller
{
    use Chart;

    /**
     * Returns Admins dashboard
     */
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'title' => 'Admin Dashboard',
            'users' => User::all(),
            'recipes' => Recipe::with('user')->orderBy('created_at')->get(),
            'public_recipes' => Recipe::public()->with('user')->orderBy('created_at')->get(),
            'collections' => Collection::with('user')->orderBy('name')->get(),
            'ingredients' => Ingredient::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'top_users' => User::topUsers()->get(),
            'activities' => Auth::user()->notifications()->get(),
            'last_user_logins' => UserLogin::lastUsersLogins()->get(),
            'users_comments' => Comment::all()->load('user'),
            'charts' => [
                'monthlyUsers' => $this->reconstructDataForMonthlyCharts(User::statisticsPerMonthForYear(2023)->get(), 2023),
                'monthlyRecipes' => $this->reconstructDataForMonthlyCharts(Recipe::statisticsPerMonthForYear(2023)->get(), 2023),
                'monthlyCollections' => $this->reconstructDataForMonthlyCharts(Collection::statisticsPerMonthForYear(2023)->get(), 2023),
            ]
        ]);
    }
}

