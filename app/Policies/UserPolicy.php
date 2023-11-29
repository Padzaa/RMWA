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
        if($user->is_admin != "1"){
//IF USER 2 SHARED A RECIPE TO ME THEN I CAN VIEW THE USER'S 2 PROFILE
            $shared = boolval($model->recipes()->whereHas('shared', function ($query) use ($user) {
                $query->where('user_shared_to', $user->id);
            })->get());
            //IF I FOLLOW A USER 2 THEN USER 2 CAN VIEW MY PROFILE
            $follow = boolval($user->followsUser($model)->get());
            //IF USER 2 HAS A PUBLIC RECIPE THEN I CAN VIEW THE USER'S 2 PROFILE
            $has_public = boolval($model->recipes()->Public()->get());

            if ($follow || $user->id === $model->id || $shared || $has_public) {
                return true;
            } else {
                return false;
            }
        }
        return true;
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
        if($user->is_admin != "1") {
            return $user->id === $model->id;
        }
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->is_admin === "1" && $model->is_admin === "0" && $user->id != $model->id;
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
