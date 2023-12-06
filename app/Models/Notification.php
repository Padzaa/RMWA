<?php

namespace App\Models;

use App\Events\MyNotifications;
use App\Notifications\RecipeCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as NotificationF;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

}
