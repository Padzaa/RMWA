<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class WelcomeController extends Controller
{
    /**
     * Returns the welcome page
     */
    public function welcome()
    {
        return Inertia::render('Welcome');
    }
}
