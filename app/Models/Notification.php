<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    /**
     * Make recipients for notifications, merging users that should get notification and admins, but also removes duplicates
     */
    public static function finalRecipientsForNotifications($user_ids)
    {
        $user_ids = gettype($user_ids) == "array" ? $user_ids : [$user_ids];
        $admins = User::getAdmins()->get();
        $users = User::whereIn("id", $user_ids)->get();

        return collect()->merge($users)->merge($admins)->unique();
    }

}
