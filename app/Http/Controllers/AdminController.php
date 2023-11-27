<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Ingredient;
use App\Models\Notification;
use App\Models\Recipe;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class AdminController extends Controller
{

    /**
     * Returns Admins dashboard
     */
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'title' => 'Admin Dashboard',
            'users' => User::all()->toArray(),
            'recipes' => $this->orderBy(Recipe::with('user'))->get()->toArray(),
            'public_recipes' => $this->orderBy(Recipe::Public()->with('user'))->get()->toArray(),
            'collections' => Collection::with('user')->orderBy('name')->get()->toArray(),
            'ingredients' => Ingredient::orderBy('name')->get()->toArray(),
            'categories' => Category::orderBy('name')->get()->toArray(),
            'top_users' => User::topUsers()->get()->toArray(),
            'activities' => Auth::user()->notifications()->get()->toArray(),
            'last_user_logins' => UserLogin::lastUsersLogins()->get()->toArray(),
            'charts' => [
                'monthlyUsers' => $this->reconstructDataForCharts(User::monthlyUsers()->get()),
                'monthlyRecipes' => $this->reconstructDataForCharts(Recipe::monthlyRecipes()->get()),
                'monthlyCollections' => $this->reconstructDataForCharts(Collection::monthlyCollections()->get()),
            ]
        ]);
    }
}

