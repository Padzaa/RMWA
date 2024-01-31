<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Notifications\TechnicalSupportRequest;
use App\Notifications\UserFollowed;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use App\Models\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

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
        if (Auth::user()->id === $user->id) {
            return redirect()->route('user.edit', $user->id);
        }

        return Inertia::render('User/User_Show', [
            "user" => $user,
            "is_following" => Auth::user()->followsUser($user)->exists(),
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user, Request $request)
    {
        $this->authorize('update', $user);

        return Inertia::render('User/User_Edit', [
                "recipes" => $this->orderAndPaginate($user->recipes(), $request),
                "user" => $user,
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $this->flashSuccessMessage("User deleted successfully.");
        $user->delete();

        return Inertia::location(URL::previous());
    }

    /**
     *Follows/Unfollows a user
     */
    public function follow(User $user)
    {
        Auth::user()->followedByMe()->toggle($user->id);

        if (Auth::user()->followsUser($user)->exists()) {
            NotificationFacade::send($user, new UserFollowed(Auth::user()));
        }

        return redirect()->back();
    }

    /**
     * Updates the read_at field of the notifications for the authenticated user.
     */
    public function notifications($id = null)
    {
        if ($id) {
            Notification::where('id', $id)->update(['read_at' => Carbon::now()]);

            return redirect()->back();
        }
        Notification::where('notifiable_id', Auth::user()->id)->where('read_at', null)->update(['read_at' => Carbon::now()]);

        return Inertia::location(URL::previous());
    }

    /**
     * Mark TSRs as read
     */
    public function updateTSRs(Request $request)
    {
        $user = User::find($request->user_id);
        if ($request->tsr_status === 'accepted') {
            Notification::acceptTSRsFromUser($user);
        }
        if ($request->tsr_status === 'rejected') {
            Notification::rejectTSRsFromUser($user);
        }
        if ($request->tsr_status === 'terminated') {
            Notification::terminateTSRsFromUser($user);
        }
        if ($request->tsr_status === 'processed') {
            Notification::processTSRsFromUser($user);
        }
        Notification::skipTSRsFromUser($user);
    }

    /**
     * Requests a technical support from admins
     * If user has not already requested a technical support and doesn't have any accepted requests,
     * because only false(doesn't have pending requests) and false(doesn't have accepted requests) can with ! return true
     */
    public function requestTechnicalSupport(Request $request): void
    {
        if (!(Inertia::getShared('pendingRequests') || Inertia::getShared('acceptedRequests'))) {
            $user = User::find($request->user_id);
            NotificationFacade::send(User::admins()->get(), new TechnicalSupportRequest($user));
            Inertia::share('pendingRequests', true);
        }

    }

}
