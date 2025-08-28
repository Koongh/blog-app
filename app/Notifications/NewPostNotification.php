<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPostNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function via(object $notifiable): array
    {
        return ['mail']; 
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Post from {$this->post->user->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->post->user->name} has created a new post: {$this->post->title}")
            ->action('View Post', url("/posts/{$this->post->id}"))
            ->line('Thank you for subscribing!');
    }
}
