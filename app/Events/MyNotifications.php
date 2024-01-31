<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MyNotifications implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->data = ['notificationsOnLogin' => $user->unreadNotifications()->where('type', '!=', 'App\Notifications\TechnicalSupportRequest')->get()];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->user->id);
    }

    public function broadcastAs(): string
    {
        return 'my-notifications';
    }
}
