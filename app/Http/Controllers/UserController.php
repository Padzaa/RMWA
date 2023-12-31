<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Jobs\SendSMS;
use App\Mail\TestingMail;
use App\Models\Recipe;
use App\Models\User;
use App\Notifications\UserFollowed;
use App\Services\TwilioService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use function Illuminate\Events\queueable;

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
            Auth::user()->follow()->where('followed_user_id', $user->id)->count() ? $is_following = true : $is_following = false;

            return Inertia::render('User/User_Show', [
                "user" => $user,
                "is_following" => $is_following,
            ]);
        } catch (Exception $e) {
            session()->flash("alert", [
                "title" => "Error!",
                "message" => $e->getMessage(),
                "type" => "error"
            ]);
            return Inertia::location('/');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        try {
            $this->authorize('update', $user);
            $recipes = $user->recipes()->paginate(10);
            return Inertia::render('User/User_Edit', [
                    "recipes" => $recipes,
                ]
            );
        } catch (Exception $e) {
            session()->flash("alert", [
                "title" => "Error!",
                "message" => $e->getMessage(),
                "type" => "error"
            ]);
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
                $path = $uploadedFile->storeAs('public', $filename);
                $user->picture = "/storage/" . $filename;
            }

            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->save();
            session()->flash("alert", [
                "title" => "Success!",
                "message" => "Your profile has been updated!",
                "type" => "success"
            ]);
            return Inertia::location('/');
        } catch (Exception $e) {
            session()->flash("alert", [
                "title" => "Error!",
                "message" => $e->getMessage(),
                "type" => "error"
            ]);
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

//Follow/Unfollow a certain user
    public function follow(Request $request, User $user)
    {
        if(Auth::user()->follow()->where('followed_user_id', $user->id)->count()){
            Auth::user()->follow()->detach($user->id);
        }else{
            Auth::user()->follow()->attach($user->id);
            Notification::send(User::find($user->id), new UserFollowed(Auth::user()));
        }
        return redirect()->back();
    }

}
