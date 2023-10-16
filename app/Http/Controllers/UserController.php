<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
        $this->authorize('view', $user);
        Auth::user()->follow()->where('followed_user_id', $user->id)->count() ? $is_following = true : $is_following = false;

        return Inertia::render('User/User_Show', [
            "user" => $user,
            "is_following" => $is_following,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {

        $this->authorize('update', $user);
        $recipes = $user->recipes()
            ->orWhereHas('shared', function ($query) use ($user) {
                $query->where('shared_recipes.user_shared_to', $user->id);
            })->paginate(10);
        return Inertia::render('User/User_Edit', [
                "recipes" => $recipes,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        if ($request->hasFile('file')) {
            if($user->picture) {
                Storage::disk("public")->delete(basename($user->picture));
            }
            $uploadedFile = $request->file('file');
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();
            $path = $uploadedFile->storeAs('public', $filename);
            $user->picture = "/storage/" . $filename;
        }

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->save();
        return Inertia::location('/');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

//Follow/Unfollow a certain user
    public function follow(Request $request, User $user)
    {
        Auth::user()->follow()->where('followed_user_id', $user->id)->count() ?
            Auth::user()->follow()->detach($user->id)
            :
            Auth::user()->follow()->attach($user->id);

        return redirect()->back();
    }
}
