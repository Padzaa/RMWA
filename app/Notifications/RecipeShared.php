<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecipeShared extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $message;
    public $recipe;
    public $notifiable;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $recipe)
    {
        $this->user = $user;
        $this->recipe = $recipe;
        $this->message = "Recipe {$recipe} shared by {$user->firstname} {$user->lastname}";
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

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->notifiable);
    }

    public function broadcastAs(): string
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
        return ['recipeShared' => \App\Models\Notification::where('notifiable_id', $this->notifiable)->where('type', 'App\Notifications\RecipeShared')->where('read_at', null)->get()->toArray()];
    }
}
