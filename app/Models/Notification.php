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
    public static function finalRecipientsForNotifications($userIds)
    {
        $userIds = gettype($userIds) == "array" ? $userIds : [$userIds];
        $admins = User::admins()->get();
        $users = User::whereIn("id", $userIds)->get();

        return collect()->merge($users)->merge($admins)->unique();
    }

}
