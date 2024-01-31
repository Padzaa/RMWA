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
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\Chart;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class AdminController extends Controller
{
    use Chart;

    /**
     * Returns Admins dashboard
     */
    public function dashboard(Request $request)
    {

        $tsrStartDate = isset($request->dateRange[0]) ? Carbon::createFromTimeString($request->dateRange[0])->setTime(0, 0, 0) :
            Carbon::now()->subDays(7)->setTime(0, 0, 0);
        $tsrEndDate = isset($request->dateRange[1]) ? Carbon::createFromTimeString($request->dateRange[1])->setTime(0, 0, 0) :
            Carbon::now()->addDay()->setTime(0, 0, 0);

        $requestedChartsYear = $request->query('year') ?? date('Y');
        $requestedNumberOfUsers = $request->query('users') ?? 5;
        return Inertia::render('Admin/Dashboard', [
            'title' => 'Admin Dashboard',
            'users' => User::all(),
            'recipes' => Recipe::with('user')->orderBy('created_at')->get(),
            'public_recipes' => Recipe::public()->with('user')->orderBy('created_at')->get(),
            'collections' => Collection::with('user')->orderBy('name')->get(),
            'ingredients' => Ingredient::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'top_users' => User::topUsers($requestedNumberOfUsers)->get(),
            'activities' => Auth::user()->notifications()->where('type', '!=', 'App\Notifications\TechnicalSupportRequest')->get(),
            'last_user_logins' => UserLogin::lastUserLogins()->get(),
            'users_comments' => Comment::all()->load('user'),
            'charts' => [
                'monthlyUsers' => User::statisticsPerMonthForYear($requestedChartsYear),
                'monthlyRecipes' => Recipe::statisticsPerMonthForYear($requestedChartsYear),
                'monthlyCollections' => Collection::statisticsPerMonthForYear($requestedChartsYear),
            ],
            'chosen_number_of_users' => +$requestedNumberOfUsers,
            'chosen_year' => +$requestedChartsYear,
            'available_years' => range(date('Y'), 2000),
            'technical_support_requests' => Auth::user()->technicalSupportRequests()->whereBetween('created_at', [$tsrStartDate, $tsrEndDate])->get(),
            'technicalSupportRequestDateRange' => [
                $tsrStartDate, $tsrEndDate
            ]
        ]);
    }
}

