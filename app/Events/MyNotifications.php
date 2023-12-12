<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class MyNotifications implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    /**
     * Create a new event instance.
     */
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
        $this->data = ['notificationsOnLogin' => User::findOrFail($user)->unreadNotifications()->get()];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->user);
    }

    public function broadcastAs(): string
    {
        return 'my-notifications';
    }
}
