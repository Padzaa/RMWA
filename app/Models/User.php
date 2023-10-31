<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;

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
        'picture'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'is_admin',
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
    public function penultimateLogin(){
        return $this->logins()->orderBy('created_at')->take(2)->first()->toSql();
    }
    /**
     * Retrieves the last login record for the user.
     *
     * @return Login|null The last login record or null if no login records exist.
     */
    public function lastLogin(){
        return $this->logins()->latest()->first();
    }
    /**
     * Retrieves the logins associated with this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logins(){
        return $this->hasMany(UserLogin::class);
    }
    /*
     Retrieve all recipes that a user owns
     */
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    /*
     Retrieve recipes that are favorite to a user/paginating them
     */

    public function favorites($per_page = 10)
    {
        return Recipe::where('is_favorite', true)->where('user_id', Auth::user()->id)->paginate($per_page);
    }

    /*
     Retrieve all recipes that are shared to a user
     */
    public function sharedWithMe()
    {
        return $this->belongsToMany(Recipe::class, 'shared_recipes', 'user_shared_to', 'recipe_id');
    }

    /*
     Retrieve every collection that a certain user owns
     */
    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    /*
     Retrieve every comment that a certain user has written
     */

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /*
    Retrieve every review that a certain user has written
    */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /*
     Retrieve every user that a certain user follows
     */
    public function follow()
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'followed_user_id');
    }

    /*
     Retrieve every user that follows a certain user
     */
    public function followed()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_user_id', 'user_id');
    }

    /*
     Retrieve every like that a certain user has
     */
    public function likes()
    {
        return $this->belongsToMany(Recipe::class, 'likes', 'user_id', 'recipe_id');
    }


}
