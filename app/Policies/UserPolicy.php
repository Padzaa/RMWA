<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        //IF USER 1 SHARED A RECIPE TO A USER 2 THEN USER 2 CAN VIEW THE USER'S 1 PROFILE
        $shared = $model->recipes()->whereHas('shared', function ($query) use ($user) {
            $query->where('user_shared_to', $user->id);
        })->get();
        //IF USER 1 FOLLOWS A USER 2 THEN USER 2 CAN VIEW THE USER'S 1 PROFILE
        $follow = $user->followedByMe()->where('followed_user_id', $model->id)->get();
        //IF USER 1 HAS A PUBLIC RECIPE THEN USER 2 CAN VIEW THE USER'S 1 PROFILE
        $has_public = $model->recipes()->where('is_public', 1)->get();

        if ($follow->count() || $user->id === $model->id || $shared->count() || $has_public->count()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {

        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        //
    }
}
