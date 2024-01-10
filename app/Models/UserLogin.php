<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    use HasFactory;

    protected $table = 'user_logins';
    protected $fillable = [
        'user_id'
    ];

    /**
     * Retrieve the associated user.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get last users logins
     */
    public static function lastUserLogins()
    {
        return self::join('users', 'user_logins.user_id', '=', 'users.id')
            ->selectRaw('MAX(user_logins.created_at) as last_login,users.id,users.firstname,users.lastname,users.email')
            ->groupBy('users.id')
            ->orderBy('last_login', 'desc');
    }
}
