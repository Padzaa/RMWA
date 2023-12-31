<?php

namespace App\Notifications;

use App\Events\MyNotifications;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PublicRecipeCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public string $recipeTitle;
    public $user;
    public string $message;
    public $notifiable;
    /**
     * Create a new notification instance.
     */
    public function __construct($recipeTitle,$user)
    {
        $this->recipeTitle = $recipeTitle;
        $this->user = $user;
        $this->message = "Recipe \"{$this->recipeTitle}\" has been created by {$user->firstname} {$user->lastname}";

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
    /**
     * Converts the given object to an array representation suitable for database storage.
     *
     * @param object $notifiable The object to be converted.
     * @return array The array representation of the object.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'user_id' => $this->user->id,
        ];
    }

    /**
     * Converts the object to an array.
     *
     * @return array The resulting array.
     */
    public function toArray(): array{
        return ['publicRecipeCreated' =>\App\Models\Notification::where('notifiable_id',$this->notifiable)->where('type','App\Notifications\PublicRecipeCreated')->where('read_at',null)->latest()->first()->toArray()];
    }
}
