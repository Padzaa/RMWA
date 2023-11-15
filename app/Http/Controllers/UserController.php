<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Notifications\UserFollowed;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {
            $this->authorize('view', $user);
            if (Auth::user()->id === $user->id) {
                return redirect()->route('user.edit', $user->id);
            }
            Auth::user()->followsUser($user)->count() ? $is_following = true : $is_following = false;

            return Inertia::render('User/User_Show', [
                "user" => $user,
                "is_following" => $is_following,
            ]);
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return Inertia::location('/');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user, Request $request)
    {
        try {
            $this->authorize('update', $user);
            $recipes = $user->recipes();
            $recipes = $this->OrderAndPaginate($recipes, $request);
            return Inertia::render('User/User_Edit', [
                    "recipes" => $recipes,
                ]
            );
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return Inertia::location('/');
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $this->authorize('update', $user);
            if ($request->hasFile('file')) {
                if ($user->picture) {
                    Storage::disk("public")->delete(basename($user->picture));
                }
                $uploadedFile = $request->file('file');
                $filename = time() . '_' . $uploadedFile->getClientOriginalName();
                $uploadedFile->storeAs('public', $filename);
                $user->picture = "/storage/" . $filename;
            }
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->save();
            $this->flashSuccessMessage('User updated successfully.');
            return Inertia::location('/');
        } catch (Exception $e) {
            $this->flashErrorMessage($e->getMessage());
            return Inertia::location('/');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     *Follows/Unfollows a user
     */
    public function follow(User $user)
    {
        if(Auth::user()->followedByMe()->where('followed_user_id', $user->id)->count()){
            Auth::user()->followedByMe()->detach($user->id);
        }else{
            Auth::user()->followedByMe()->attach($user->id);
            Notification::send(User::find($user->id), new UserFollowed(Auth::user()));
        }
        return redirect()->back();
    }

}
