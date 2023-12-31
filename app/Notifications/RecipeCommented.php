<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecipeCommented extends Notification implements ShouldQueue
{
    use Queueable;
    public string $message;
    public string $recipeTitle;
    public $user;
    public $notifiable;

    /**
     * Create a new notification instance.
     */
    public function __construct ($recipeTitle,$user)
{
    $this->user = $user;

    $this->recipeTitle = $recipeTitle;
    $this->message = "User \"{$this->user->firstname} {$this->user->lastname}\" commented on recipe \"{$this->recipeTitle}\".";
}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
{
    $this->notifiable = $notifiable->id;
    return ['database', 'broadcast'];
}

    /**
     * Returns the channel names the event should broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.'.$this->notifiable);
    }
    /**
     * Retrieves the name of the event that should be broadcasted.
     *
     * @return string The name of the event.
     */
    public function broadcastAs()
    {
        return 'my-notifications';
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
{
    return (new MailMessage)
        ->line('The introduction to the notification.')
        ->action('Notification Action', url('/'))
        ->line('Thank you for using our application!');
}

    public function toDatabase(object $notifiable): array
{
    return [
        'message' => $this->message,
        'user_id' => $this->user->id,
    ];
}

    public function toArray(): array
{
    return ['recipeCommented' => \App\Models\Notification::where('notifiable_id', $this->notifiable)->where('type', 'App\Notifications\RecipeCommented')->where('read_at', null)->latest()->first()->toArray()];
}
}
