<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\Ingredient;
use App\Models\Notification;
use App\Models\Recipe;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class AdminController extends Controller
{

    /**
     * Returns Admins dashboard
     */
    public function dashboard(Request $request)
    {

        $requestedChartsYear = $request->query('year') ?? date('Y');
        $requestedUsers = $request->query('users') ?? 5;

        return Inertia::render('Admin/Dashboard', [
            'title' => 'Admin Dashboard',
            'users' => User::all(),
            'recipes' => $this->orderBy(Recipe::with('user'))->get(),
            'public_recipes' => $this->orderBy(Recipe::public()->with('user'))->get(),
            'collections' => Collection::with('user')->orderBy('name')->get(),
            'ingredients' => Ingredient::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'top_users' => User::topUsers($requestedUsers)->get(),
            'activities' => Auth::user()->notifications()->get(),
            'last_user_logins' => UserLogin::lastUsersLogins()->get(),
            'users_comments' => Comment::all()->load('user'),
            'charts' => [
                'monthlyUsers' => $this->reconstructDataForMonthlyCharts(User::statisticsPerMonthForYear($requestedChartsYear)->get(), $requestedChartsYear),
                'monthlyRecipes' => $this->reconstructDataForMonthlyCharts(Recipe::statisticsPerMonthForYear($requestedChartsYear)->get(), $requestedChartsYear),
                'monthlyCollections' => $this->reconstructDataForMonthlyCharts(Collection::statisticsPerMonthForYear($requestedChartsYear)->get(), $requestedChartsYear),
            ],
            'chosen_number_of_users' => +$requestedUsers,
            'chosen_year' => +$requestedChartsYear,
            'available_years' => range(date('Y'), 2000),
        ]);
    }
}

