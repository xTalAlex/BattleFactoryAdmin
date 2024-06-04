<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Routing\Route;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $password;
    public $url;
    /**
     * Create a new notification instance.
     *
     * @param password shows human readable password [not used]
     * 
     * @return void
     */
    public function __construct(string $password = null)
    {
        $this->password = $password;
        //@todo replace with password configuration url
        $this->url = Route::has('password.request') ? route('password.request') : config('uniteagency.url');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Account Created'))
            ->markdown('mail.user.created', [
                'url' => $this->url,
                'username' => $notifiable->name,
                'password' => $this->password,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
