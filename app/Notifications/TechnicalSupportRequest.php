<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TechnicalSupportRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('technical-support');
    }

    public function broadcastAs(): string
    {
        return 'technical-support-request';
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tsr_sender_id' => $this->user->id,
            'user' => $this->user,
        ];

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $notification = \App\Models\Notification::find($this->id);
        $notification->tsr_sender_id = $this->user->id;
        $notification->save();
        $notification->id = $this->id;
        return ['technicalSupport' => $notification];
    }
}
