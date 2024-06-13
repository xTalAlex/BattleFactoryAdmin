<?php

namespace App\Notifications;

use App\Models\Squad;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class SquadSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public Squad $squad;

    public $user;

    public $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Squad $squad)
    {
        $this->url = route('filament.admin.resources.squads.edit', $squad);
        $this->squad = $squad;
        $this->user = $squad->user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack', 'mail'];
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
            ->subject(__('New Squad'))
            ->greeting(__('Hello!'))
            ->line(__('A new Squad has been submitted!'))
            ->lineIf($this->user, __('From').' '.($this->user ? $this->user->email : ''))
            ->lineIf($this->squad->description, $this->squad->description)
            ->action(__('Edit Squad'), $this->url);
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        $url = $this->url;

        return (new SlackMessage)
            ->content(__('New Squad'))
            ->attachment(function ($attachment) use ($url) {
                $attachment->title(__('A new Squad has been submitted!'), $url)
                    ->fields([
                        __('Name') => $this->squad->name,
                    ])
                    ->content($this->squad->description);
            });
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
