<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Chart;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable, Chart;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'is_admin',
        'password',
        'picture',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Retrieve all recipes that a user owns
     */
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    /**
     * Retrieve recipes that are favorite to a user/paginating them
     */
    public function favorites()
    {
        return Recipe::where('is_favorite', true)->where('user_id', Auth::user()->id);
    }

    /**
     * Retrieves the shared recipes for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function sharedRecipes()
    {
        return $this
            ->recipes()
            ->join('shared_recipes', 'recipes.id', '=', 'shared_recipes.recipe_id')
            ->select('recipes.*')
            ->groupBy('recipes.id');
    }

    /**
     * Retrieve all recipes that are shared to a user
     */
    public function sharedWithMe()
    {
        return $this->belongsToMany(Recipe::class, 'shared_recipes', 'user_shared_to', 'recipe_id')->withTimestamps();
    }

    /**
     * Retrieve every collection that a certain user owns
     */
    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    /**
     * Retrieve every comment that a certain user has written
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Retrieve every review that a certain user has written
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Checking if there is a record of me following a certain user
     */
    public function followsUser($user)
    {
        return $this->followedByMe()->where('followed_user_id', $user->id);
    }

    /**
     * Retrieve all messages that a certain user has sent
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Retrieve all messages that a certain user has received
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Retrieve every user that a certain user follows
     */
    public function followedByMe()
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'followed_user_id')->withTimestamps();
    }

    /**
     * Retrieve every user that follows a certain user
     */
    public function myFollowers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_user_id', 'user_id')->withTimestamps();
    }

    /**
     * Retrieve every like that a certain user has
     */
    public function likedRecipes()
    {
        return $this->belongsToMany(Recipe::class, 'likes', 'user_id', 'recipe_id')->withTimestamps();
    }

    /**
     * Retrieve top 5 users who have written the recipes with the best ratings
     */
    public static function topUsers($number_of_users = 5)
    {
        return self::join('recipes', 'users.id', 'recipes.user_id')
            ->join('reviews', 'recipes.id', 'reviews.recipe_id')
            ->select('users.*', DB::raw('AVG(reviews.rating) as average_rating'))
            ->groupBy('users.id')
            ->orderBy('average_rating', 'desc')
            ->take($number_of_users);
    }

    /**
     * Retrieve admins
     */
    public static function getAdmins()
    {
        return self::where('is_admin', 1);
    }


}
