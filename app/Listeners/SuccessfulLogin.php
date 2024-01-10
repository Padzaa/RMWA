<?php

namespace App\Listeners;

use App\Events\MyNotifications;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

class SuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        UserLogin::create([
            'user_id' => $event->user->id,
        ]);
        event(new MyNotifications(Auth::user()));

    }
}
