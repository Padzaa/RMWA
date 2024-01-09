<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     * Get all users who communicated with $user
     */
    public static function existingChatsForUser($user)
    {
        return User::whereIn('id', self::getSendersAndReceiversIdsAssociatedWithUser($user))->get()->each(function ($chat) {
            static::configureChat($chat);
        })->sortByDesc('last_message.created_at')->values();
    }

    /**
     * Returns all senders and receivers ids associated with $user
     */
    public static function getSendersAndReceiversIdsAssociatedWithUser($user)
    {
        return self::where('sender_id', $user->id)
            ->select('receiver_id as id')
            ->distinct()
            ->union(
                self::where('receiver_id', $user->id)
                    ->select('sender_id as id')
                    ->distinct()
            )->get()->pluck('id');
    }

    /**
     * Takes chat as a parameter and sets last_message and messages
     * @param $chat
     * @return void
     */
    public static function configureChat($chat)
    {
        $chat->last_message = Message::messagesWithUser($chat)->with('sender', 'receiver')->latest()->first();
        $chat->messages = Message::messagesWithUser($chat)->with('sender', 'receiver')->get();
    }

    /**
     * Returns messages I have exchanged with a certain user($user)
     * @param $query
     * @param $user
     * @return mixed
     */
    public function scopeMessagesWithUser($query, $user)
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
