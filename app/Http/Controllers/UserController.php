<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if(Auth::user()->follow()->where('followed_user_id', $user->id)->count() > 0){
            $is_following = true;
        }else{
            $is_following = false;
        }
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
        $recipes = $user->recipes()
            ->orWhereHas('shared', function ($query) use ($user) {
                $query->where('shared_recipes.user_shared_to', $user->id);
            })->get();
        return Inertia::render('User/User_Edit',[
            "recipes" => $recipes
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $validator = $request->all();

        $validator = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(auth()->id())],

        ],
            [
                'firstname.required' => 'First name is required!',
                'lastname.required' => 'Last name is required!',
                'email.required' => 'Email is required!',
                'email.unique' => 'Email already exists!',

            ]);


        $user = User::findOrFail($id);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();
            $path = $uploadedFile->storeAs('public', $filename);
            $user->picture = "/storage/".$filename;
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


    public function follow(Request $request,$id){
        $user = User::findOrFail($id);
        if(Auth::user()->follow()->where('followed_user_id', $user->id)->count() > 0){
            Auth::user()->follow()->detach($user->id);
        }else{
            Auth::user()->follow()->attach($user->id);

        }

        return redirect()->back();
    }
}
