<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    /**
     * Make recipients for notifications, merging users that should get notification, admins and removes duplicates
     */
    public static function finalRecipientsForNotifications($user_ids)
    {
        $user_ids = gettype($user_ids) == "array" ? $user_ids : [$user_ids];
        $admins = \App\Models\User::getAdmins()->get();
        $users = \App\Models\User::whereIn("id", $user_ids)->get();

        return collect()->merge($users)->merge($admins)->unique();
    }

}
