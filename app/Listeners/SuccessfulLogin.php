<?php

namespace App\Listeners;

use App\Events\MyNotifications;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
