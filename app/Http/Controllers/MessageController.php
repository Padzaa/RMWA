<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inboxes = Message::existingChatsForUser(Auth::user());
        return Inertia::render('User/Messages', [
            'title' => 'Messages',
            'inboxes' => $inboxes,
            'users' => User::all()->except(array_merge([Auth::user()->id], $inboxes->pluck('id')->toArray())),
        ]);
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
    public function store(StoreMessageRequest $request)
    {

        $path = '';
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $filename = time() . '_' . str_replace(' ', '_', $uploadedFile->getClientOriginalName());
            $uploadedFile->storeAs('public', $filename);
            $path = "/storage/" . $filename;
        }

        $message = [
            'sender_id' => Auth::user()->id,
            'receiver_id' => $request->receiver_id,
            'content' => $path != '' ? $path : $request->msg_content,
            'created_at' => now(),
        ];

        Message::create($message);
        event(new SendMessage($message));
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }
}
