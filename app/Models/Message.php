<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id', 'receiver_id', 'content'
    ];

    /**
     * User who sent the message
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User who received the message
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All records of messages that are associated with user(either sent or received)
     */
    public function scopeDifferentiateInboxes($query)
    {
        return $query->where('sender_id', Auth::user()->id)
            ->orWhere('receiver_id', Auth::user()->id)
            ->select('sender_id', 'receiver_id')
            ->distinct();
    }

    /**
     * Returns all users ids that I communicated with(sent or received message)
     * @return array
     */
    public static function inboxes()
    {
        return array_merge(self::differentiateInboxes()->where('sender_id', '!=', Auth::user()->id)->pluck('sender_id')->toArray(),
            self::differentiateInboxes()->get()->where('receiver_id', '!=', Auth::user()->id)->pluck('receiver_id')->toArray());

    }

    /**
     * Returns messages I have exchanged with a certain user($user)
     * @param $query
     * @param $user
     * @return mixed
     */
    public function scopeForUser($query, $user)
    {
        return $query->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })->where(function ($query) use ($user) {
            $query->where('sender_id', '=', Auth::user()->id)
                ->orWhere('receiver_id', '=', Auth::user()->id);
        });
    }
}
