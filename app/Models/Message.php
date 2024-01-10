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
    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User who received the message
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get configured chats for user
     */
    public static function getActiveChatsForUser($user)
    {
        return self::getChatsForUser($user)->each(function ($chat) {
            static::configureChat($chat);
        })->sortByDesc('last_message.created_at')->values();
    }

    /**
     * Returns all unique senders and receivers associated with $user
     */
    public static function getChatsForUser($user): \Illuminate\Support\Collection
    {
        $receivers = $user->sentMessages()->with('receiver')->get()->pluck('receiver');
        $senders = $user->receivedMessages()->with('sender')->get()->pluck('sender');

        return collect([...$receivers, ...$senders])->unique();
    }

    /**
     * Takes chat as a parameter and sets last_message and messages
     * @param $chat
     * @returns void
     */
    public static function configureChat($chat): void
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
    public function scopeMessagesWithUser($query, $user): mixed
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
